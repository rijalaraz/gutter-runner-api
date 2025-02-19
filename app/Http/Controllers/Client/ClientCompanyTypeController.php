<?php

namespace App\Http\Controllers\Client;

use App\Models\Client\ClientCompanyType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ClientCompanyTypeController
{
    /**
     * @OA\Get(path="/client_company_types",
     *   tags={"Clients"},
     *   summary="Liste des types de compagnie",
     *   description="Liste des types de compagnie",
     *   operationId="companyTypeList",
     *   @OA\Response(
     *      response=200,
     *      description="Liste obtenue",
     *      content={
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      type="array",
     *                      property="client_company_types",
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
     *                              example="Architecte"
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
        $companyTypes = ClientCompanyType::orderBy('id', 'ASC')->get(['id', 'name']);

        return response()->json([
            'client_company_types' => $companyTypes,
            'total' => $companyTypes->count(),
        ]);
    }

      /**
     * @OA\Post(path="/client_company_types",
     *   tags={"Clients"},
     *   summary="Création d'un type de compagnie",
     *   description="Création d'un type de compagnie",
     *   operationId="createCompanyType",
     *   @OA\Parameter(
     *     name="name",
     *     in="query",
     *     description="Nom du type de compagnie",
     *     @OA\Schema(
     *          type="string",
     *          example="Terrassement"
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
     *                      type="object",
     *                      property="client_company_type",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string",
     *                          description="Nom du type de compagnie",
     *                          example="Terrassement"
     *                      ),
     *                      @OA\Property(
     *                          property="id",
     *                          type="integer",
     *                          description="ID",
     *                          example=1
     *                      )
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
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Paramètres vides ou invalides",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="name",
     *                         type="array",
     *                         @OA\Items(
     *                              type="string",
     *                              example="La valeur du champ nom est déjà utilisée."
     *                         )
     *                     )
     *                 )
     *             )
     *         }
     *   )
     * )
     *
     * Create a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->validator($request->all());

        if($validator->fails()) {
            return response($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $client_company_type = ClientCompanyType::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'client_company_type' => $client_company_type,
        ]);
    }

    public function show(ClientCompanyType $clientCompanyType)
    {
    }

    public function update(Request $request, ClientCompanyType $clientCompanyType)
    {
    }

    public function destroy(ClientCompanyType $clientCompanyType)
    {
    }

     /**
     * Get a validator for an incoming request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255|unique:client_company_types,name,except,id',
        ]);
    }
}
