<?php

namespace App\Http\Controllers;

use App\Models\Client\Client;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController
{
     /**
     * @OA\Get(path="/api/tags",
     *   tags={"Clients"},
     *   summary="Liste des étiquettes en autocomplete",
     *   description="Liste des étiquettes en autocomplete",
     *   operationId="etiquettesList",
     *   @OA\Parameter(
     *     name="nom",
     *     in="query",
     *     description="Une chaîne de caractères",
     *     @OA\Schema(
     *          type="string",
     *          example="m"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="client_uuid",
     *     in="query",
     *     description="L'uuid du client",
     *     @OA\Schema(
     *          type="string",
     *          example="880371"
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
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=1
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                              description="Nom",
     *                              example="Aluminium"
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
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $client = Client::where('uuid', $request->client_uuid)->first();
        $clientTags = [];
        if($client) {
            $clientTags = $client->tags()->pluck('name')->toArray();
        }

        $tags = Tag::whereLike('name', $request->nom)
            ->whereNotIn('name', $clientTags)
            ->get();

        return response()->json([
            'data' => $tags,
            'total' => $tags->count(),
        ]);
    }

    public function store(Request $request)
    {
    }

    public function show(Tag $tag)
    {
    }

    public function update(Request $request, Tag $tag)
    {
    }

    public function destroy(Tag $tag)
    {
    }
}
