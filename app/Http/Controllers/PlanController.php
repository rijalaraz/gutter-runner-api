<?php

namespace App\Http\Controllers;

use App\Models\Forfait;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PlanController
{
    /**
     * @OA\Get(path="/api/plans",
     *   tags={"Inscription"},
     *   summary="Liste des plans d'abonnement",
     *   description="Liste des plans d'abonnement",
     *   operationId="plansList",
     *   @OA\Response(
     *      response=200,
     *      description="Liste obtenue",
     *      content={
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      type="array",
     *                      property="plans",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(
     *                              property="id",
     *                              type="string",
     *                              description="ID",
     *                              example="gutter_runner_monthly_forfait_gestionnaire"
     *                          ),
     *                          @OA\Property(
     *                              property="object",
     *                              type="string",
     *                              description="",
     *                              example="plan"
     *                          ),
     *                          @OA\Property(
     *                              property="active",
     *                              type="boolean",
     *                              description="Statut de l'abonnement",
     *                              example=true
     *                          ),
     *                          @OA\Property(
     *                              property="aggregate_usage",
     *                              type="string",
     *                              description="",
     *                              example=null
     *                          ),
     *                          @OA\Property(
     *                              property="amount",
     *                              type="integer",
     *                              description="Tarif du produit",
     *                              example=19900
     *                          ),
     *                          @OA\Property(
     *                              property="amount_decimal",
     *                              type="string",
     *                              description="Tarif en décimal du produit",
     *                              example="19900"
     *                          ),
     *                          @OA\Property(
     *                              property="billing_scheme",
     *                              type="string",
     *                              description="Modèle de tarification",
     *                              example="per_unit"
     *                          ),
     *                          @OA\Property(
     *                              property="created",
     *                              type="integer",
     *                              description="Timestamp de création du plan",
     *                              example=1620480035
     *                          ),
     *                          @OA\Property(
     *                              property="currency",
     *                              type="string",
     *                              description="Devise",
     *                              example="cad"
     *                          ),
     *                          @OA\Property(
     *                              property="interval",
     *                              type="string",
     *                              description="Type de facturation",
     *                              example="month"
     *                          ),
     *                          @OA\Property(
     *                              property="interval_count",
     *                              type="integer",
     *                              description="",
     *                              example=1
     *                          ),
     *                          @OA\Property(
     *                              property="livemode",
     *                              type="boolean",
     *                              description="Mode",
     *                              example=false
     *                          ),
     *                          @OA\Property(
     *                              property="metadata",
     *                              type="array",
     *                              description="Métadonnées",
     *                              @OA\Items(
     *                                 type="string"
     *                              )
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                              description="Nom du plan ou Nom du produit",
     *                              example="Gestionnaire"
     *                          ),
     *                          @OA\Property(
     *                              property="nickname",
     *                              type="string",
     *                              description="Surnom du plan",
     *                              example=null
     *                          ),
     *                          @OA\Property(
     *                              property="product",
     *                              type="string",
     *                              description="Identifiant unique du produit",
     *                              example="prod_JRjYzxw4str3Sh"
     *                          ),
     *                          @OA\Property(
     *                              property="statement_descriptor",
     *                              type="string",
     *                              description="Libellé de relevé bancaire",
     *                              example="Forfait Gestionnaire"
     *                          ),
     *                          @OA\Property(
     *                              property="tiers",
     *                              type="string",
     *                              description="",
     *                              example=null
     *                          ),
     *                          @OA\Property(
     *                              property="tiers_mode",
     *                              type="string",
     *                              description="",
     *                              example=null
     *                          ),
     *                          @OA\Property(
     *                              property="transform_usage",
     *                              type="string",
     *                              description="",
     *                              example=null
     *                          ),
     *                          @OA\Property(
     *                              property="trial_period_days",
     *                              type="string",
     *                              description="",
     *                              example=null
     *                          ),
     *                          @OA\Property(
     *                              property="usage_type",
     *                              type="string",
     *                              description="",
     *                              example="licensed"
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="total",
     *                      type="integer",
     *                      description="Nombre total",
     *                      example=1
     *                  )
     *              )
     *          )
     *      }
     *   )
     * )
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $stripe = Stripe::make(env('STRIPE_SECRET'));
        $plans = $stripe->plans()->all();

        return response()->json([
           'plans' => $plans['data'],
           'total' => count($plans['data']),
        ]);
    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }

    /**
     * Get a validator for an incoming plan request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'plan' => 'required',
            'plan.id' => 'required|max:255',
            'plan.name' => 'required|max:255',
            'plan.amount' => ['required', 'numeric', 'min:1', 'max:1999.99', 'regex:/^\d+(\.\d{1,2})?$/'],
            'plan.currency' => 'required|max:3',
            'plan.interval' => 'required|in:week,month,year',
        ]);
    }
}
