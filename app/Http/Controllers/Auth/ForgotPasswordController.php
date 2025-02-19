<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Response;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

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
     * 
     * @OA\Post(path="/password/email",
     *   tags={"Authentification"},
     *   summary="Envoi du lien de réinitialisation du mot de passe",
     *   description="Mot de passe oublié, email de réinitialisation",
     *   operationId="sendResetLinkEmail",
     *   @OA\Parameter(
     *     name="email",
     *     required=true,
     *     in="query",
     *     description="Adresse email",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Envoi d'email réussi",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="status",
     *                         type="string",
     *                         description="Message de confirmation d'envoi d'email"
     *                     ),
     *                     example={
     *                         "status": "Un courriel de réinitialisation vous a été envoyé à rijalaraz@gmail.com. <i>Il se pourrait qu'il soit dans votre dossier de courriels indésirables</i>."
     *                     }
     *                 )
     *             )
     *         }
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Utilisateur inexistant",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         description="Message d'échec d'envoi d'email"
     *                     ),
     *                     example={
     *                         "email": "Aucun utilisateur n'a été trouvé avec cette adresse email."
     *                     }
     *                 )
     *             )
     *         }
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Adresse email invalide",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Données invalides",
     *                         example="The given data was invalid."
     *                     ),
     *                     @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         @OA\Property(
     *                             property="email",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="string",
     *                                 example="Le champ adresse email doit être une adresse email valide."
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         }
     *   )
     * )
     * 
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return ['message' => trans($response, $this->credentials($request))];
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->json(['email' => trans($response)], Response::HTTP_BAD_REQUEST);
    }
}
