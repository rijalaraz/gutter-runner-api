<?php

namespace App\Http\Controllers\Client;

use App\Models\Client\ClientNumeroType;
use Illuminate\Http\Request;

class ClientNumeroTypeController
{
    /**
     * @OA\Get(path="/client_numero_types",
     *   tags={"Clients"},
     *   summary="Liste des types de numéro",
     *   description="Liste des types de numéro",
     *   operationId="numberTypeList",
     *   @OA\Response(
     *      response=200,
     *      description="Liste obtenue",
     *      content={
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      type="array",
     *                      property="client_numero_types",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=2
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                              description="Nom",
     *                              example="Bureau"
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
        $numberTypes = ClientNumeroType::orderBy('id', 'ASC')->get(['id', 'name']);

        return response()->json([
            'client_numero_types' => $numberTypes,
            'total' => $numberTypes->count(),
        ]);
    }

    public function store(Request $request)
    {
    }

    public function show(ClientNumeroType $clientNumeroType)
    {
    }

    public function update(Request $request, ClientNumeroType $clientNumeroType)
    {
    }

    public function destroy(ClientNumeroType $clientNumeroType)
    {
    }
}
