<?php

namespace App\Http\Controllers\Demande;

use App\Models\Demande\DemandeSource;
use Illuminate\Http\Request;

class DemandeSourceController
{
    /**
     * @OA\Get(path="/demande_sources",
     *   tags={"Demandes"},
     *   summary="Liste des sources de demande",
     *   description="Liste des sources de demande",
     *   operationId="demandeSourcesList",
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
     *                              example="Internet"
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
        $demandeSources = DemandeSource::orderBy('id', 'ASC')->get(['id', 'name']);

        return response()->json([
            'data' => $demandeSources,
            'total' => $demandeSources->count(),
        ]);
    }

    public function store(Request $request)
    {
    }

    public function show(DemandeSource $demandeSource)
    {
    }

    public function update(Request $request, DemandeSource $demandeSource)
    {
    }

    public function destroy(DemandeSource $demandeSource)
    {
    }
}
