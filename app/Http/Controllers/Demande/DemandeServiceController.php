<?php

namespace App\Http\Controllers\Demande;

use App\Models\Demande\DemandeService;
use Illuminate\Http\Request;

class DemandeServiceController
{
     /**
     * @OA\Get(path="/demande_services",
     *   tags={"Demandes"},
     *   summary="Liste des services de demande",
     *   description="Liste des services de demande",
     *   operationId="demandeServicesList",
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
     *                              example="Installation"
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="total",
     *                      type="integer",
     *                      description="Nombre total",
     *                      example=7
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
        $demandeServices = DemandeService::orderBy('id', 'ASC')->get(['id', 'name']);

        return response()->json([
            'data' => $demandeServices,
            'total' => $demandeServices->count(),
        ]);
    }

    public function store(Request $request)
    {
    }

    public function show(DemandeService $demandeService)
    {
    }

    public function update(Request $request, DemandeService $demandeService)
    {
    }

    public function destroy(DemandeService $demandeService)
    {
    }
}
