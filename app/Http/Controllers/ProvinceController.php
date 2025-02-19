<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController
{
    /**
     * @OA\Get(path="/provinces",
     *   tags={"Clients"},
     *   summary="Liste des provinces",
     *   description="Liste des provinces",
     *   operationId="provincesList",
     *   @OA\Response(
     *      response=200,
     *      description="Liste obtenue",
     *      content={
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      type="array",
     *                      property="provinces",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="ID",
     *                              example=11
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                              description="Nom de province",
     *                              example="Quebec"
     *                          ),
     *                          @OA\Property(
     *                              property="iso",
     *                              type="string",
     *                              description="Code de province",
     *                              example="QC"
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="total",
     *                      type="integer",
     *                      description="Nombre total",
     *                      example=13
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
        $provinces = Province::orderBy('name', 'ASC')->get();

        return response()->json([
            'provinces' => $provinces,
            'total' => $provinces->count(),
        ]);
    }

    public function store(Request $request)
    {
    }

    public function show(Province $province)
    {
    }

    public function update(Request $request, Province $province)
    {
    }

    public function destroy(Province $province)
    {
    }
}
