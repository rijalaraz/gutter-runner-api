<?php

namespace App\Http\Controllers\Demande;

use App\Models\Demande\DemandeProductPresentation;
use Illuminate\Http\Request;

class DemandeProductPresentationController
{
    /**
     * @OA\Get(path="/demande_product_presentations",
     *   tags={"Demandes"},
     *   summary="Liste des présentations de produits",
     *   description="Liste des présentations de produits",
     *   operationId="productPresentationsList",
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
     *                              example="GutterClean System"
     *                          ),
     *                          @OA\Property(
     *                              property="url",
     *                              type="string",
     *                              description="Url",
     *                              example="https://www.youtube.com/watch?v=Zvp7E5vMrWM"
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
    public function index()
    {
        $productPresentations = DemandeProductPresentation::orderBy('id', 'ASC')->get(['id', 'name','url']);

        return response()->json([
            'data' => $productPresentations,
            'total' => $productPresentations->count(),
        ]);
    }

    public function store(Request $request)
    {
    }

    public function show(DemandeProductPresentation $demandeProductPresentation)
    {
    }

    public function update(Request $request, DemandeProductPresentation $demandeProductPresentation)
    {
    }

    public function destroy(DemandeProductPresentation $demandeProductPresentation)
    {
    }
}
