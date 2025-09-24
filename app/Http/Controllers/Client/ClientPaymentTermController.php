<?php

namespace App\Http\Controllers\Client;

use App\Models\Client\ClientPaymentTerm;
use Illuminate\Http\Request;

class ClientPaymentTermController
{
    /**
     * @OA\Get(path="/api/client_payment_terms",
     *   tags={"Clients"},
     *   summary="Liste des termes de paiement",
     *   description="Liste des termes de paiement",
     *   operationId="paymentTermsList",
     *   @OA\Response(
     *      response=200,
     *      description="Liste obtenue",
     *      content={
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      type="array",
     *                      property="client_payment_terms",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=3
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                              description="Nom",
     *                              example="Net 15 jours"
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="total",
     *                      type="integer",
     *                      description="Nombre total",
     *                      example=6
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
    public function index()
    {
        $paymentTerms = ClientPaymentTerm::orderBy('id', 'ASC')->get(['id', 'name']);

        return response()->json([
            'client_payment_terms' => $paymentTerms,
            'total' => $paymentTerms->count(),
        ]);
    }

    public function store(Request $request)
    {
    }

    public function show(ClientPaymentTerm $clientPaymentTerm)
    {
    }

    public function update(Request $request, ClientPaymentTerm $clientPaymentTerm)
    {
    }

    public function destroy(ClientPaymentTerm $clientPaymentTerm)
    {
    }
}
