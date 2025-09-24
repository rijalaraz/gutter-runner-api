<?php

namespace App\Http\Controllers\Demande;

use App\Models\Demande\Demande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\DemandeCollection;
use App\Http\Resources\DemandeResource;
use App\Models\Address;
use App\Models\Client\Client;
use App\Models\Client\ClientCellulaire;
use App\Models\Client\ClientCompany;
use App\Models\Client\ClientCourriel;
use App\Models\Client\ClientSequence;
use App\Models\Demande\DemandeClientAppointment;
use App\Models\Demande\DemandeSequence;
use App\Models\Demande\DemandeService;
use App\Models\Demande\DemandeSource;
use App\Models\Demande\DemandeTag;
use App\Models\DocumentActivity;
use App\Models\Note;
use App\Models\Photo;
use Illuminate\Support\Str;
use App\Support\UploadBase64Trait;
use App\Transformers\Demande\DemandeTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DemandeController extends Controller
{
    use UploadBase64Trait;

    public function __construct(Demande $model)
    {
        $this->model = $model;
        $this->middleware('permission:Ajouter')->only('store');
        $this->middleware('permission:Modification')->only('update');
        $this->middleware('permission:Lecture seule', ['only' => ['index','show']]);
    }

    /**
     * @OA\Get(path="/api/demandes",
     *   tags={"Demandes"},
     *   summary="Liste des demandes",
     *   description="Liste des demandes",
     *   operationId="demandesList",
     *   @OA\Parameter(
     *      description="Statut",
     *      in="query",
     *      name="statut",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="Soumissionnée"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="urgent",
     *      required=false,
     *      in="query",
     *      @OA\Schema(
     *          type="integer",
     *          example=1
     *      ),
     *      description="Urgent (1 ou 0)",
     *   ),
     *   @OA\Parameter(
     *      name="assigned_to",
     *      required=false,
     *      in="query",
     *      @OA\Schema(
     *          type="integer",
     *          example=2
     *      ),
     *      description="Assignée à",
     *   ),
     *   @OA\Parameter(
     *      name="demande_source_id",
     *      required=false,
     *      in="query",
     *      @OA\Schema(
     *          type="integer",
     *          example=4
     *      ),
     *      description="Source",
     *   ),
     *   @OA\Parameter(
     *      name="demande_service_id[]",
     *      required=false,
     *      in="query",
     *      @OA\Schema(
     *          type="array",
     *          @OA\Items(type="integer", format="int32"),
     *          example={1, 3}
     *      ),
     *      description="Service",
     *   ),
     *   @OA\Parameter(
     *      name="reception_date",
     *      required=false,
     *      in="query",
     *      @OA\Schema(
     *          type="string",
     *          example="15/08/2025-10/09/2025"
     *      ),
     *      description="Intervalle de dates",
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="Liste obtenue",
     *      content={
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      type="object",
     *                      property="data"
     *                  )
     *              )
     *          )
     *      }
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Utilisateur non connecté",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="L'utilisateur n'est même pas connecté",
     *                         example="Vous ne vous êtes pas encore authentifié."
     *                     )
     *                 )
     *             )
     *         }
     *   )
     * )
     *
     * Display a listing of the resource.
     *
     * @return DemandeCollection
     */
    public function index(Request $request)
    {
        $query = $this->model->query();

        $query->when($request->has('statut'), function ($q) {
            return $q->where('statut', request('statut'));
        });

        $query->when($request->has('urgent'), function ($q) {
            return $q->where('urgent', request('urgent'));
        });

        $query->when($request->has('assigned_to'), function ($q) {
            return $q->where('assigned_to', request('assigned_to'));
        });

        $query->when($request->has('demande_source_id'), function ($q) {
            return $q->where('demande_source_id', request('demande_source_id'));
        });

        $query->when($request->has('demande_service_id'), function ($q) {
            return $q->whereHas('services', function($s)
            {
                $integerIDs = array_map( function($value) { return (int)$value; }, request('demande_service_id') );
                $s->whereIn('demande_service_id', $integerIDs);
            });
        });

        $query->when($request->has('reception_date'), function ($q) {

            [$min_date, $max_date] = explode('-', request('reception_date'));
            $minDate = Carbon::createFromFormat('d/m/Y H:i:s', sprintf('%s 00:00:00', $min_date));
            $maxDate = Carbon::createFromFormat('d/m/Y H:i:s', sprintf('%s 00:00:00', $max_date));
            return $q->whereBetween('reception_date', [$minDate , $maxDate]);

            /*
            switch (request('reception_date')) {
                case 'today': // Aujourd'hui
                    return $q->whereDate('reception_date', Carbon::today());
                    break;

                case 'yesterday': // Hier
                    return $q->whereDate('reception_date', Carbon::yesterday());
                    break;

                case 'this_week': // Cette semaine
                    return $q->whereBetween('reception_date', [Carbon::today()->startOfWeek(), Carbon::today()->endOfWeek()]);
                    break;

                case 'the_last_seven_days': // Les sept derniers jours
                    return $q->whereBetween('reception_date', [Carbon::today()->subdays(6) , Carbon::today()]);
                    break;

                case 'last_week': // La semaine dernière
                    return $q->whereBetween('reception_date', [Carbon::today()->subWeek()->startOfWeek() , Carbon::today()->subWeek()->endOfWeek()]);
                    break;

                case 'this_month': // Ce mois-ci
                    return $q->whereMonth('reception_date', Carbon::today()->format('m'))
                        ->whereYear('reception_date', Carbon::today()->format('Y'));
                    break;

                case 'last_month': // Le mois dernier
                    return $q->whereMonth('reception_date', Carbon::today()->subMonth()->format('m'))
                        ->whereYear('reception_date', Carbon::today()->format('Y'));
                    break;

                case 'the_last_thirty_days': // Les 30 derniers jours
                    return $q->whereBetween('reception_date', [Carbon::today()->subdays(29) , Carbon::today()]);
                    break;

                case 'this_year': // Cette année
                    return $q->whereYear('reception_date', Carbon::today()->format('Y'));
                    break;

                case 'last_year': // L'année dernière
                    return $q->whereYear('reception_date', Carbon::today()->subYear()->format('Y'));
                    break;

                case 'last_year_on_the_same_date': // L'année dernière à la même date
                    return $q->whereYear('reception_date', Carbon::today()->subYear()->format('Y'))
                        ->whereMonth('reception_date', Carbon::today()->format('m'))
                        ->whereDay('reception_date', Carbon::today()->format('d'));
                    break;

                case 'for_life': // A vie
                    return $q->whereBetween('reception_date', [auth()->user()->created_at, Carbon::today()]);
                    break;

                default:
                    [$min_date, $max_date] = explode('-', request('reception_date'));
                    $minDate = Carbon::createFromFormat('d/m/Y H:i:s', sprintf('%s 00:00:00', $min_date));
                    $maxDate = Carbon::createFromFormat('d/m/Y H:i:s', sprintf('%s 00:00:00', $max_date));
                    return $q->whereBetween('reception_date', [$minDate , $maxDate]);
                    break;
            }
            */
        });

        $paginator = $query->advancedFilter();

        if ($request->has('limit')) {
            $paginator->appends('limit', $request->get('limit'));
        }

        $delayInDays = (float) Demande::select([DB::raw("AVG(DATEDIFF(statut_change_date, reception_date)) AS delay")])
            ->where('statut', Demande::STATUT_SOUMISSIONNEE)
            ->pluck('delay')
            ->toArray()[0];

        // Temps moyen écoulé pour soumissionner
        $average_elapse_time = Demande::secondsToHumanReadable($delayInDays * 24 * 3600, 3);

        return (new DemandeCollection($paginator))->additional([
            'count' => [
                'nb_brouillon' => Demande::where('statut', Demande::STATUT_BROUILLON)->count(),
                'nb_assignee' => Demande::where('statut', Demande::STATUT_ASSIGNEE)->count(),
                'nb_rv_planifie' => Demande::where('statut', Demande::STATUT_PLANIFIEE)->count(),
                'nb_soumissionnee' => Demande::where('statut', Demande::STATUT_SOUMISSIONNEE)->count(),
                'nb_annulee' => Demande::where('statut', Demande::STATUT_ANNULEE)->count(),
                'nb_perdue' => Demande::where('statut', Demande::STATUT_PERDUE)->count(),
                'nb_actives' => Demande::where('statut', Demande::STATUT_BROUILLON)
                    ->orWhere('statut', Demande::STATUT_ASSIGNEE)
                    ->orWhere('statut', Demande::STATUT_PLANIFIEE)
                    ->count(),
                'average_elapse_time' => $average_elapse_time,
            ],
        ]);
        // return fractal($paginator, new DemandeTransformer())->respond();
    }

    /**
     * @OA\Get(path="/api/demande_statuts",
     *   tags={"Demandes"},
     *   summary="Liste des statuts de demandes",
     *   description="Liste des statuts de demandes",
     *   operationId="demandeStatutList",
     *   @OA\Response(
     *      response=200,
     *      description="Liste obtenue",
     *      content={
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="data",
     *                      type="array",
     *                      description="Statuts des demandes",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                              description="Nom du statut",
     *                              example="Soumissionnée"
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *      }
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Utilisateur non connecté",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="L'utilisateur n'est même pas connecté",
     *                         example="Vous ne vous êtes pas encore authentifié."
     *                     )
     *                 )
     *             )
     *         }
     *   )
     * )
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statutList()
    {
        $statuts = [];
        foreach (Demande::STATUTES as $value) {
            $statuts[] = [
                'name' => $value,
            ];
        }

        return response()->json([
            'data' => $statuts
        ]);
    }

    /**
     * @OA\Post(path="/api/demandes",
     *   tags={"Demandes"},
     *   summary="Création d'une demande",
     *   description="Création d'une demande",
     *   operationId="demandeCreation",
     *   @OA\RequestBody(
     *       required=true,
     *       description="Paramètres de création d'une demande",
     *       @OA\JsonContent(
     *          type="object",
     *          required={"numero", "urgent", "source", "services", "plus_de_details", "confirmation_email"},
     *          @OA\Property(
     *              property="numero",
     *              type="string",
     *              description="Numéro de la demande à 6 chiffres",
     *              example="010000"
     *          ),
     *          @OA\Property(
     *              property="assigned_to",
     *              type="integer",
     *              description="L'utilisateur à qui on assigne la demande",
     *              example=2
     *          ),
     *          @OA\Property(
     *              property="urgent",
     *              type="boolean",
     *              description="L'urgence de la demande",
     *              example=true
     *          ),
     *          @OA\Property(
     *              property="source",
     *              type="string",
     *              description="Source de la demande",
     *              example="Réseaux sociaux"
     *          ),
     *          @OA\Property(
     *              property="services",
     *              type="array",
     *              description="Les services de la demande",
     *              @OA\Items(
     *                  type="string",
     *                  example="Réparation"
     *              )
     *          ),
     *          @OA\Property(
     *              property="plus_de_details",
     *              type="string",
     *              description="Plus de détails",
     *              example="Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l'imprimerie depuis les années 1500"
     *          ),
     *          @OA\Property(
     *              property="appointment_required",
     *              type="boolean",
     *              description="Rendez-vous requis",
     *              example=true
     *          ),
     *          @OA\Property(
     *              property="client_appointment",
     *              type="object",
     *              description="Rendez-vous avec le client",
     *              @OA\Property(
     *                  property="appointment_date",
     *                  type="string",
     *                  description="Date du rendez-vous",
     *                  example="2021-06-20T08:30:20.929Z"
     *              ),
     *              @OA\Property(
     *                  property="demande_client_availability_id",
     *                  type="array",
     *                  description="Disponibilités clients",
     *                  @OA\Items(
     *                      type="integer",
     *                      example=3
     *                  )
     *              )
     *          ),
     *          @OA\Property(
     *              property="tags",
     *              type="array",
     *              description="Les étiquettes de la demande",
     *              @OA\Items(
     *                  type="string",
     *                  example="Gouttières"
     *              )
     *          ),
     *          @OA\Property(
     *              property="confirmation_email",
     *              type="boolean",
     *              description="Email de confirmation",
     *              example=true
     *          ),
     *          @OA\Property(
     *              property="demande_product_presentation_id",
     *              type="array",
     *              description="Présentation des produits",
     *              @OA\Items(
     *                  type="integer",
     *                  example=3
     *              )
     *          ),
     *          @OA\Property(
     *              property="existing_client",
     *              type="object",
     *              description="Dans la cas où on sélectionne un client existant",
     *              @OA\Property(
     *                  property="uuid",
     *                  type="string",
     *                  description="L'uuid du client",
     *                  example="CU-010000"
     *              ),
     *              @OA\Property(
     *                  property="existing_service_address",
     *                  type="object",
     *                  description="Le cas où on sélectionne une adresse de service du client",
     *                  @OA\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="Identifiant de l'adresse de service",
     *                      example=2
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="new_service_address",
     *                  type="object",
     *                  description="Le cas où on crée une nouvelle adresse de service",
     *                  @OA\Property(
     *                      property="street",
     *                      type="string",
     *                      description="Rue",
     *                      example="3497 Goyeau Ave"
     *                  ),
     *                  @OA\Property(
     *                      property="city",
     *                      type="string",
     *                      description="Ville",
     *                      example="Windsor"
     *                  ),
     *                  @OA\Property(
     *                      property="province",
     *                      type="string",
     *                      description="Province",
     *                      example="Ontario"
     *                  ),
     *                  @OA\Property(
     *                      property="zipcode",
     *                      type="string",
     *                      description="Code postal",
     *                      example="N9A 1H9"
     *                  ),
     *                  @OA\Property(
     *                      property="country",
     *                      type="string",
     *                      description="Pays",
     *                      example="Canada"
     *                  ),
     *                  @OA\Property(
     *                      property="postal_address",
     *                      type="string",
     *                      description="Adresse postale",
     *                      example="2566 Windsor Street, Toronto, Etobicoke, ON, Canada"
     *                  )
     *              )
     *          ),
     *          @OA\Property(
     *              property="new_client",
     *              type="object",
     *              description="Dans la cas où on crée un nouveau client",
     *              @OA\Property(
     *                  property="client_titre_id",
     *                  type="integer",
     *                  description="M. ou Mme.",
     *                  example=1
     *              ),
     *              @OA\Property(
     *                  property="prenom",
     *                  type="string",
     *                  description="Prénom",
     *                  example="Murielle"
     *              ),
     *              @OA\Property(
     *                  property="nom",
     *                  type="string",
     *                  description="Nom",
     *                  example="Todd"
     *              ),
     *              @OA\Property(
     *                  property="company",
     *                  type="string",
     *                  description="Compagnie",
     *                  example="Blanche"
     *              ),
     *              @OA\Property(
     *                  property="cellulaire",
     *                  type="string",
     *                  description="Cellulaire",
     *                  example="604-221-1023"
     *              ),
     *              @OA\Property(
     *                  property="courriel",
     *                  type="string",
     *                  description="Courriel",
     *                  example="muriel.todd@blanche.ca"
     *              ),
     *              @OA\Property(
     *                  property="billing_address",
     *                  type="object",
     *                  description="L'adresse de facturation du nouveau client",
     *                  @OA\Property(
     *                      property="street",
     *                      type="string",
     *                      description="Rue",
     *                      example="2067 Bay Street"
     *                  ),
     *                  @OA\Property(
     *                      property="city",
     *                      type="string",
     *                      description="Ville",
     *                      example="Toronto"
     *                  ),
     *                  @OA\Property(
     *                      property="province",
     *                      type="string",
     *                      description="Province",
     *                      example="Ontario"
     *                  ),
     *                  @OA\Property(
     *                      property="zipcode",
     *                      type="string",
     *                      description="Code postal",
     *                      example="M5J 2R8"
     *                  ),
     *                  @OA\Property(
     *                      property="country",
     *                      type="string",
     *                      description="Pays",
     *                      example="Canada"
     *                  ),
     *                  @OA\Property(
     *                      property="postal_address",
     *                      type="string",
     *                      description="Adresse postale",
     *                      example="2455 West Toronto Street, Toronto, ON, Canada"
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="same_as_billing_address",
     *                  type="boolean",
     *                  description="Identique à l'adresse de facturation",
     *                  example=true
     *              ),
     *              @OA\Property(
     *                  property="service_address",
     *                  type="object",
     *                  description="Différente de l'adresse de facturation",
     *                  @OA\Property(
     *                      property="street",
     *                      type="string",
     *                      description="Rue",
     *                      example="3497 Goyeau Ave"
     *                  ),
     *                  @OA\Property(
     *                      property="city",
     *                      type="string",
     *                      description="Ville",
     *                      example="Windsor"
     *                  ),
     *                  @OA\Property(
     *                      property="province",
     *                      type="string",
     *                      description="Province",
     *                      example="Ontario"
     *                  ),
     *                  @OA\Property(
     *                      property="zipcode",
     *                      type="string",
     *                      description="Code postal",
     *                      example="N9A 1H9"
     *                  ),
     *                  @OA\Property(
     *                      property="country",
     *                      type="string",
     *                      description="Pays",
     *                      example="Canada"
     *                  ),
     *                  @OA\Property(
     *                      property="postal_address",
     *                      type="string",
     *                      description="Adresse postale",
     *                      example="2566 Windsor Street, Toronto, Etobicoke, ON, Canada"
     *                  )
     *              )
     *          ),
     *          @OA\Property(
     *              property="notes_internes",
     *              type="array",
     *              description="Pièces jointes et notes internes",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(
     *                      property="report_to_soumission",
     *                      type="boolean",
     *                      example=true
     *                  ),
     *                  @OA\Property(
     *                      property="report_to_contrat",
     *                      type="boolean",
     *                      example=true
     *                  ),
     *                  @OA\Property(
     *                      property="report_to_bon_de_travail",
     *                      type="boolean",
     *                      example=false
     *                  ),
     *                  @OA\Property(
     *                      property="report_to_facture",
     *                      type="boolean",
     *                      example=true
     *                  ),
     *                  @OA\Property(
     *                      property="note",
     *                      type="string",
     *                      description="Note interne",
     *                      example="Contrairement à une opinion répandue, le Lorem Ipsum n'est pas simplement du texte aléatoire"
     *                  ),
     *                  @OA\Property(
     *                      property="attachements",
     *                      type="array",
     *                      description="Pièces jointes",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(
     *                              property="file_name",
     *                              type="string",
     *                              description="Nom du fichier",
     *                              example="Ny fiainana araka ny Fanahy.docx"
     *                          ),
     *                          @OA\Property(
     *                              property="url",
     *                              type="string",
     *                              description="Chaîne base64 du fichier",
     *                              example="UEsDBBQACAgIAKp+dVIAAAAAAAAAAAAAAAARAAAAZG9jUHJvcHMvY29yZS54bWx9Ul1PwjAUffdXLH3f2m6A0IyRqOFJEqMQjW+1u0Bx65q2fP17u8EmKvHt3nNOz/1qOjm..."
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *       )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Création réussie d'une demande",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Message de confirmation",
     *                         example="Une demande a été créée avec succès."
     *                     )
     *                 )
     *             )
     *         }
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Paramètres vides ou invalides",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                          property="errors",
     *                          type="object",
     *                          @OA\Property(
     *                              property="numero",
     *                              type="array",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="La valeur du champ numero est déjà utilisée."
     *                              )
     *                          ),
     *                          @OA\Property(
     *                              property="notes_internes.0.attachements.0.url",
     *                              type="array",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="Le notes_internes.0.attachements.0.url doit être une chaîne Base64 valide."
     *                              )
     *                         )
     *                     )
     *                 )
     *             )
     *        }
     *   )
     * )
     *
     * Handle a demande creation request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse|Response
     */
    public function store(Request $request)
    {
        $validator = $this->validator($request->all());

        if($validator->fails()) {
            return response(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $demande = $this->createDemande($request->all());
        if($demande) {
            $lastSequence = DemandeSequence::latest()->first();
            if($lastSequence) {
                $lastNumero = $lastSequence->numero;
            } else {
                $lastNumero = '010000';
            }
            if($demande->numero == $lastNumero) {
                $newLastNumero = Str::padLeft($lastNumero + 1, 6, $pad = '0');
                DemandeSequence::create([
                    'numero' => $newLastNumero,
                ]);
            }

            if ($demande->confirmation_email) {
                $client = $demande->client()->first();
                $client->sendEmailDemandeCreationNotification($demande);
            }
        }

        /*
        $demande = Demande::byUuid('RQ-010000')->first();

        $client = $demande->client()->first();
        $client->sendEmailDemandeCreationNotification($demande);
        */

        return response()->json([
            'message' => trans('demande.created'),
            'demande' => $demande,
        ]);
    }

    protected function validatorAct(array $data)
    {
        return Validator::make($data, [
            'action' => 'required|max:255',
            'demande_uuids' => 'required',
            'demande_uuids.*' => 'required|max:255',
        ]);
    }

    protected function validator(array $data, Demande $demande = null)
    {
        if($demande) {
            // $uniqueNumeroSaisiRule = Rule::unique('demandes')->ignore($demande->id);
            // $clientEmails = $client->courriels()->pluck('email')->toArray();
            // $uniqueEmailRule = Rule::unique('client_courriels')->whereNotIn('email', $clientEmails);
        } else {
            $uniqueNumeroSaisiRule = Rule::unique('demandes');
            $uniqueEmailRule = Rule::unique('client_courriels', 'email');
        }

        return Validator::make($data, [
            'numero' => !$demande ? ['required', "digits:6", $uniqueNumeroSaisiRule] : [],
            'assigned_to' => 'nullable|integer',
            'urgent' => 'required|boolean',
            'source' => 'required|max:255',
            'services' => 'required',
            'services.*' => 'required|max:255',
            'plus_de_details' => 'nullable|min:3|max:1000',
            'appointment_required' => 'required|boolean',
            'client_appointment' => 'required_if:appointment_required,true',
            'client_appointment.appointment_date' => 'required_if:appointment_required,true|date',
            'tags' => 'sometimes',
            'tags.*' => 'sometimes|max:255',
            'confirmation_email' => 'required|boolean',
            'demande_product_presentation_id' => 'nullable',
            'demande_product_presentation_id.*' => 'nullable|integer',
            'existing_client' => 'required_without:new_client|empty_with:new_client',
            'existing_client.client_uuid' => 'sometimes|max:255',
            'existing_client.existing_service_address' => 'required_without:existing_client.new_service_address|empty_with:existing_client.new_service_address',
            'existing_client.existing_service_address.id' => 'sometimes|integer',
            'existing_client.new_service_address' => 'required_without:existing_client.existing_service_address|empty_with:existing_client.existing_service_address',
            'existing_client.new_service_address.street' => 'sometimes|max:255',
            'existing_client.new_service_address.city' => 'sometimes|max:255',
            'existing_client.new_service_address.province' => 'sometimes|max:255',
            'existing_client.new_service_address.zipcode' => 'sometimes|max:255',
            'existing_client.new_service_address.country' => 'sometimes|max:255',
            'existing_client.new_service_address.postal_address' => 'sometimes|min:3|max:1000',
            'new_client' => 'required_without:existing_client|empty_with:existing_client',
            'new_client.client_titre_id' => 'sometimes|integer',
            'new_client.prenom' => 'sometimes|max:255',
            'new_client.nom' => 'sometimes|max:255',
            'new_client.company' => 'sometimes|nullable|max:255',
            'new_client.cellulaire' => 'sometimes|max:255',
            "new_client.courriel" => ['sometimes', 'email:rfc,dns', 'max:255', $uniqueEmailRule],
            'new_client.billing_address' => 'sometimes',
            'new_client.billing_address.street' => 'sometimes|max:255',
            'new_client.billing_address.city' => 'sometimes|max:255',
            'new_client.billing_address.province' => 'sometimes|max:255',
            'new_client.billing_address.zipcode' => 'sometimes|max:255',
            'new_client.billing_address.country' => 'sometimes|max:255',
            'new_client.billing_address.postal_address' => 'sometimes|min:3|max:1000',
            'new_client.same_as_billing_address' => 'sometimes|boolean',
            'new_client.service_address' => 'required_if:new_client.same_as_billing_address,false',
            'new_client.service_address.street' => 'sometimes|max:255',
            'new_client.service_address.city' => 'sometimes|max:255',
            'new_client.service_address.province' => 'sometimes|max:255',
            'new_client.service_address.zipcode' => 'sometimes|max:255',
            'new_client.service_address.country' => 'sometimes|max:255',
            'new_client.service_address.postal_address' => 'sometimes|min:3|max:1000',
            'notes_internes' => 'sometimes|nullable',
            'notes_internes.*.report_to_soumission' => 'sometimes|boolean',
            'notes_internes.*.report_to_contrat' => 'sometimes|boolean',
            'notes_internes.*.report_to_bon_de_travail' => 'sometimes|boolean',
            'notes_internes.*.report_to_facture' => 'sometimes|boolean',
            'notes_internes.*.note' => 'sometimes|min:3|max:1000',
            'notes_internes.*.attachements' => 'sometimes',
            'notes_internes.*.attachements.*.file_name' => 'sometimes|max:255',
            'notes_internes.*.attachements.*.url' => 'sometimes|base64',
        ], [
            'existing_client.empty_with' => trans('validation.empty_with', [
                'values' => 'new_client',
            ]),
            'new_client.empty_with' => trans('validation.empty_with', [
                'values' => 'existing_client',
            ]),
            'existing_client.existing_service_address.empty_with' => trans('validation.empty_with', [
                'values' => 'existing_client.new_service_address'
            ]),
            'existing_client.new_service_address.empty_with' => trans('validation.empty_with', [
                'values' => 'existing_client.existing_service_address'
            ]),
        ]);
    }

    private function getNextNumero()
    {
        $lastSequence = DemandeSequence::latest()->first();
        if($lastSequence) {
            $lastNumero = $lastSequence->numero;
        } else {
            $lastNumero = '010000';
        }
        $res = Demande::where('numero', $lastNumero)->first();
        if($res) {
            $newLastNumero = Str::padLeft($lastNumero + 1, 6, $pad = '0');
            DemandeSequence::create([
                'numero' => $newLastNumero,
            ]);
            return $this->getNextNumero();
        } else {
            return $lastNumero;
        }
    }

    private function getNextClientNumero()
    {
        $lastSequence = ClientSequence::latest()->first();
        if($lastSequence) {
            $lastNumero = $lastSequence->numero;
        } else {
            $lastNumero = '010000';
        }
        $res = Client::where('numero_saisi', $lastNumero)->first();
        if($res) {
            $newLastNumero = Str::padLeft($lastNumero + 1, 6, $pad = '0');
            ClientSequence::create([
                'numero' => $newLastNumero,
            ]);
            return $this->getNextClientNumero();
        } else {
            return $lastNumero;
        }
    }

    protected function createDemande(array $data, $mode = 'create_mode', Demande $demande = null)
    {
        $source = DemandeSource::firstOrCreate([
            'name' => $data['source'],
        ], [
            'name' => $data['source'],
        ]);

        if (!empty($data['existing_client'])) {

            /**
             * @var Client $client
             */
            $client = Client::byUuid($data['existing_client']['uuid'])->first();

            if(!empty($data['existing_client']['existing_service_address'])) {

                $service_address = Address::findOrFail($data['existing_client']['existing_service_address']['id']);

            } else if(!empty($data['existing_client']['new_service_address'])) {

                $sa = $data['existing_client']['new_service_address'];
                $service_address = Address::create([
                    'street' => $sa['street'],
                    'city' => $sa['city'],
                    'province' => $sa['province'],
                    'zipcode' => $sa['zipcode'],
                    'country' => $sa['country'],
                    'postal_address' => $sa['postal_address'],
                ]);
                $client->service_addresses()->save($service_address);

            }

        } else if (!empty($data['new_client'])) {

            $lastNumero = $this->getNextClientNumero();

            /**
             * @var Client $client
             */
            $client = Client::create([
                'numero_saisi' => $lastNumero,
                'client_titre_id' => $data['new_client']['client_titre_id'],
                'prenom' => $data['new_client']['prenom'],
                'nom' => $data['new_client']['nom'],
            ]);

            if($client) {
                if($client->numero_saisi == $lastNumero) {
                    $newLastNumero = Str::padLeft($lastNumero + 1, 6, $pad = '0');
                    ClientSequence::create([
                        'numero' => $newLastNumero,
                    ]);
                }

                if (!empty($data['new_client']['company'])) {
                    ClientCompany::create([
                        'name' => $data['new_client']['company'],
                        'client_id' => $client->id,
                    ]);
                }

                ClientCellulaire::create([
                    'client_id' => $client->id,
                    'numero' => $data['new_client']['cellulaire'],
                    'send_sms' => true,
                ]);

                ClientCourriel::create([
                    'client_id' => $client->id,
                    'email' => $data['new_client']['courriel'],
                ]);

                $billing_address = Address::create([
                    'street' => $data['new_client']['billing_address']['street'],
                    'city' => $data['new_client']['billing_address']['city'],
                    'province' => $data['new_client']['billing_address']['province'],
                    'zipcode' => $data['new_client']['billing_address']['zipcode'],
                    'country' => $data['new_client']['billing_address']['country'],
                    'postal_address' => $data['new_client']['billing_address']['postal_address'],
                ]);
                $client->billing_address()->save($billing_address);

                if($data['new_client']['same_as_billing_address']) {
                    $service_address = Address::create([
                        'street' => $billing_address->street,
                        'city' => $billing_address->city,
                        'province' => $billing_address->province,
                        'zipcode' => $billing_address->zipcode,
                        'country' => $billing_address->country,
                        'postal_address' => $billing_address->postal_address,
                    ]);
                    $client->service_addresses()->save($service_address);
                } else {
                    if(!empty($data['new_client']['service_address'])) {
                        $sa = $data['new_client']['service_address'];
                        $service_address = Address::create([
                            'street' => $sa['street'],
                            'city' => $sa['city'],
                            'province' => $sa['province'],
                            'zipcode' => $sa['zipcode'],
                            'country' => $sa['country'],
                            'postal_address' => $sa['postal_address'],
                        ]);
                        $client->service_addresses()->save($service_address);
                    }
                }

            }

        }

        $now = new \DateTime();

        /**
         * @var Demande $demande
         */
        $demande = Demande::updateOrCreate([
            "id" => $demande ? $demande->id : null,
        ], $mode == 'create_mode' ? [
            'numero' => $data['numero'],
            'created_by' => auth()->user()->id,
            'statut' => !empty($data['assigned_to']) ? Demande::STATUT_ASSIGNEE : Demande::STATUT_BROUILLON,
            'statut_change_date' => $now,
            'delai_de_reponse' => null,
            'assigned_to' => $data['assigned_to'],
            'urgent' => $data['urgent'],
            'demande_source_id' => $source->id,
            'plus_de_details' => $data['plus_de_details'],
            'confirmation_email' => $data['confirmation_email'],
            'client_id' => $client->id,
            'reception_date' => $now,
        ] : [
            'statut' => $data['statut'],
            'statut_change_date' => $now,
            'delai_de_reponse' => null,
            'assigned_to' => $data['assigned_to'],
            'urgent' => $data['urgent'],
            'demande_source_id' => $source->id,
            'plus_de_details' => $data['plus_de_details'],
        ]);

        $demande->service_address()->delete();
        $demande->service_address()->save($service_address);

        $demande->services()->sync([]);

        if (!empty($data['services'])) {
            $serviceIds = [];
            foreach ($data['services'] as $service) {
                $demande_service = DemandeService::firstOrCreate([
                    'name' => $service,
                ], [
                    'name' => $service,
                ]);
                $serviceIds[] = $demande_service->id;
            }
            $demande->services()->sync($serviceIds);
        }

        if (!empty($data['client_appointment'])) {
            /**
             * @var DemandeClientAppointment $demande_client_appointment
             */
            $demande_client_appointment = DemandeClientAppointment::updateOrCreate([
                'demande_id' => $demande->id,
            ], [
                'appointment_date' => date("U",strtotime($data['client_appointment']['appointment_date'])),
                'year' => date("Y",strtotime($data['client_appointment']['appointment_date'])),
                'month' => date("m",strtotime($data['client_appointment']['appointment_date'])),
                'day' => date("d",strtotime($data['client_appointment']['appointment_date'])),
                'time' => date("H:i:s",strtotime($data['client_appointment']['appointment_date'])),
            ]);

            $demande_client_appointment->client_availabilities()->sync([]);

            if (!empty($data['client_appointment']['demande_client_availability_id'])) {
                $demande_client_appointment->client_availabilities()->sync($data['client_appointment']['demande_client_availability_id']);
            }
        } else {
            $demande_client_appointment = $demande->client_appointment()->delete();
        }

        $demande->tags()->sync([]);

        if (!empty($data['tags'])) {
            $tagIds = [];
            foreach ($data['tags'] as $strTag) {
                $tag = DemandeTag::firstOrCreate([
                    'name' => $strTag,
                ], [
                    'name' => $strTag,
                ]);
                $tagIds[] = $tag->id;
            }
            $demande->tags()->sync($tagIds);
        }

        $demande->product_presentations()->sync([]);

        if (!empty($data['demande_product_presentation_id'])) {
            $demande->product_presentations()->sync($data['demande_product_presentation_id']);
        }

        $demande->notes_internes()->delete();

        if (!empty($data['notes_internes'])) {
            $aNotes = [];
            foreach ($data['notes_internes'] as $note_interne) {
                /**
                 * @var Note $note
                 */
                $note = Note::create([
                    'note' => $note_interne['note'],
                    'report_to_soumission' => $note_interne['report_to_soumission'],
                    'report_to_contrat' => $note_interne['report_to_contrat'],
                    'report_to_bon_de_travail' => $note_interne['report_to_bon_de_travail'],
                    'report_to_facture' => $note_interne['report_to_facture'],
                ]);

                $note->attachements()->delete();

                if (!empty($note_interne['attachements'])) {
                    $pieces_jointes = [];
                    foreach ($note_interne['attachements'] as $attachement) {
                        $filename =  $this->uploadFile($attachement , 'demandes');
                        $pj = Photo::create([
                            'url' => $filename,
                        ]);
                        $pieces_jointes[] = $pj;
                    }
                    $note->attachements()->saveMany($pieces_jointes);
                }

                $aNotes[] = $note;
            }
            $demande->notes_internes()->saveMany($aNotes);
        }

        $activity = DocumentActivity::create([
            'activity_user' => auth()->user()->id,
            'document_statut' => $demande->statut,
        ]);

        $demande->activities()->save($activity);

        return $demande;
    }

    /**
     * @OA\Get(path="/api/demandes/{uuid}",
     *   tags={"Demandes"},
     *   summary="Affichage d'une demande",
     *   description="Affichage d'une demande",
     *   operationId="getDemande",
     *   @OA\Parameter(
     *     name="uuid",
     *     required=true,
     *     in="path",
     *     description="L'uuid de la demande",
     *     @OA\Schema(
     *          type="string",
     *          example="RQ-010000"
     *     )
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="Résultat obtenu",
     *      content={
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="data",
     *                      type="object",
     *                      description="Données d'une demande",
     *                      @OA\Property(
     *                          property="client",
     *                          type="string",
     *                          description="Client de la demande",
     *                          example="Lemay"
     *                      ),
     *                      @OA\Property(
     *                          property="created_at",
     *                          type="string",
     *                          description="Date de création de la demande",
     *                          example="2021-06-16T18:49:07.000000Z"
     *                      ),
     *                      @OA\Property(
     *                          property="uuid",
     *                          type="string",
     *                          description="Uuid de la demande",
     *                          example="RQ-010000"
     *                      ),
     *                      @OA\Property(
     *                          property="urgent",
     *                          type="integer",
     *                          description="Urgent",
     *                          example=1
     *                      ),
     *                      @OA\Property(
     *                          property="statut",
     *                          type="string",
     *                          description="Statut de la demande",
     *                          example="Assignée"
     *                      ),
     *                      @OA\Property(
     *                          property="delai_de_reponse",
     *                          type="string",
     *                          description="Délai de réponse",
     *                          example=null
     *                      ),
     *                      @OA\Property(
     *                          property="creator",
     *                          type="object",
     *                          description="Créateur de la demande",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=1
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                              description="Nom du créateur",
     *                              example="Admin"
     *                          ),
     *                          @OA\Property(
     *                              property="firstname",
     *                              type="string",
     *                              description="Prénom du créateur",
     *                              example="admin"
     *                          ),
     *                          @OA\Property(
     *                              property="lastname",
     *                              type="string",
     *                              description="Nom du créateur",
     *                              example="Admin"
     *                          ),
     *                          @OA\Property(
     *                              property="email",
     *                              type="string",
     *                              description="Email du créateur",
     *                              example="admin@admin.com"
     *                          ),
     *                          @OA\Property(
     *                              property="mobilephone",
     *                              type="string",
     *                              description="Téléphone mobile",
     *                              example=null
     *                          ),
     *                          @OA\Property(
     *                              property="phone",
     *                              type="string",
     *                              description="Téléphone fixe",
     *                              example=null
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          property="assigne",
     *                          type="object",
     *                          description="L'utilisateur à qui la demande est assignée",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=2
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                              description="Nom de l'assigné",
     *                              example="Mathieu Roy"
     *                          ),
     *                          @OA\Property(
     *                              property="firstname",
     *                              type="string",
     *                              description="Prénom de l'assigné",
     *                              example="Mathieu"
     *                          ),
     *                          @OA\Property(
     *                              property="lastname",
     *                              type="string",
     *                              description="Nom de l'assigné",
     *                              example="Roy"
     *                          ),
     *                          @OA\Property(
     *                              property="email",
     *                              type="string",
     *                              description="Email de l'assigné",
     *                              example="mathieu.roy@strategemedia.com"
     *                          ),
     *                          @OA\Property(
     *                              property="mobilephone",
     *                              type="string",
     *                              description="Téléphone mobile de l'assigné",
     *                              example=null
     *                          ),
     *                          @OA\Property(
     *                              property="phone",
     *                              type="string",
     *                              description="Téléphone fixe de l'assigné",
     *                              example=null
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          property="source",
     *                          type="object",
     *                          description="Source de la demande",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=5
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                              description="Nom de la source",
     *                              example="Réseaux sociaux"
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          property="services",
     *                          type="array",
     *                          description="Services",
     *                          @OA\Items(
     *                              type="object",
     *                              @OA\Property(
     *                                  property="id",
     *                                  type="integer",
     *                                  description="ID",
     *                                  example=2
     *                              ),
     *                              @OA\Property(
     *                                  property="name",
     *                                  type="string",
     *                                  description="Nom du service",
     *                                  example="Réparation"
     *                              )
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          property="plus_de_details",
     *                          type="string",
     *                          description="Plus de détails",
     *                          example="Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l'imprimerie depuis les années 1500"
     *                      ),
     *                      @OA\Property(
     *                          property="client_appointment",
     *                          type="object",
     *                          description="Rendez-vous avec le client",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=1
     *                          ),
     *                          @OA\Property(
     *                              property="appointment_date",
     *                              type="string",
     *                              description="Date du rendez-vous",
     *                              example="2021-06-20T08:30:20.000000Z"
     *                          ),
     *                          @OA\Property(
     *                              property="year",
     *                              type="integer",
     *                              description="Année du rendez-vous",
     *                              example=2021
     *                          ),
     *                          @OA\Property(
     *                              property="month",
     *                              type="string",
     *                              description="Mois du rendez-vous",
     *                              example="06"
     *                          ),
     *                          @OA\Property(
     *                              property="day",
     *                              type="string",
     *                              description="Jour du rendez-vous",
     *                              example="20"
     *                          ),
     *                          @OA\Property(
     *                              property="time",
     *                              type="string",
     *                              description="Heure du rendez-vous",
     *                              example="08:30:20"
     *                          ),
     *                          @OA\Property(
     *                              property="client_availabilities",
     *                              type="array",
     *                              description="Disponibilités clients",
     *                              @OA\Items(
     *                                  type="object",
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="ID",
     *                                      example=3
     *                                  ),
     *                                  @OA\Property(
     *                                      property="name",
     *                                      type="string",
     *                                      description="Nom de la disponibilité",
     *                                      example="Soir"
     *                                  )
     *                              )
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          property="tags",
     *                          type="array",
     *                          description="Etiquettes",
     *                          @OA\Items(
     *                              type="object",
     *                              @OA\Property(
     *                                  property="id",
     *                                  type="integer",
     *                                  description="ID",
     *                                  example=3
     *                              ),
     *                              @OA\Property(
     *                                  property="name",
     *                                  type="string",
     *                                  description="Nom de l'étiquette",
     *                                  example="Gouttières"
     *                              )
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          property="service_address",
     *                          type="object",
     *                          description="Adresse de service de la demande",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=2
     *                          ),
     *                          @OA\Property(
     *                              property="street",
     *                              type="string",
     *                              description="Rue",
     *                              example="3115 Tycos Dr"
     *                          ),
     *                          @OA\Property(
     *                              property="city",
     *                              type="string",
     *                              description="Ville",
     *                              example="Toronto"
     *                          ),
     *                          @OA\Property(
     *                              property="province",
     *                              type="string",
     *                              description="Province",
     *                              example="Ontario"
     *                          ),
     *                          @OA\Property(
     *                              property="zipcode",
     *                              type="string",
     *                              description="Zipcode",
     *                              example="M5T 1T4"
     *                          ),
     *                          @OA\Property(
     *                              property="country",
     *                              type="string",
     *                              description="Pays",
     *                              example="Canada"
     *                          ),
     *                          @OA\Property(
     *                              property="note_interne",
     *                              type="string",
     *                              description="Note interne",
     *                              example=null
     *                          ),
     *                          @OA\Property(
     *                              property="postal_address",
     *                              type="string",
     *                              description="Adresse postale",
     *                              example="3115 Tycos Drive, Toronto, North York, ON, Canada"
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          type="array",
     *                          property="notes_internes",
     *                          @OA\Items(
     *                              type="object",
     *                              @OA\Property(
     *                                  property="id",
     *                                  type="integer",
     *                                  description="ID",
     *                                  example=1
     *                              ),
     *                              @OA\Property(
     *                                  property="note",
     *                                  type="string",
     *                                  description="Note",
     *                                  example="Contrairement à une opinion répandue, le Lorem Ipsum n'est pas simplement du texte aléatoire"
     *                              ),
     *                              @OA\Property(
     *                                  property="report_to_soumission",
     *                                  type="integer",
     *                                  description="Reporter la note à la soumission suivante",
     *                                  example=1
     *                              ),
     *                              @OA\Property(
     *                                  property="report_to_contrat",
     *                                  type="integer",
     *                                  description="Reporter la note au contrat suivant",
     *                                  example=1
     *                              ),
     *                              @OA\Property(
     *                                  property="report_to_bon_de_travail",
     *                                  type="integer",
     *                                  description="Reporter la note au bon de travail suivant",
     *                                  example=0
     *                              ),
     *                              @OA\Property(
     *                                  property="report_to_facture",
     *                                  type="integer",
     *                                  description="Reporter la note à la facture suivante",
     *                                  example=1
     *                              ),
     *                              @OA\Property(
     *                                  type="array",
     *                                  property="attachements",
     *                                  description="Pièces jointes de la note",
     *                                  @OA\Items(
     *                                      type="object",
     *                                      @OA\Property(
     *                                          property="id",
     *                                          type="integer",
     *                                          description="ID",
     *                                          example=2
     *                                      ),
     *                                      @OA\Property(
     *                                          property="photo_url",
     *                                          type="string",
     *                                          description="Url de la pièce jointe",
     *                                          example="http://localhost:2023/storage/uploads/clients/docx/8/ny-fiainana-araka-ny-fanahy.docx"
     *                                      )
     *                                  )
     *                              )
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *      }
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Utilisateur non connecté",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="L'utilisateur n'est même pas connecté",
     *                         example="Vous ne vous êtes pas encore authentifié."
     *                     )
     *                 )
     *             )
     *         }
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Utilisateur non autorisé ou non permis",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="L'utilisateur n'a pas la permission nécessaire",
     *                         example="Vous n'avez pas la permission d'effectuer cette action."
     *                     )
     *                 )
     *             )
     *         }
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Client inexistant",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Le client n'existe pas",
     *                         example="Cet article n'existe pas."
     *                     )
     *                 )
     *             )
     *         }
     *   )
     * )
     *
     * Display a listing of the resource.
     *
     * @return DemandeResource
     */
    public function show(Demande $demande)
    {
        return new DemandeResource($demande);
        // return fractal($demande, new DemandeTransformer())->respond();
    }

    public function update(Request $request, Demande $demande)
    {
    }

    public function destroy(Demande $demande)
    {
    }

    /**
     * @OA\Get(path="/api/demande_date_filter_interval",
     *   tags={"Demandes"},
     *   summary="Intervalle de dates correspondant à chaque filtre de dates",
     *   description="Intervalle de dates correspondant à chaque filtre de dates",
     *   operationId="demandeDateFilterInterval",
     *   @OA\Parameter(
     *     name="date_filter",
     *     in="query",
     *     description="Filtre de dates",
     *     @OA\Schema(
     *          type="string",
     *          example="this_week"
     *     )
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="Liste obtenue",
     *      content={
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      type="object",
     *                      property="data",
     *                      @OA\Property(
     *                          property="datetime_interval",
     *                          type="array",
     *                          description="Intervalle de dates",
     *                          @OA\Items(
     *                              type="string",
     *                              example="2021-07-01T00:00:00.000000Z"
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *      }
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Utilisateur non connecté",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="L'utilisateur n'est même pas connecté",
     *                         example="Vous ne vous êtes pas encore authentifié."
     *                     )
     *                 )
     *             )
     *         }
     *   )
     * )
     *
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function getDateFilterInterval(Request $request)
    {
        $interval = $this->getDateInterval(request('date_filter'));

        return response()->json([
            'data' => [
                'datetime_interval' => $interval,
            ]
        ]);
    }

    private function getDateInterval($date_filter)
    {
        switch ($date_filter) {
            case 'today': // Aujourd'hui
                $interval = [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()];
                break;

            case 'yesterday': // Hier
                $interval = [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()];
                break;

            case 'this_week': // Cette semaine
                $interval = [Carbon::today()->startOfWeek(), Carbon::today()->endOfWeek()];
                break;

            case 'the_last_seven_days': // Les sept derniers jours
                $interval = [Carbon::today()->subdays(7)->startOfDay(), Carbon::yesterday()->endOfDay()];
                break;

            case 'last_week': // La semaine dernière
                $interval = [Carbon::today()->subWeek()->startOfWeek() , Carbon::today()->subWeek()->endOfWeek()];
                break;

            case 'this_month': // Ce mois-ci
                $interval = [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()];
                break;

            case 'last_month': // Le mois dernier
                $interval = [Carbon::today()->subMonth()->startOfMonth(), Carbon::today()->subMonth()->endOfMonth()];
                break;

            case 'the_last_thirty_days': // Les 30 derniers jours
                $interval = [Carbon::today()->subdays(30)->startOfDay(), Carbon::yesterday()->endOfDay()];
                break;

            case 'this_year': // Cette année
                $interval = [Carbon::today()->startOfYear(), Carbon::today()->endOfYear()];
                break;

            case 'last_year': // L'année dernière
                $interval = [Carbon::today()->subYear()->startOfYear() , Carbon::today()->subYear()->endOfYear()];
                break;

            case 'last_year_on_the_same_date': // L'année dernière à la même date
                $dateString = sprintf('%s-%s-%s', Carbon::today()->subYear()->format('Y'), Carbon::today()->format('m'), Carbon::today()->format('d'));
                $interval = [Carbon::createFromFormat('Y-m-d', $dateString)->startOfDay(), Carbon::createFromFormat('Y-m-d', $dateString)->endOfDay()];
                break;

            case 'for_life': // A vie
                $interval = [auth()->user()->created_at, Carbon::today()->endOfDay()];
                break;

            default:
                $interval = [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()];
                break;
        }

        return $interval;
    }

    /**
     * @OA\Post(path="/api/demande_actions",
     *   tags={"Demandes"},
     *   summary="Actions groupées sur des demandes",
     *   description="Actions groupées sur des demandes",
     *   operationId="demandesActions",
     *   @OA\RequestBody(
     *       required=true,
     *       description="Paramètres d'action sur les demandes",
     *       @OA\JsonContent(
     *          type="object",
     *          required={"action", "demande_uuids"},
     *          @OA\Property(
     *              property="action",
     *              description="Action sur les demandes",
     *              type="string",
     *              enum={"Planifier", "Convertir en soumission", "Perdue", "Annuler", "Supprimer", "Dupliquer"}
     *          ),
     *          @OA\Property(
     *              property="demande_uuids",
     *              type="array",
     *              description="Les uuid des demandes",
     *              @OA\Items(
     *                  type="string",
     *                  example="RQ-010000"
     *              )
     *          )
     *       )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Modification réussie sur les demandes",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Message de confirmation",
     *                         example="Les demandes ont été modifiées avec succès."
     *                     )
     *                 )
     *             )
     *         }
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Paramètres vides ou invalides",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                          property="errors",
     *                          type="object",
     *                          @OA\Property(
     *                              property="action",
     *                              type="array",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="Le champ action est obligatoire."
     *                              )
     *                          ),
     *                          @OA\Property(
     *                              property="demande_uuids",
     *                              type="array",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="Le champ demande uuids est obligatoire."
     *                              )
     *                         )
     *                     )
     *                 )
     *             )
     *        }
     *   )
     * )
     *
     * Handle a client creation request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse|Response
     */
    public function act(Request $request)
    {
        $validator = $this->validatorAct($request->all());

        if($validator->fails()) {
            return response(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $now = new \DateTime();

        switch ($request->action) {
            case 'Planifier':
                foreach ($request->demande_uuids as $demande_uuid) {
                    $demande = Demande::byUuid($demande_uuid)->first();
                    if ($demande) {
                        $demande->update([
                            'statut' => Demande::STATUT_PLANIFIEE,
                            'statut_change_date' => $now,
                        ]);
                    }
                }
                break;

            case 'Convertir en soumission':
                foreach ($request->demande_uuids as $demande_uuid) {
                    $demande = Demande::byUuid($demande_uuid)->first();
                    if ($demande) {
                        $demande->update([
                            'statut' => Demande::STATUT_SOUMISSIONNEE,
                            'statut_change_date' => $now,
                        ]);
                    }
                }
                break;

            case 'Perdue':
                foreach ($request->demande_uuids as $demande_uuid) {
                    $demande = Demande::byUuid($demande_uuid)->first();
                    if ($demande) {
                        $demande->update([
                            'statut' => Demande::STATUT_PERDUE,
                            'statut_change_date' => $now,
                        ]);
                    }
                }
                break;

            case 'Annuler':
                foreach ($request->demande_uuids as $demande_uuid) {
                    $demande = Demande::byUuid($demande_uuid)->first();
                    if ($demande) {
                        $demande->update([
                            'statut' => Demande::STATUT_ANNULEE,
                            'statut_change_date' => $now,
                        ]);
                    }
                }
                break;

            case 'Supprimer':
                foreach ($request->demande_uuids as $demande_uuid) {
                    $demande = Demande::byUuid($demande_uuid)->first();
                    if ($demande) {
                        $demande->delete();
                    }
                }
                break;

            case 'Dupliquer':
                foreach ($request->demande_uuids as $demande_uuid) {
                    $demande = Demande::byUuid($demande_uuid)->first();
                    if ($demande) {
                        // copy attributes from original model
                        $newDemande = $demande->replicate();
                        // Reset any fields needed to connect to another parent, etc
                        $now = new \DateTime();
                        $newDemande->reception_date = $now;
                        $newDemande->statut_change_date = $now;
                        $newDemande->numero = $this->getNextNumero();
                        $newDemande->uuid = 'RQ-'.$this->getNextNumero();
                        // save model before you recreate relations (so it has an id)
                        $newDemande->push();

                        $newDemande->services()->sync($demande->services()->get());
                        $newDemande->tags()->sync($demande->tags()->get());

                        $demande_client_appointment = $demande->client_appointment()->first();

                        $new_demande_client_appointment = DemandeClientAppointment::create([
                            'appointment_date' => $demande_client_appointment->appointment_date,
                            'year' => $demande_client_appointment->year,
                            'month' => $demande_client_appointment->month,
                            'day' => $demande_client_appointment->day,
                            'time' => $demande_client_appointment->time,
                            'demande_id' => $newDemande->id,
                        ]);

                        if ( $demande_client_appointment->client_availabilities()->get()->isNotEmpty() ) {
                            $new_demande_client_appointment->client_availabilities()->sync( $demande_client_appointment->client_availabilities()->get() );
                        }

                        $service_address = $demande->service_address()->first();

                        $new_service_address = Address::create([
                            'street' => $service_address->street,
                            'city' => $service_address->city,
                            'province' => $service_address->province,
                            'zipcode' => $service_address->zipcode,
                            'country' => $service_address->country,
                            'postal_address' => $service_address->postal_address,
                        ]);

                        $newDemande->service_address()->save($new_service_address);

                        $demande_notes_internes =  $demande->notes_internes()->get();

                        $aNotes = [];
                        foreach ($demande_notes_internes as $demande_note_interne) {

                            $note = Note::create([
                                'note' => $demande_note_interne->note,
                                'report_to_soumission' => $demande_note_interne->report_to_soumission,
                                'report_to_contrat' => $demande_note_interne->report_to_contrat,
                                'report_to_bon_de_travail' => $demande_note_interne->report_to_bon_de_travail,
                                'report_to_facture' => $demande_note_interne->report_to_facture,
                            ]);

                            $attachements = $demande_note_interne->attachements()->get();

                            $pieces_jointes = [];
                            foreach ($attachements as $attachement) {
                                $filename = $attachement->url;
                                $pj = Photo::create([
                                    'url' => $filename,
                                ]);
                                $pieces_jointes[] = $pj;
                            }

                            $note->attachements()->saveMany($pieces_jointes);
                            $aNotes[] = $note;
                        }
                        $newDemande->notes_internes()->saveMany($aNotes);

                        $activity = DocumentActivity::create([
                            'activity_user' => auth()->user()->id,
                            'document_statut' => $newDemande->statut,
                        ]);

                        $newDemande->activities()->save($activity);
                    }
                }
                break;

            default:
                # code...
                break;
        }

        return response()->json(['message' => trans('demande.updated')]);
    }
}
