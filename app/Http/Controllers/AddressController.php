<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Client\Client;
use Illuminate\Http\Request;

class AddressController
{
     /**
     * @OA\Get(path="/addresses",
     *   tags={"Clients"},
     *   summary="Liste des adresses postales en autocomplete",
     *   description="Liste des adresses postales en autocomplete",
     *   operationId="addressesList",
     *   @OA\Parameter(
     *     name="adresse",
     *     in="query",
     *     description="Une chaîne de caractères",
     *     @OA\Schema(
     *          type="string",
     *          example="24"
     *     )
     *   ),
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
     *                              property="postal_address",
     *                              type="string",
     *                              description="Adresse postale",
     *                              example="2466 Boulevard Laurier, Québec, QC, Canada"
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="total",
     *                      type="integer",
     *                      description="Nombre total",
     *                      example=3
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
    public function index(Request $request)
    {
        $addresses = Address::whereLike('postal_address', $request->adresse)
            ->distinct()
            ->get('postal_address');

        return response()->json([
            'data' => $addresses,
            'total' => $addresses->count(),
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
}
