<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Company;
use App\Models\CompanyDomaine;
use App\Models\CompanySuccursale;
use App\Models\Photo;
use App\Models\Region;
use App\Models\SocialNetwork;
use App\Models\User;
use App\Support\UploadBase64Trait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    use RegistersUsers, UploadBase64Trait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @OA\Post(path="/register",
     *   tags={"Inscription"},
     *   summary="Inscription d'un utilisateur",
     *   description="Inscription d'un utilisateur",
     *   operationId="userRegistration",
     *   @OA\RequestBody(
     *       required=true,
     *       description="Paramètres d'inscription d'un utilisateur",
     *       @OA\JsonContent(
     *          type="object",
     *          required={"company_name", "email", "password", "password_confirmation", "payment_method", "plan"},
     *          @OA\Property(
     *              property="company_name",
     *              type="string",
     *              description="Nom de la compagnie où l'utilisateur travaille",
     *              example="Alura"
     *          ),
     *          @OA\Property(
     *              property="email",
     *              type="string",
     *              description="Email",
     *              example="math@gmail.com"
     *          ),
     *          @OA\Property(
     *              property="password",
     *              type="string",
     *              description="Mot de passe",
     *              example="Math2021!"
     *          ),
     *          @OA\Property(
     *              property="password_confirmation",
     *              type="string",
     *              description="Confirmation du mot de passe",
     *              example="Math2021!"
     *          ),
     *          @OA\Property(
     *              property="payment_method",
     *              type="string",
     *              description="Moyen de paiement",
     *              example="pm_1IpCtsL1HC1QKC81m2mqd8l3"
     *          ),
     *          @OA\Property(
     *              property="plan",
     *              type="string",
     *              description="Identifiant unique du plan d'abonnement",
     *              example="gutter_runner_monthly_forfait_gestionnaire"
     *          )
     *       )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Envoi d'email de vérification et abonnement réussi à un plan Stripe",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="status",
     *                         type="string",
     *                         description="Message de confirmation",
     *                         example="Un courriel de vérification d'adresse email vous a été envoyé."
     *                     ),
     *                     @OA\Property(
     *                          property="subscription",
     *                          type="object",
     *                          description="Détails de l'abonnement",
     *                              @OA\Property(
     *                                  property="name",
     *                                  type="string",
     *                                  description="Nom de l'abonnement",
     *                                  example="default"
     *                              ),
     *                              @OA\Property(
     *                                  property="stripe_id",
     *                                  type="string",
     *                                  description="Identifiant unique de l'abonnement",
     *                                  example="sub_JS52aO9Xa7QhWw"
     *                              ),
     *                              @OA\Property(
     *                                  property="stripe_status",
     *                                  type="string",
     *                                  description="Statut de l'abonnement",
     *                                  example="active"
     *                              ),
     *                              @OA\Property(
     *                                  property="stripe_plan",
     *                                  type="string",
     *                                  description="Identifiant du plan d'abonnement",
     *                                  example="gutter_runner_monthly_forfait_gestionnaire"
     *                              ),
     *                              @OA\Property(
     *                                  property="quantity",
     *                                  type="integer",
     *                                  description="Nombre d'unités de produits",
     *                                  example=1
     *                              ),
     *                              @OA\Property(
     *                                  property="trial_ends_at",
     *                                  type="string",
     *                                  description="Date de fin d'essai de l'abonnement",
     *                                  example=null
     *                              ),
     *                              @OA\Property(
     *                                  property="ends_at",
     *                                  type="string",
     *                                  description="Date de fin de l'abonnement",
     *                                  example=null
     *                              ),
     *                              @OA\Property(
     *                                  property="user_id",
     *                                  type="integer",
     *                                  description="Identifiant de l'utilisateur abonné",
     *                                  example=12
     *                              ),
     *                              @OA\Property(
     *                                  property="updated_at",
     *                                  type="string",
     *                                  description="Date de mis à jour de l'abonnement",
     *                                  example="2021-05-09T11:32:30.000000Z"
     *                              ),
     *                              @OA\Property(
     *                                  property="created_at",
     *                                  type="string",
     *                                  description="Date de création de l'abonnement",
     *                                  example="2021-04-28T21:21:35.000000Z"
     *                              ),
     *                              @OA\Property(
     *                                  property="id",
     *                                  type="integer",
     *                                  description="Identifiant de l'abonnement dans la base de données",
     *                                  example=2
     *                              )
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
     *                              property="email",
     *                              type="array",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="Le champ adresse email doit être une adresse email valide."
     *                              )
     *                          ),
     *                          @OA\Property(
     *                              property="password",
     *                              type="array",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="Le champ de confirmation mot de passe ne correspond pas."
     *                              )
     *                         )
     *                     )
     *                 )
     *             )
     *        }
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Cartes invalides ou autres erreurs",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="status",
     *                         type="string",
     *                         example="Your card has insufficient funds."
     *                     )
     *                 )
     *             )
     *         }
     *   )
     * )
     * 
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if($validator->fails()) {
            return response(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $paymentMethodId = $request->payment_method;
        $planId = $request->plan;

        $user = User::where('email', $request->email)->first();

        if($user) {
            $stripeCustomer = $user->createOrGetStripeCustomer();
            if ($user->subscribedToPlan($planId, 'default')) {
                return response()->json([
                    'message' => trans('registration.already_subscribed'),
                ], Response::HTTP_BAD_REQUEST);
            }
        } else {
            $user = $this->create($request->all());
            $stripeCustomer = $user->createOrGetStripeCustomer();
        }

        $this->guard()->login($user);

        try {
            if (!$user->hasPaymentMethod()) {
                $user->addPaymentMethod($paymentMethodId);
            }

            // $user->updateDefaultPaymentMethod($paymentMethodId);

            // $user->updateDefaultPaymentMethodFromStripe();

            $subscription = $user->newSubscription('default', $planId)->create($paymentMethodId, [
                'email' => $user->email
            ]);

            event(new Registered($user));

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($response = $this->registered($user, $subscription)) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], Response::HTTP_CREATED)
                    : redirect($this->redirectPath());
    }

    /**
     * The user has been registered.
     *
     * @param  User  $user
     * @param  mixed  $subscription
     * @return \Illuminate\Http\JsonResponse
     */
    protected function registered(User $user, $subscription)
    {
        if ($user instanceof MustVerifyEmail) {
            return response()->json([
                'message' => trans('verification.sent'),
                'subscription' => $subscription,
            ]);
        }

        return response()->json($user);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $uniqueEmailRule = Rule::unique('users', 'email');
        return Validator::make($data, [
            'email' => ['required', 'email:rfc,dns', 'max:255', $uniqueEmailRule],
            'password' => 'required|min:6|pwned|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/', // at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character
            'password_confirmation' => 'required|min:6',
            'company_name' => 'required|max:255',
            'payment_method' => 'required|max:255',
            'plan' => 'required|max:255',
        ]);
    }

    /**
     * Create user.
     *
     * @param  array  $data
     * @return User   $user
     */
    protected function create(array $data)
    {
        $company = Company::create([
            'nom' => $data['company_name'],
        ]);

        $user = User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'remember_token' => bcrypt(Str::random(10)),
            'company_id' => $company->id,
        ]);

        return $user;
    }
}
