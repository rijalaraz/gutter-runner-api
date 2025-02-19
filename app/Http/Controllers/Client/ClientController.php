<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Models\Address;
use App\Models\Client\Client;
use App\Models\Client\ClientCellulaire;
use App\Models\Client\ClientCompany;
use App\Models\Client\ClientCourriel;
use App\Models\Client\ClientSequence;
use App\Models\Client\ClientTelephone;
use App\Models\Photo;
use App\Models\Tag;
use App\Support\UploadBase64Trait;
use App\Transformers\Client\ClientTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    use UploadBase64Trait;

    public function __construct(Client $model)
    {
        $this->model = $model;
        $this->middleware('permission:Ajouter')->only('store');
        $this->middleware('permission:Modification')->only('update');
        $this->middleware('permission:Lecture seule', ['only' => ['index','show']]);
    }

    public function index(Request $request)
    {
    }

    /**
     * @OA\Get(path="/clients_autocomplete",
     *   tags={"Demandes"},
     *   summary="Liste des clients en autocomplete",
     *   description="Liste des clients en autocomplete",
     *   operationId="clientsList",
     *   @OA\Parameter(
     *     name="nom",
     *     in="query",
     *     description="Une chaîne de caractères",
     *     @OA\Schema(
     *          type="string",
     *          example="m"
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
     *                      type="array",
     *                      property="data",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=1
     *                          ),
     *                          @OA\Property(
     *                              property="uuid",
     *                              type="integer",
     *                              description="L'uuid du client",
     *                              example="CU-010000"
     *                          ),
     *                          @OA\Property(
     *                              property="nom_complet",
     *                              type="string",
     *                              description="Nom",
     *                              example="Catherine Henry"
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="total",
     *                      type="integer",
     *                      description="Nombre total",
     *                      example=4
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
     * @return \Illuminate\Http\Response
     */
    public function getList(Request $request)
    {
        $clients = Client::with('billing_address')
            ->whereLike('prenom', $request->nom)
            ->orWhereLike('nom', $request->nom)
            ->get();

        return response()->json([
            'data' => $clients,
            'total' => $clients->count(),
        ]);
    }

    /**
     * @OA\Post(path="/clients",
     *   tags={"Clients"},
     *   summary="Création d'un client",
     *   description="Création d'un client",
     *   operationId="clientCreation",
     *   @OA\RequestBody(
     *       required=true,
     *       description="Paramètres de création d'un client",
     *       @OA\JsonContent(
     *          type="object",
     *          required={"numero_saisi", "client_titre_id", "prenom", "nom", "company", "client_recurrent", "client_payment_term_id", "cellulaire", "courriels", "billing_address", "same_as_billing_address"},
     *          @OA\Property(
     *              property="numero_saisi",
     *              type="string",
     *              description="Numéro du client à 6 chiffres",
     *              example="010000"
     *          ),
     *          @OA\Property(
     *              property="client_titre_id",
     *              type="integer",
     *              description="Identifiant du titre M. ou Mme.",
     *              example=1
     *          ),
     *          @OA\Property(
     *              property="prenom",
     *              type="string",
     *              description="Prénom du client",
     *              example="Fabien"
     *          ),
     *          @OA\Property(
     *              property="nom",
     *              type="string",
     *              description="Nom du client",
     *              example="Lagarde"
     *          ),
     *          @OA\Property(
     *              property="company",
     *              type="object",
     *              description="Compagnie où travaille le client",
     *              required={"name", "client_company_type_id"},
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  description="Nom de la compagnie",
     *                  example="Proxima"
     *              ),
     *              @OA\Property(
     *                  property="client_company_type_id",
     *                  type="array",
     *                  description="Identifiants du type de compagnie",
     *                  @OA\Items(
     *                      type="integer",
     *                      example=3
     *                  )
     *              )
     *          ),
     *          @OA\Property(
     *              property="client_recurrent",
     *              type="boolean",
     *              description="Client récurrent",
     *              example=false
     *          ),
     *          @OA\Property(
     *              property="client_payment_term_id",
     *              type="integer",
     *              description="Identifiant du terme de paiement",
     *              example=4
     *          ),
     *          @OA\Property(
     *              property="tags",
     *              type="array",
     *              description="Les étiquettes du client",
     *              @OA\Items(
     *                  type="string",
     *                  example="Aluminium"
     *              )
     *          ),
     *          @OA\Property(
     *              property="cellulaire",
     *              type="object",
     *              description="Céllulaire du client",
     *              required={"numero", "send_sms"},
     *              @OA\Property(
     *                  property="numero",
     *                  type="string",
     *                  description="Numéro du cellulaire",
     *                  example="604-221-1023"
     *              ),
     *              @OA\Property(
     *                  property="send_sms",
     *                  type="boolean",
     *                  description="Envoyer des sms à ce céllulaire ou pas",
     *                  example=true
     *              )
     *          ),
     *          @OA\Property(
     *              property="telephones",
     *              type="array",
     *              description="Autres téléphones du client",
     *              @OA\Items(
     *                  type="object",
     *                  required={"numero", "client_numero_type_id"},
     *                  @OA\Property(
     *                      property="numero",
     *                      type="string",
     *                      description="Numéro du téléphone",
     *                      example="250-218-8608"
     *                  ),
     *                  @OA\Property(
     *                      property="client_numero_type_id",
     *                      type="integer",
     *                      description="Type de numéro du téléphone",
     *                      example=1
     *                  )
     *              )
     *          ),
     *          @OA\Property(
     *              property="courriels",
     *              type="array",
     *              description="Adresses email du client",
     *              @OA\Items(
     *                  type="object",
     *                  required={"email"},
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      description="Email",
     *                      example="fabien.lagarde@proxima.ca"
     *                  )
     *              )
     *          ),
     *          @OA\Property(
     *              property="note_interne",
     *              type="string",
     *              description="Note interne du client",
     *              example="On sait depuis longtemps que travailler avec du texte lisible et contenant du sens est source de distractions, et empêche de se concentrer sur la mise en page elle-même"
     *          ),
     *          @OA\Property(
     *              property="billing_address",
     *              type="object",
     *              description="Adresse de facturation",
     *              required={"street", "city", "province", "zipcode", "country", "postal_address"},
     *              @OA\Property(
     *                  property="street",
     *                  type="string",
     *                  description="Rue",
     *                  example="3691 rue Fournier"
     *              ),
     *              @OA\Property(
     *                  property="city",
     *                  type="string",
     *                  description="Ville",
     *                  example="St Jerome"
     *              ),
     *              @OA\Property(
     *                  property="province",
     *                  type="string",
     *                  description="Province",
     *                  example="Quebec"
     *              ),
     *              @OA\Property(
     *                  property="zipcode",
     *                  type="string",
     *                  description="Code postal",
     *                  example="J7Z 4V1"
     *              ),
     *              @OA\Property(
     *                  property="country",
     *                  type="string",
     *                  description="Pays",
     *                  example="Canada"
     *              ),
     *              @OA\Property(
     *                  property="note_interne",
     *                  type="string",
     *                  description="Note interne de l'adresse de facturation",
     *                  example="Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression"
     *              ),
     *              @OA\Property(
     *                  property="postal_address",
     *                  type="string",
     *                  description="Adresse postale obtenue avec l'API Google",
     *                  example="2466 Boulevard Laurier, Québec, QC, Canada"
     *              )
     *          ),
     *          @OA\Property(
     *              property="same_as_billing_address",
     *              type="boolean",
     *              description="true si l'adresse de service est la même que l'adresse de facturation",
     *              example=true
     *          ),
     *          @OA\Property(
     *              property="service_address_note_interne",
     *              type="string",
     *              description="Note interne de l'adresse de service si elle est la même que l'adresse de facturation",
     *              example="Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum"
     *          ),
     *          @OA\Property(
     *              property="service_addresses",
     *              type="array",
     *              description="Adresses de service du client si différente de l'adresse de facturation",
     *              @OA\Items(
     *                  type="object",
     *                  required={"street", "city", "province", "zipcode", "country", "postal_address"},
     *                  @OA\Property(
     *                      property="street",
     *                      type="string",
     *                      description="Rue",
     *                      example="147 Scarth Street"
     *                  ),
     *                  @OA\Property(
     *                      property="city",
     *                      type="string",
     *                      description="Ville",
     *                      example="Montreal"
     *                  ),
     *                  @OA\Property(
     *                      property="province",
     *                      type="string",
     *                      description="Province",
     *                      example="Quebec"
     *                  ),
     *                  @OA\Property(
     *                      property="zipcode",
     *                      type="string",
     *                      description="Code postal",
     *                      example="S4P 3Y2"
     *                  ),
     *                  @OA\Property(
     *                      property="country",
     *                      type="string",
     *                      description="Pays",
     *                      example="Canada"
     *                  ),
     *                  @OA\Property(
     *                      property="note_interne",
     *                      type="string",
     *                      description="Note interne de l'adresse de service",
     *                      example="De nombreuses suites logicielles de mise en page ou éditeurs de sites Web ont fait du Lorem Ipsum leur faux texte par défaut"
     *                  ),
     *                  @OA\Property(
     *                      property="postal_address",
     *                      type="string",
     *                      description="Adresse postale obtenue avec l'API Google",
     *                      example="2466 Rue de la Tamise, Québec, QC, Canada"
     *                  )
     *              )
     *          ),
     *          @OA\Property(
     *              property="pieces_jointes",
     *              type="array",
     *              description="Pièces jointes",
     *              @OA\Items(
     *                  type="object",
     *                  required={"url", "file_name"},
     *                  @OA\Property(
     *                      property="file_name",
     *                      type="string",
     *                      description="Nom du fichier joint",
     *                      example="La joie de vivre.docx"
     *                  ),
     *                  @OA\Property(
     *                      property="url",
     *                      type="string",
     *                      description="Chaîne de caractères base64 de la pièce jointe",
     *                      example="UEsDBBQACAgIAKp+dVIAAAAAAAAAAAAAAAARAAAAZG9jUHJvcHMvY29yZS54bWx9Ul1PwjAUffdXLH3f2m6A0IyRqOFJEqMQjW+1u0Bx65q2fP17u8EmKvHt3nNOz/1qOjmURbADY2WlxohGBAWgRJVLtRqjxXwaDlFgHVc5LyoFY..."
     *                  )
     *              )
     *          )
     *       )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Création réussie d'un client",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="status",
     *                         type="string",
     *                         description="Message de confirmation",
     *                         example="Un client a été créé avec succès."
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
     *                              property="cellulaire.numero",
     *                              type="array",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="Le champ cellulaire.numero est obligatoire."
     *                              )
     *                          ),
     *                          @OA\Property(
     *                              property="pieces_jointes.0.url",
     *                              type="array",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="Le pieces_jointes.0.url doit être une chaîne Base64 valide."
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
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = $this->validator($request->all());

        if($validator->fails()) {
            return response(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $client = $this->createClient($request->all());
        if($client) {
            $lastSequence = ClientSequence::latest()->first();
            if($lastSequence) {
                $lastNumero = $lastSequence->numero;
            } else {
                $lastNumero = '010000';
            }
            if($client->numero_saisi == $lastNumero) {
                $newLastNumero = Str::padLeft($lastNumero + 1, 6, $pad = '0');
                ClientSequence::create([
                    'numero' => $newLastNumero,
                ]);
            }
        }

        return response()->json([
            'message' => trans('client.created'),
            'client' => $client,
        ]);
    }

    private function getNextNumero()
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
            return $this->getNextNumero();
        } else {
            return $lastNumero;
        }
    }

    /**
     * @OA\Post(path="/clients/quick",
     *   tags={"Demandes"},
     *   summary="Ajout rapide d'un nouveau client",
     *   description="Ajout rapide d'un nouveau client",
     *   operationId="createClientQuickly",
     *   @OA\RequestBody(
     *       required=true,
     *       description="Paramètres de création rapide d'un nouveau client",
     *       @OA\JsonContent(
     *          type="object",
     *          required={"client_titre_id", "nom_complet", "company", "telephone", "email", "billing_address", "same_as_billing_address"},
     *          @OA\Property(
     *              property="client_titre_id",
     *              type="integer",
     *              description="Identifiant du titre M. ou Mme.",
     *              example=2
     *          ),
     *          @OA\Property(
     *              property="nom_complet",
     *              type="string",
     *              description="Prénom et Nom du client",
     *              example="Sonya Berger"
     *          ),
     *          @OA\Property(
     *              property="company",
     *              type="string",
     *              description="Nom de la compagnie où travaille le client",
     *              example="Havana"
     *          ),
     *          @OA\Property(
     *              property="telephone",
     *              type="string",
     *              description="Numéro de téléphone",
     *              example="519-984-4952"
     *          ),
     *          @OA\Property(
     *              property="email",
     *              type="string",
     *              description="Email",
     *              example="sonya.berger@havana.ca"
     *          ),
     *          @OA\Property(
     *              property="billing_address",
     *              type="object",
     *              description="Adresse de facturation du client",
     *              required={"street", "city", "province", "zipcode", "country", "postal_address"},
     *              @OA\Property(
     *                  property="street",
     *                  type="string",
     *                  description="Rue",
     *                  example="3691 rue Fournier"
     *              ),
     *              @OA\Property(
     *                  property="city",
     *                  type="string",
     *                  description="Ville",
     *                  example="St Jerome"
     *              ),
     *              @OA\Property(
     *                  property="province",
     *                  type="string",
     *                  description="Province",
     *                  example="Quebec"
     *              ),
     *              @OA\Property(
     *                  property="zipcode",
     *                  type="string",
     *                  description="Code postal",
     *                  example="J7Z 4V1"
     *              ),
     *              @OA\Property(
     *                  property="country",
     *                  type="string",
     *                  description="Pays",
     *                  example="Canada"
     *              ),
     *              @OA\Property(
     *                  property="postal_address",
     *                  type="string",
     *                  description="Adresse postale obtenue avec l'API Google",
     *                  example="2466 Boulevard Laurier, Québec, QC, Canada"
     *              )
     *          ),
     *          @OA\Property(
     *              property="same_as_billing_address",
     *              type="boolean",
     *              description="true si l'adresse de service est la même que l'adresse de facturation",
     *              example=true
     *          ),
     *          @OA\Property(
     *              property="service_address",
     *              type="object",
     *              required={"street", "city", "province", "zipcode", "country", "postal_address"},
     *              description="Adresse de service du client si différente de l'adresse de facturation",
     *              @OA\Property(
     *                  property="street",
     *                  type="string",
     *                  description="Rue",
     *                  example="147 Scarth Street"
     *              ),
     *              @OA\Property(
     *                  property="city",
     *                  type="string",
     *                  description="Ville",
     *                  example="Montreal"
     *              ),
     *              @OA\Property(
     *                  property="province",
     *                  type="string",
     *                  description="Province",
     *                  example="Quebec"
     *              ),
     *              @OA\Property(
     *                  property="zipcode",
     *                  type="string",
     *                  description="Code postal",
     *                  example="S4P 3Y2"
     *              ),
     *              @OA\Property(
     *                  property="country",
     *                  type="string",
     *                  description="Pays",
     *                  example="Canada"
     *              ),
     *              @OA\Property(
     *                  property="postal_address",
     *                  type="string",
     *                  description="Adresse postale obtenue avec l'API Google",
     *                  example="2466 Rue de la Tamise, Québec, QC, Canada"
     *              )
     *          )
     *       )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Création réussie d'un client",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="status",
     *                         type="string",
     *                         description="Message de confirmation",
     *                         example="Un client a été créé avec succès."
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
     *                              property="company",
     *                              type="array",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="Le champ company est obligatoire."
     *                              )
     *                          ),
     *                          @OA\Property(
     *                              property="email",
     *                              type="array",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="La valeur du champ adresse email est déjà utilisée."
     *                              )
     *                         )
     *                     )
     *                 )
     *             )
     *        }
     *   )
     * )
     *
     * Handle a quick client creation request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function createClientQuickly(Request $request)
    {
        $validator = $this->validateQuick($request->all());

        if($validator->fails()) {
            return response(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $pieces = explode(" ", $request['nom_complet'], 2);
        $prenom = $pieces[0];
        $nom = $pieces[1];

        $lastNumero = $this->getNextNumero();

        $client = Client::create([
            'numero_saisi' => $lastNumero,
            'client_titre_id' => $request['client_titre_id'],
            'prenom' => $prenom,
            'nom' => $nom,
        ]);

        if($client) {
            if($client->numero_saisi == $lastNumero) {
                $newLastNumero = Str::padLeft($lastNumero + 1, 6, $pad = '0');
                ClientSequence::create([
                    'numero' => $newLastNumero,
                ]);
            }

            if (!empty($request['company'])) {
                ClientCompany::create([
                    'name' => $request['company'],
                    'client_id' => $client->id,
                ]);
            }

            ClientCellulaire::create([
                'client_id' => $client->id,
                'numero' => $request['telephone'],
                'send_sms' => true,
            ]);

            ClientCourriel::create([
                'client_id' => $client->id,
                'email' => $request['email'],
            ]);

            $billing_address = Address::create([
                'street' => $request['billing_address']['street'],
                'city' => $request['billing_address']['city'],
                'province' => $request['billing_address']['province'],
                'zipcode' => $request['billing_address']['zipcode'],
                'country' => $request['billing_address']['country'],
                'postal_address' => $request['billing_address']['postal_address'],
            ]);
            $client->billing_address()->save($billing_address);

            if($request['same_as_billing_address']) {
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
                if(!empty($request['service_address'])) {
                    $sa = $request['service_address'];
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

            return response()->json([
                'message' => trans('client.created'),
                'client' => $client,
            ]);
        }
    }

    protected function validateQuick(array $data)
    {
        return Validator::make($data, [
            'nom_complet' => 'required|max:255',
            'company' => 'sometimes|max:255',
            'telephone' => 'required|max:255',
            'email' => 'required|email|max:255|unique:client_courriels',
            'billing_address' => 'required',
            'billing_address.street' => 'required|max:255',
            'billing_address.city' => 'required|max:255',
            'billing_address.province' => 'required|max:255',
            'billing_address.zipcode' => 'required|max:255',
            'billing_address.country' => 'required|max:255',
            'billing_address.postal_address' => 'required|min:3|max:1000',
            'same_as_billing_address' => 'required|boolean',
            'service_address' => 'sometimes',
            'service_address.street' => 'sometimes|max:255',
            'service_address.city' => 'sometimes|max:255',
            'service_address.province' => 'sometimes|max:255',
            'service_address.zipcode' => 'sometimes|max:255',
            'service_address.country' => 'sometimes|max:255',
            'service_address.postal_address' => 'sometimes|min:3|max:1000',
        ]);
    }

    /**
     * @OA\Get(path="/clients/{uuid}",
     *   tags={"Clients"},
     *   summary="Affichage d'un client",
     *   description="Affichage d'un client",
     *   operationId="getClient",
     *   @OA\Parameter(
     *     name="uuid",
     *     required=true,
     *     in="path",
     *     description="L'uuid du client",
     *     @OA\Schema(
     *          type="string",
     *          example="CU-010000"
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
     *                      description="Données d'un client",
     *                      @OA\Property(
     *                          property="numero_saisi",
     *                          type="string",
     *                          description="Numéro à 6 chiffres du client",
     *                          example="010000"
     *                      ),
     *                      @OA\Property(
     *                          property="uuid",
     *                          type="string",
     *                          description="Uuid du client",
     *                          example="CU-010000"
     *                      ),
     *                      @OA\Property(
     *                          property="titre",
     *                          type="object",
     *                          description="Titre du client",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=2
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                              description="Nom du titre",
     *                              example="Mme."
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          property="prenom",
     *                          type="string",
     *                          description="Prénom du client",
     *                          example="Fabien"
     *                      ),
     *                      @OA\Property(
     *                          property="nom",
     *                          type="string",
     *                          description="Nom du client",
     *                          example="Lagrange"
     *                      ),
     *                      @OA\Property(
     *                          property="nom_complet",
     *                          type="string",
     *                          description="Nom complet du client",
     *                          example="Fabien Lagrange"
     *                      ),
     *                      @OA\Property(
     *                          property="company",
     *                          type="object",
     *                          description="Compagnie du client",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=15
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                              description="Nom de la compagnie",
     *                              example="Metalo"
     *                          ),
     *                          @OA\Property(
     *                              property="types",
     *                              type="array",
     *                              description="Types de compagnie",
     *                              @OA\Items(
     *                                  type="object",
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="ID",
     *                                      example=7
     *                                  ),
     *                                  @OA\Property(
     *                                      property="name",
     *                                      type="string",
     *                                      description="Nom du type de compagnie",
     *                                      example="Revêtement"
     *                                  )
     *                              )
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          property="cellulaire",
     *                          type="object",
     *                          description="Céllulaire du client",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=4
     *                          ),
     *                          @OA\Property(
     *                              property="numero",
     *                              type="string",
     *                              description="Numéro du cellulaire",
     *                              example="604-221-1023"
     *                          ),
     *                          @OA\Property(
     *                              property="send_sms",
     *                              type="integer",
     *                              description="Envoyer ou non du sms au cellulaire",
     *                              example=1
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          type="array",
     *                          property="telephones",
     *                          @OA\Items(
     *                              type="object",
     *                              @OA\Property(
     *                                  property="id",
     *                                  type="integer",
     *                                  description="ID",
     *                                  example=6
     *                              ),
     *                              @OA\Property(
     *                                  property="numero",
     *                                  type="string",
     *                                  description="Numéro du téléphone",
     *                                  example="250-218-8608"
     *                              ),
     *                              @OA\Property(
     *                                  property="type",
     *                                  type="object",
     *                                  description="Type de numéro du téléphone",
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="ID",
     *                                      example=1
     *                                  ),
     *                                  @OA\Property(
     *                                      property="name",
     *                                      type="string",
     *                                      description="Type de numéro",
     *                                      example="Maison"
     *                                  )
     *                              )
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          type="array",
     *                          property="courriels",
     *                          @OA\Items(
     *                              type="object",
     *                              @OA\Property(
     *                                  property="id",
     *                                  type="integer",
     *                                  description="ID",
     *                                  example=6
     *                              ),
     *                              @OA\Property(
     *                                  property="email",
     *                                  type="string",
     *                                  description="Email",
     *                                  example="fabien.lagrange@temporary-mail.net"
     *                              )
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          property="billing_address",
     *                          type="object",
     *                          description="Adresse de facturation du client",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=7
     *                          ),
     *                          @OA\Property(
     *                              property="street",
     *                              type="string",
     *                              description="Rue",
     *                              example="3691 rue Fournier"
     *                          ),
     *                          @OA\Property(
     *                              property="city",
     *                              type="string",
     *                              description="Ville",
     *                              example="St Jerome"
     *                          ),
     *                          @OA\Property(
     *                              property="province",
     *                              type="string",
     *                              description="Province",
     *                              example="Quebec"
     *                          ),
     *                          @OA\Property(
     *                              property="zipcode",
     *                              type="string",
     *                              description="Code postal",
     *                              example="J7Z 4V1"
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
     *                              description="Note interne de l'adresse de facturation",
     *                              example="Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression"
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          property="service_addresses",
     *                          type="array",
     *                          description="Adresses de service du client",
     *                          @OA\Items(
     *                              type="object",
     *                              @OA\Property(
     *                                  property="id",
     *                                  type="integer",
     *                                  description="ID",
     *                                  example=8
     *                              ),
     *                              @OA\Property(
     *                                  property="street",
     *                                  type="string",
     *                                  description="Rue",
     *                                  example="147 Scarth Street"
     *                              ),
     *                              @OA\Property(
     *                                  property="city",
     *                                  type="string",
     *                                  description="Ville",
     *                                  example="Montreal"
     *                              ),
     *                              @OA\Property(
     *                                  property="province",
     *                                  type="string",
     *                                  description="Province",
     *                                  example="Quebec"
     *                              ),
     *                              @OA\Property(
     *                                  property="zipcode",
     *                                  type="string",
     *                                  description="Code postal",
     *                                  example="S4P 3Y2"
     *                              ),
     *                              @OA\Property(
     *                                  property="country",
     *                                  type="string",
     *                                  description="Pays",
     *                                  example="Canada"
     *                              ),
     *                              @OA\Property(
     *                                  property="note_interne",
     *                                  type="string",
     *                                  description="Note interne de l'adresse de service",
     *                                  example="De nombreuses suites logicielles de mise en page ou éditeurs de sites Web ont fait du Lorem Ipsum leur faux texte par défaut"
     *                              )
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          type="array",
     *                          property="tags",
     *                          description="Etiquettes du client",
     *                          @OA\Items(
     *                              type="object",
     *                              @OA\Property(
     *                                  property="id",
     *                                  type="integer",
     *                                  description="ID",
     *                                  example=6
     *                              ),
     *                              @OA\Property(
     *                                  property="name",
     *                                  type="string",
     *                                  description="Nom de l'étiquette",
     *                                  example="Etiquette 1"
     *                              )
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          type="array",
     *                          property="pieces_jointes",
     *                          description="Pièces jointes du client",
     *                          @OA\Items(
     *                              type="object",
     *                              @OA\Property(
     *                                  property="id",
     *                                  type="integer",
     *                                  description="ID",
     *                                  example=6
     *                              ),
     *                              @OA\Property(
     *                                  property="photo_url",
     *                                  type="string",
     *                                  description="Url de la pièce jointe",
     *                                  example="http://localhost:2023/storage/uploads/clients/docx/8/ny-fiainana-araka-ny-fanahy.docx"
     *                              )
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          property="note_interne",
     *                          type="string",
     *                          description="Note interne du client",
     *                          example="On sait depuis longtemps que travailler avec du texte lisible et contenant du sens est source de distractions, et empêche de se concentrer sur la mise en page elle-même"
     *                      ),
     *                      @OA\Property(
     *                          property="client_recurrent",
     *                          type="integer",
     *                          description="Client récurrent ou non",
     *                          example=0
     *                      ),
     *                      @OA\Property(
     *                          property="payment_term",
     *                          type="object",
     *                          description="Termes de paiement du client",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=4
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                              description="Nom du terme de paiement",
     *                              example="Net 30 jours"
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
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        return new ClientResource($client);
        // return fractal($client, new ClientTransformer())->respond();
    }

    /**
     * @OA\Get(path="/clients/{uuid}/service_addresses",
     *   tags={"Demandes"},
     *   summary="Liste des adresses de service d'un client",
     *   description="Liste des adresses de service d'un client",
     *   operationId="clientServiceAddresses",
     *   @OA\Parameter(
     *     name="uuid",
     *     required=true,
     *     in="path",
     *     description="L'uuid du client",
     *     @OA\Schema(
     *          type="string",
     *          example="CU-010000"
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
     *                      type="array",
     *                      description="Données des addresses de service",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=8
     *                          ),
     *                          @OA\Property(
     *                              property="street",
     *                              type="string",
     *                              description="Rue",
     *                              example="147 Scarth Street"
     *                          ),
     *                          @OA\Property(
     *                              property="city",
     *                              type="string",
     *                              description="Ville",
     *                              example="Montreal"
     *                          ),
     *                          @OA\Property(
     *                              property="province",
     *                              type="string",
     *                              description="Province",
     *                              example="Quebec"
     *                          ),
     *                          @OA\Property(
     *                              property="zipcode",
     *                              type="string",
     *                              description="Code postal",
     *                              example="S4P 3Y2"
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
     *                              description="Note interne de l'adresse de service",
     *                              example="De nombreuses suites logicielles de mise en page ou éditeurs de sites Web ont fait du Lorem Ipsum leur faux texte par défaut"
     *                          ),
     *                          @OA\Property(
     *                              property="postal_address",
     *                              type="string",
     *                              description="Adresse postale l'adresse de service",
     *                              example="2466 Boulevard Laurier, Québec, QC, Canada"
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="total",
     *                      type="integer",
     *                      description="Nombre total",
     *                      example=4
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
     * @return \Illuminate\Http\Response
     */
    public function getServiceAddresses(Request $request, Client $uuid)
    {
        $serviceAddresses = $uuid->service_addresses()->get(['id', 'postal_address']);

        return response()->json([
            'data' => $serviceAddresses,
            'total' => $serviceAddresses->count(),
        ]);
    }

    /**
     * @OA\Put(path="/clients/{uuid}",
     *   tags={"Clients"},
     *   summary="Modification d'un client",
     *   description="Modification d'un client",
     *   operationId="clientUpdate",
     *   @OA\Parameter(
     *     name="uuid",
     *     required=true,
     *     in="path",
     *     description="L'uuid du client",
     *     @OA\Schema(
     *          type="string",
     *          example="CU-010008"
     *     )
     *   ),
     *   @OA\RequestBody(
     *       required=true,
     *       description="Paramètres de modification d'un client",
     *       @OA\JsonContent(
     *          type="object",
     *          required={"client_titre_id", "prenom", "nom", "client_recurrent", "client_payment_term_id", "cellulaire", "courriels", "billing_address", "same_as_billing_address"},
     *          @OA\Property(
     *              property="client_titre_id",
     *              type="integer",
     *              description="Identifiant du titre M. ou Mme.",
     *              example=1
     *          ),
     *          @OA\Property(
     *              property="prenom",
     *              type="string",
     *              description="Prénom du client",
     *              example="Fabien"
     *          ),
     *          @OA\Property(
     *              property="nom",
     *              type="string",
     *              description="Nom du client",
     *              example="Lagarde"
     *          ),
     *          @OA\Property(
     *              property="company",
     *              type="object",
     *              description="Compagnie où travaille le client",
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  description="Nom de la compagnie",
     *                  example="Proxima"
     *              ),
     *              @OA\Property(
     *                  property="client_company_type_id",
     *                  type="array",
     *                  description="Identifiants du type de compagnie",
     *                  @OA\Items(
     *                      type="integer",
     *                      example=3
     *                  )
     *              )
     *          ),
     *          @OA\Property(
     *              property="client_recurrent",
     *              type="boolean",
     *              description="Client récurrent",
     *              example=false
     *          ),
     *          @OA\Property(
     *              property="client_payment_term_id",
     *              type="integer",
     *              description="Identifiant du terme de paiement",
     *              example=4
     *          ),
     *          @OA\Property(
     *              property="tags",
     *              type="array",
     *              description="Les étiquettes du client",
     *              @OA\Items(
     *                  type="string",
     *                  example="Gouttières"
     *              )
     *          ),
     *          @OA\Property(
     *              property="cellulaire",
     *              type="object",
     *              description="Céllulaire du client",
     *              required={"numero", "send_sms"},
     *              @OA\Property(
     *                  property="numero",
     *                  type="string",
     *                  description="Numéro du cellulaire",
     *                  example="604-221-1023"
     *              ),
     *              @OA\Property(
     *                  property="send_sms",
     *                  type="boolean",
     *                  description="Envoyer des sms à ce céllulaire ou pas",
     *                  example=true
     *              )
     *          ),
     *          @OA\Property(
     *              property="telephones",
     *              type="array",
     *              description="Autres téléphones du client",
     *              @OA\Items(
     *                  type="object",
     *                  required={"numero", "client_numero_type_id"},
     *                  @OA\Property(
     *                      property="numero",
     *                      type="string",
     *                      description="Numéro du téléphone",
     *                      example="250-218-8608"
     *                  ),
     *                  @OA\Property(
     *                      property="client_numero_type_id",
     *                      type="integer",
     *                      description="Type de numéro du téléphone",
     *                      example=1
     *                  )
     *              )
     *          ),
     *          @OA\Property(
     *              property="courriels",
     *              type="array",
     *              description="Adresses email du client",
     *              @OA\Items(
     *                  type="object",
     *                  required={"email"},
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      description="Email",
     *                      example="fabien.lagarde@proxima.ca"
     *                  )
     *              )
     *          ),
     *          @OA\Property(
     *              property="note_interne",
     *              type="string",
     *              description="Note interne du client",
     *              example="On sait depuis longtemps que travailler avec du texte lisible et contenant du sens est source de distractions, et empêche de se concentrer sur la mise en page elle-même"
     *          ),
     *          @OA\Property(
     *              property="billing_address",
     *              type="object",
     *              description="Adresse de facturation",
     *              required={"street", "city", "province", "zipcode", "country"},
     *              @OA\Property(
     *                  property="street",
     *                  type="string",
     *                  description="Rue",
     *                  example="3691 rue Fournier"
     *              ),
     *              @OA\Property(
     *                  property="city",
     *                  type="string",
     *                  description="Ville",
     *                  example="St Jerome"
     *              ),
     *              @OA\Property(
     *                  property="province",
     *                  type="string",
     *                  description="Province",
     *                  example="Quebec"
     *              ),
     *              @OA\Property(
     *                  property="zipcode",
     *                  type="string",
     *                  description="Code postal",
     *                  example="J7Z 4V1"
     *              ),
     *              @OA\Property(
     *                  property="country",
     *                  type="string",
     *                  description="Pays",
     *                  example="Canada"
     *              ),
     *              @OA\Property(
     *                  property="note_interne",
     *                  type="string",
     *                  description="Note interne de l'adresse de facturation",
     *                  example="Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression"
     *              ),
     *              @OA\Property(
     *                  property="postal_address",
     *                  type="string",
     *                  description="Adresse postale obtenue avec l'API Google",
     *                  example="2468 Rue Monseigneur Laflèche, Québec, QC, Canada"
     *              )
     *          ),
     *          @OA\Property(
     *              property="service_addresses",
     *              type="array",
     *              description="Adresses de service du client si différente de l'adresse de facturation",
     *              @OA\Items(
     *                  type="object",
     *                  required={"street", "city", "province", "zipcode", "country"},
     *                  @OA\Property(
     *                      property="street",
     *                      type="string",
     *                      description="Rue",
     *                      example="147 Scarth Street"
     *                  ),
     *                  @OA\Property(
     *                      property="city",
     *                      type="string",
     *                      description="Ville",
     *                      example="Montreal"
     *                  ),
     *                  @OA\Property(
     *                      property="province",
     *                      type="string",
     *                      description="Province",
     *                      example="Quebec"
     *                  ),
     *                  @OA\Property(
     *                      property="zipcode",
     *                      type="string",
     *                      description="Code postal",
     *                      example="S4P 3Y2"
     *                  ),
     *                  @OA\Property(
     *                      property="country",
     *                      type="string",
     *                      description="Pays",
     *                      example="Canada"
     *                  ),
     *                  @OA\Property(
     *                      property="note_interne",
     *                      type="string",
     *                      description="Note interne de l'adresse de service",
     *                      example="De nombreuses suites logicielles de mise en page ou éditeurs de sites Web ont fait du Lorem Ipsum leur faux texte par défaut"
     *                  ),
     *                  @OA\Property(
     *                      property="postal_address",
     *                      type="string",
     *                      description="Adresse postale obtenue avec l'API Google",
     *                      example="2468 Boulevard Père-Lelièvre, Québec, QC, Canada"
     *                  )
     *              )
     *          ),
     *          @OA\Property(
     *              property="pieces_jointes",
     *              type="array",
     *              description="Pièces jointes",
     *              @OA\Items(
     *                  type="object",
     *                  required={"url", "file_name"},
     *                  @OA\Property(
     *                      property="file_name",
     *                      type="string",
     *                      description="Nom du fichier joint",
     *                      example="La joie de vivre.docx"
     *                  ),
     *                  @OA\Property(
     *                      property="url",
     *                      type="string",
     *                      description="Chaîne de caractères base64 de la pièce jointe",
     *                      example="UEsDBBQACAgIAKp+dVIAAAAAAAAAAAAAAAARAAAAZG9jUHJvcHMvY29yZS54bWx9Ul1PwjAUffdXLH3f2m6A0IyRqOFJEqMQjW+1u0Bx65q2fP17u8EmKvHt3nNOz/1qOjmURbADY2WlxohGBAWgRJVLtRqjxXwaDlFgHVc5LyoFY..."
     *                  )
     *              )
     *          )
     *       )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Modification réussie du client",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="status",
     *                         type="string",
     *                         description="Message de confirmation",
     *                         example="Le client a été modifié avec succès."
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
     *                              property="cellulaire.numero",
     *                              type="array",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="Le champ cellulaire.numero est obligatoire."
     *                              )
     *                          ),
     *                          @OA\Property(
     *                              property="pieces_jointes.0.url",
     *                              type="array",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="Le pieces_jointes.0.url doit être une chaîne Base64 valide."
     *                              )
     *                         )
     *                     )
     *                 )
     *             )
     *        }
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
     * Handle a client update request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Client $client
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Client $client)
    {
        $validator = $this->validator($request->all(), $client);

        if($validator->fails()) {
            return response(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $client = $this->createClient($request->all(), 'update_mode', $client);

        return response()->json([
            'message' => trans('client.updated'),
        ]);
    }

    /**
     * @OA\Post(path="/clients/{uuid}/add_service_address",
     *   tags={"Demandes"},
     *   summary="Ajout d'une adresse de service à un client",
     *   description="Ajout d'une adresse de service à un client",
     *   operationId="addServiceAddressToClient",
     *   @OA\Parameter(
     *     name="uuid",
     *     required=true,
     *     in="path",
     *     description="L'uuid du client",
     *     @OA\Schema(
     *         type="string",
     *         example="CU-010000"
     *     )
     *   ),
     *   @OA\RequestBody(
     *       required=true,
     *       description="Paramètres de modification d'un client",
     *       @OA\JsonContent(
     *          type="object",
     *          required={"street", "city", "province", "zipcode", "country", "postal_address"},
     *          @OA\Property(
     *              property="street",
     *              type="string",
     *              description="Rue",
     *              example="147 Scarth Street"
     *          ),
     *          @OA\Property(
     *              property="city",
     *              type="string",
     *              description="Ville",
     *              example="Montreal"
     *          ),
     *          @OA\Property(
     *              property="province",
     *              type="string",
     *              description="Province",
     *              example="Quebec"
     *          ),
     *          @OA\Property(
     *              property="zipcode",
     *              type="string",
     *              description="Code postal",
     *              example="S4P 3Y2"
     *          ),
     *          @OA\Property(
     *              property="country",
     *              type="string",
     *              description="Pays",
     *              example="Canada"
     *          ),
     *          @OA\Property(
     *              property="note_interne",
     *              type="string",
     *              description="Note interne de l'adresse de service",
     *              example="De nombreuses suites logicielles de mise en page ou éditeurs de sites Web ont fait du Lorem Ipsum leur faux texte par défaut"
     *          ),
     *          @OA\Property(
     *              property="postal_address",
     *              type="string",
     *              description="Adresse postale obtenue avec l'API Google",
     *              example="2468 Boulevard Père-Lelièvre, Québec, QC, Canada"
     *          )
     *       )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Modification réussie du client",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="status",
     *                         type="string",
     *                         description="Message de confirmation",
     *                         example="Le client a été modifié avec succès."
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
     *                              property="street",
     *                              type="array",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="Le champ street est obligatoire."
     *                              )
     *                          ),
     *                          @OA\Property(
     *                              property="city",
     *                              type="array",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="Le champ ville est obligatoire."
     *                              )
     *                         )
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
     * Handle a client update request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Client $client
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function addServiceAddress(Request $request, Client $uuid)
    {
        $validator = $this->validateAddress($request->all());

        if($validator->fails()) {
            return response(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $service_address = Address::create([
            'street' => $request->street,
            'city' => $request->city,
            'province' => $request->province,
            'zipcode' => $request->zipcode,
            'country' => $request->country,
            'note_interne' => $request->note_interne,
            'postal_address' => $request->postal_address,
        ]);
        $uuid->service_addresses()->save($service_address);

        return response()->json([
            'message' => trans('client.updated'),
        ]);
    }

    protected function validateAddress(array $data)
    {
        return Validator::make($data, [
            'street' => 'required|max:255',
            'city' => 'required|max:255',
            'province' => 'required|max:255',
            'zipcode' => 'required|max:255',
            'country' => 'required|max:255',
            'note_interne' => 'sometimes|min:3|max:1000',
            'postal_address' => 'required|min:3|max:1000',
        ]);
    }

    public function destroy(Client $client)
    {
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, Client $client = null)
    {
        if($client) {
            $clientEmails = $client->courriels()->pluck('email')->toArray();
            $uniqueEmailRule = Rule::unique('client_courriels')->whereNotIn('email', $clientEmails);
            // $uniqueNumeroSaisiRule = Rule::unique('clients')->ignore($client->id);
        } else {
            $uniqueEmailRule = Rule::unique('client_courriels');
            $uniqueNumeroSaisiRule = Rule::unique('clients');
        }

        return Validator::make($data, [
            'numero_saisi' => !$client ? ['required', "digits:6", $uniqueNumeroSaisiRule] : [],
            'client_titre_id' => 'required|integer',
            'prenom' => 'required|max:255',
            'nom' => 'required|max:255',
            'company' => 'sometimes',
            'company.name' => 'sometimes|max:255',
            'company.client_company_type_id' => 'sometimes',
            'company.client_company_type_id.*' => 'sometimes|integer',
            'client_recurrent' => 'required|boolean',
            'client_payment_term_id' => 'required|integer',
            'tags' => 'sometimes',
            'tags.*' => 'sometimes|max:255',
            'cellulaire' => 'required',
            'cellulaire.numero' => 'required|max:255',
            'cellulaire.send_sms' => 'required|boolean',
            'telephones' => 'sometimes',
            'telephones.*.numero' => 'sometimes|max:255',
            'telephones.*.client_numero_type_id' => 'sometimes|integer',
            'courriels' => 'required',
            'courriels.*.email' => ['required', 'email:rfc,dns', 'max:255', $uniqueEmailRule],
            // 'courriels.*.email' => 'required|email|max:255|unique:client_courriels',
            'pieces_jointes' => 'sometimes',
            'pieces_jointes.*.url' => 'sometimes|base64',
            'pieces_jointes.*.file_name' => 'sometimes|max:255',
            'billing_address' => 'required',
            'billing_address.street' => 'required|max:255',
            'billing_address.city' => 'required|max:255',
            'billing_address.province' => 'required|max:255',
            'billing_address.zipcode' => 'required|max:255',
            'billing_address.country' => 'required|max:255',
            'billing_address.postal_address' => 'required|min:3|max:1000',
            'billing_address.note_interne' => 'nullable|sometimes|min:3|max:1000',
            'same_as_billing_address' => !$client ? 'required|boolean' : '',
            'note_interne' => 'nullable|sometimes|min:3|max:1000',
            'service_address_note_interne' => 'nullable|sometimes|min:3|max:1000',
            'service_addresses' => 'sometimes',
            'service_addresses.*.street' => 'sometimes|max:255',
            'service_addresses.*.city' => 'sometimes|max:255',
            'service_addresses.*.province' => 'sometimes|max:255',
            'service_addresses.*.zipcode' => 'sometimes|max:255',
            'service_addresses.*.country' => 'sometimes|max:255',
            'service_addresses.*.postal_address' => 'sometimes|min:3|max:1000',
            'service_addresses.*.note_interne' => 'nullable|sometimes|min:3|max:1000',
        ]);
    }

    /**
     * Create user.
     *
     * @param  array  $data
     * @return User   $user
     */
    protected function createClient(array $data, $mode = 'create_mode', Client $client = null)
    {
        /**
         * @var Client $client
         */
        $client = Client::updateOrCreate([
            "id" => $client ? $client->id : null,
        ], $mode == 'create_mode' ? [
            "numero_saisi" => $data['numero_saisi'],
            "client_titre_id" => $data['client_titre_id'],
            "prenom" => $data['prenom'],
            "nom" => $data['nom'],
            "client_recurrent" => $data['client_recurrent'],
            "client_payment_term_id" => $data['client_payment_term_id'],
            "note_interne" => !empty($data['note_interne']) ? $data['note_interne'] : "",
            "same_as_billing_address" => $data['same_as_billing_address'],
        ] : [
            "client_titre_id" => $data['client_titre_id'],
            "prenom" => $data['prenom'],
            "nom" => $data['nom'],
            "client_recurrent" => $data['client_recurrent'],
            "client_payment_term_id" => $data['client_payment_term_id'],
            "note_interne" => !empty($data['note_interne']) ? $data['note_interne'] : "",
        ]);

        if(!empty($data['company'])) {
            /**
             * @var ClientCompany $client_company
             */
            $client_company = ClientCompany::updateOrCreate([
                'client_id' => $client->id,
            ], [
                'name' => $data['company']['name'],
            ]);

            $client_company->company_types()->sync([]);

            if(!empty($data['company']['client_company_type_id'])) {
                $client_company->company_types()->sync($data['company']['client_company_type_id']);
            }
        } else {
            $client_company = $client->company()->delete();
        }

        $client->tags()->sync([]);

        if(!empty($data['tags'])) {
            $tagIds = [];
            foreach ($data['tags'] as $strTag) {
                $tag = Tag::firstOrCreate([
                    'name' => $strTag,
                ], [
                    'name' => $strTag,
                ]);
                $tagIds[] = $tag->id;
            }
            $client->tags()->sync($tagIds);
        }

        ClientCellulaire::updateOrCreate([
            'client_id' => $client->id,
        ], [
            'numero' => $data['cellulaire']['numero'],
            'send_sms' => $data['cellulaire']['send_sms'],
        ]);

        $client->telephones()->delete();

        if(!empty($data['telephones'])) {
            foreach ($data['telephones'] as $telephone) {
                ClientTelephone::create([
                    'client_id' => $client->id,
                    'numero' => $telephone['numero'],
                    'client_numero_type_id' => $telephone['client_numero_type_id'],
                ]);
            }
        }

        $client->courriels()->delete();

        foreach ($data['courriels'] as $courriel) {
            ClientCourriel::create([
                'client_id' => $client->id,
                'email' => $courriel['email'],
            ]);
        }

        if($mode == 'create_mode') {
            $billing_address = Address::create([
                'street' => $data['billing_address']['street'],
                'city' => $data['billing_address']['city'],
                'province' => $data['billing_address']['province'],
                'zipcode' => $data['billing_address']['zipcode'],
                'country' => $data['billing_address']['country'],
                'note_interne' => !empty($data['billing_address']['note_interne']) ? $data['billing_address']['note_interne'] : "",
                'postal_address' => $data['billing_address']['postal_address'],
            ]);
            $client->billing_address()->save($billing_address);

            if($data['same_as_billing_address']) {
                $service_address = Address::create([
                    'street' => $billing_address->street,
                    'city' => $billing_address->city,
                    'province' => $billing_address->province,
                    'zipcode' => $billing_address->zipcode,
                    'country' => $billing_address->country,
                    'note_interne' => !empty($data['service_address_note_interne']) ? $data['service_address_note_interne'] : "",
                    'postal_address' => $billing_address->postal_address,
                ]);
                $client->service_addresses()->save($service_address);
            } else {
                if(!empty($data['service_addresses'])) {
                    $aServiceAdresses = [];
                    foreach ($data['service_addresses'] as $sa) {
                        $service_address = Address::create([
                            'street' => $sa['street'],
                            'city' => $sa['city'],
                            'province' => $sa['province'],
                            'zipcode' => $sa['zipcode'],
                            'country' => $sa['country'],
                            'note_interne' => !empty($sa['note_interne']) ? $sa['note_interne'] : "",
                            'postal_address' => $sa['postal_address'],
                        ]);
                        $aServiceAdresses[] = $service_address;
                    }
                    $client->service_addresses()->saveMany($aServiceAdresses);
                }
            }
        } else {

            $client->billing_address()->delete();

            $billing_address = Address::create([
                'street' => $data['billing_address']['street'],
                'city' => $data['billing_address']['city'],
                'province' => $data['billing_address']['province'],
                'zipcode' => $data['billing_address']['zipcode'],
                'country' => $data['billing_address']['country'],
                'note_interne' => !empty($data['billing_address']['note_interne']) ? $data['billing_address']['note_interne'] : "",
                'postal_address' => $data['billing_address']['postal_address'],
            ]);
            $client->billing_address()->save($billing_address);

            $client->service_addresses()->delete();

            if(!empty($data['service_addresses'])) {
                $aServiceAdresses = [];
                foreach ($data['service_addresses'] as $sa) {
                    $service_address = Address::create([
                        'street' => $sa['street'],
                        'city' => $sa['city'],
                        'province' => $sa['province'],
                        'zipcode' => $sa['zipcode'],
                        'country' => $sa['country'],
                        'note_interne' => !empty($sa['note_interne']) ? $sa['note_interne'] : "",
                        'postal_address' => $sa['postal_address'],
                    ]);
                    $aServiceAdresses[] = $service_address;
                }
                $client->service_addresses()->saveMany($aServiceAdresses);
            }
        }

        $client->pieces_jointes()->delete();

        if(!empty($data['pieces_jointes'])) {
            $aAttachements = [];
            foreach ($data['pieces_jointes'] as $pieces_jointe) {
                $filename =  $this->uploadFile($pieces_jointe, 'clients');
                $pj = Photo::create([
                    'url' => $filename,
                ]);
                $aAttachements[] = $pj;
            }
            $client->pieces_jointes()->saveMany($aAttachements);
        }

        return $client;
    }
}
