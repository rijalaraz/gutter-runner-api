<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Transformers\Users\PermissionTransformer;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    protected $model;

    public function __construct(Permission $model)
    {
        $this->model = $model;
        $this->middleware('permission:List permissions')->only('index');
    }

    /**
     * @OA\Get(path="/permissions",
     *   tags={"Roles et Permissions"},
     *   summary="Liste des permissions",
     *   description="Liste des permissions",
     *   operationId="permissionsList",
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
     *                              type="string",
     *                              description="Uuid de la permission",
     *                              example="947027"
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                              description="Nom de la permission",
     *                              example="Create clients"
     *                          ),
     *                          @OA\Property(
     *                              property="created_at",
     *                              type="string",
     *                              description="Date de création de la permission",
     *                              example="2021-05-15T17:08:58+00:00"
     *                          ),
     *                          @OA\Property(
     *                              property="updated_at",
     *                              type="string",
     *                              description="Date de mise à jour de la permission",
     *                              example="2021-05-15T17:08:58+00:00"
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="meta",
     *                      type="object",
     *                      description="Métadonnées",
     *                      @OA\Property(
     *                          property="pagination",
     *                          type="object",
     *                          description="Description de la pagination",
     *                          @OA\Property(
     *                              property="total",
     *                              type="integer",
     *                              description="Total des données",
     *                              example=13
     *                          ),
     *                          @OA\Property(
     *                              property="count",
     *                              type="integer",
     *                              description="Nombre de données affichées",
     *                              example=13
     *                          ),
     *                          @OA\Property(
     *                              property="per_page",
     *                              type="integer",
     *                              description="Nombre de données affichées par page",
     *                              example=15
     *                          ),
     *                          @OA\Property(
     *                              property="current_page",
     *                              type="integer",
     *                              description="Numéro de la page courante",
     *                              example=1
     *                          ),
     *                          @OA\Property(
     *                              property="total_pages",
     *                              type="integer",
     *                              description="Nombre total des pages",
     *                              example=1
     *                          ),
     *                          @OA\Property(
     *                              property="links",
     *                              type="object",
     *                              description="Liens associés"
     *                          )
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
     *     response=403,
     *     description="Utilisateur non autorisé ou non permis",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="L'utilisateur n'a pas la permission nécessaire",
     *                         example="Vous n'avez pas la permission d'effectuer cette action."
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
        $paginator = $this->model->paginate($request->get('limit', config('app.pagination_limit')));
        if ($request->has('limit')) {
            $paginator->appends('limit', $request->get('limit'));
        }

        return fractal($paginator, new PermissionTransformer())->respond();
    }
}
