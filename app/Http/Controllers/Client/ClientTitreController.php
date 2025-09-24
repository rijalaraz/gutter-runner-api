<?php

namespace App\Http\Controllers\Client;

use App\Models\Client\ClientTitre;
use Illuminate\Http\Request;

class ClientTitreController
{
     /**
     * @OA\Get(path="/api/client_titres",
     *   tags={"Clients"},
     *   summary="Liste des titres",
     *   description="Liste des titres",
     *   operationId="titreList",
     *   @OA\Response(
     *      response=200,
     *      description="Liste obtenue",
     *      content={
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      type="array",
     *                      property="client_titres",
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
     *                              example="M."
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="total",
     *                      type="integer",
     *                      description="Nombre total",
     *                      example=2
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
        $titres = ClientTitre::orderBy('id', 'ASC')->get(['id', 'name']);

        return response()->json([
            'client_titres' => $titres,
            'total' => $titres->count(),
        ]);
    }

    public function store(Request $request)
    {
    }

    public function show(ClientTitre $clientTitre)
    {
    }

    public function update(Request $request, ClientTitre $clientTitre)
    {
    }

    public function destroy(ClientTitre $clientTitre)
    {
    }
}
