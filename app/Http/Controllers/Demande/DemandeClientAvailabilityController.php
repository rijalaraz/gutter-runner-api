<?php

namespace App\Http\Controllers\Demande;

use App\Models\Demande\DemandeClientAvailability;
use Illuminate\Http\Request;

class DemandeClientAvailabilityController
{
    /**
     * @OA\Get(path="/api/demande_client_availabilities",
     *   tags={"Demandes"},
     *   summary="Liste des disponibilités clients de la demande",
     *   description="Liste des disponibilités clients de la demande",
     *   operationId="clientAvailabilitiesList",
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
     *                              property="name",
     *                              type="string",
     *                              description="Nom",
     *                              example="Avant-midi"
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
    public function index()
    {
        $demandeClientAvailabilities = DemandeClientAvailability::orderBy('id', 'ASC')->get(['id', 'name']);

        return response()->json([
            'data' => $demandeClientAvailabilities,
            'total' => $demandeClientAvailabilities->count(),
        ]);
    }

    public function store(Request $request)
    {
    }

    public function show(DemandeClientAvailability $demandeClientAvailability)
    {
    }

    public function update(Request $request, DemandeClientAvailability $demandeClientAvailability)
    {
    }

    public function destroy(DemandeClientAvailability $demandeClientAvailability)
    {
    }
}
