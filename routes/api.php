<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\ClientSequenceController;
use App\Http\Controllers\Compagnie\DomaineController;
use App\Http\Controllers\Compagnie\SuccursaleController;
use App\Http\Controllers\Demande\DemandeController;
use App\Http\Controllers\Demande\DemandeSequenceController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Users\RolesController;
use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => ['auth:api']], function () {
    // Authentification
    Route::post('logout', [LoginController::class, 'logout']);

    /*
    // Users
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UsersController::class, 'index']);
        Route::post('/', [UsersController::class, 'store']);
        Route::get('/{uuid}', [UsersController::class, 'show']);
        Route::put('/{uuid}', [UsersController::class, 'update']);
        Route::patch('/{uuid}', [UsersController::class, 'update']);
        Route::delete('/{uuid}', [UsersController::class, 'destroy']);
    });
    */

    // Roles
    Route::group(['prefix' => 'roles'], function () {
        Route::get('/', [RolesController::class, 'index']);
        Route::post('/', [RolesController::class, 'store']);
        Route::get('/{uuid}', [RolesController::class, 'show']);
        Route::put('/{uuid}', [RolesController::class, 'update']);
        Route::patch('/{uuid}', [RolesController::class, 'update']);
        Route::delete('/{uuid}', [RolesController::class, 'destroy']);
    });

    // Permissions
    Route::resource('permissions', 'Users\PermissionsController');

    // Clients
    Route::resource('client_titres', 'Client\ClientTitreController');
    Route::resource('client_company_types', 'Client\ClientCompanyTypeController');
    Route::resource('client_payment_terms', 'Client\ClientPaymentTermController');
    Route::resource('client_numero_types', 'Client\ClientNumeroTypeController');
    Route::get('provinces', [ProvinceController::class, 'index']);
    Route::resource('clients', 'Client\ClientController');
    Route::resource('tags', 'TagController');
    Route::get('addresses', [AddressController::class, 'index']);
    Route::get('client_sequences', [ClientSequenceController::class, 'index']);

    // Demandes
    Route::resource('demande_sources', 'Demande\DemandeSourceController');
    Route::resource('demande_services', 'Demande\DemandeServiceController');
    Route::resource('demande_client_availabilities', 'Demande\DemandeClientAvailabilityController');
    Route::get('clients_autocomplete', [ClientController::class, 'getList']);
    Route::get('clients/{uuid}/service_addresses', [ClientController::class, 'getServiceAddresses']);
    Route::get('demande_sequences', [DemandeSequenceController::class, 'index']);
    Route::get('users/assignable', [UsersController::class, 'getAssignableUsers']);
    Route::resource('demande_product_presentations', 'Demande\DemandeProductPresentationController');
    Route::post('clients/{uuid}/add_service_address', [ClientController::class, 'addServiceAddress']);
    Route::post('clients/quick', [ClientController::class, 'createClientQuickly']);
    Route::resource('demandes', 'Demande\DemandeController');
    Route::get('demande_statuts', [DemandeController::class, 'statutList']);
    Route::post('demande_actions', [DemandeController::class, 'act']);
    Route::get('demande_date_filter_interval', [DemandeController::class, 'getDateFilterInterval']);

    Route::patch('settings/profile', [ProfileController::class, 'update']);
    Route::patch('settings/password', [PasswordController::class, 'update']);
});

Route::group(['namespace' => 'App\Http\Controllers\Auth', 'middleware' => ['guest:api']], function () {
    // Authentification
    Route::post('login', [LoginController::class, 'login']);
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);

    // Inscription
    Route::get('domaines', [DomaineController::class, 'index']);
    Route::get('succursales', [SuccursaleController::class, 'index']);
    Route::get('plans', [PlanController::class, 'index']);
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('email/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend']);
    Route::post('stripe/webhook', [WebhookController::class, 'handleWebhook']);

    Route::post('oauth/{driver}', [OAuthController::class, 'redirect']);
    Route::get('oauth/{driver}/callback', [OAuthController::class, 'handleCallback'])->name('oauth.callback');
});
