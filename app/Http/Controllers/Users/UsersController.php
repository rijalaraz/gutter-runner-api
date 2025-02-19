<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Transformers\Users\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UsersController extends Controller
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
        $this->middleware('permission:List users')->only('index');
        $this->middleware('permission:List users')->only('show');
        $this->middleware('permission:Create users')->only('store');
        $this->middleware('permission:Update users')->only('update');
        $this->middleware('permission:Delete users')->only('destroy');
    }

    public function index(Request $request)
    {
        $paginator = $this->model->with('roles.permissions')->paginate($request->get('limit', config('app.pagination_limit', 20)));
        if ($request->has('limit')) {
            $paginator->appends('limit', $request->get('limit'));
        }

        return fractal($paginator, new UserTransformer())->respond();
    }

     /**
     * @OA\Get(path="/users/assignable",
     *   tags={"Demandes"},
     *   summary="Liste des usagers à qui on peut assigner une demande",
     *   description="Liste des usagers à qui on peut assigner une demande",
     *   operationId="assignableUsersList",
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
     *                              description="Nom de l'usager",
     *                              example="Catherine Henry"
     *                          ),
     *                          @OA\Property(
     *                              property="email",
     *                              type="string",
     *                              description="Email de l'usager",
     *                              example="catherine.henry@gmail.com"
     *                          ),
     *                          @OA\Property(
     *                              property="roles",
     *                              type="object",
     *                              description="Role de l'usager",
     *                              @OA\Property(
     *                                  property="data",
     *                                  type="array",
     *                                  @OA\Items(
     *                                      type="object",
     *                                      @OA\Property(
     *                                          property="id",
     *                                          type="string",
     *                                          description="ID",
     *                                          example="995624"
     *                                      ),
     *                                      @OA\Property(
     *                                          property="name",
     *                                          type="string",
     *                                          description="Nom du role",
     *                                          example="Administrateur"
     *                                      ),
     *                                      @OA\Property(
     *                                          property="permissions",
     *                                          type="object",
     *                                          description="Les permissions du role",
     *                                          @OA\Property(
     *                                              property="data",
     *                                              type="array",
     *                                              @OA\Items(
     *                                                  type="object",
     *                                                  @OA\Property(
     *                                                      property="id",
     *                                                      type="string",
     *                                                      description="ID",
     *                                                      example="571433"
     *                                                  ),
     *                                                  @OA\Property(
     *                                                      property="name",
     *                                                      type="string",
     *                                                      description="Nom de la permission",
     *                                                      example="Lecture seule"
     *                                                  )
     *                                              )
     *                                          )
     *                                      )
     *                                  )
     *                              )
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
     *   )
     * )
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAssignableUsers()
    {
        $users = $this->model->with('roles.permissions')->get();

        $fUsers = $users->filter(function($user) {
            foreach ($user->roles as $role) {
                foreach ($role->permissions as $permission) {
                    if (
                        $permission['name'] == 'Lecture seule' &&
                        // $user->id != auth()->user()->id &&
                        $user->company()->pluck('id')->toArray()[0] == auth()->user()->company()->pluck('id')->toArray()[0]
                    ) {
                        return $user;
                    }
                }
            }
        });

        return fractal($fUsers, new UserTransformer())->respond();
    }

    public function show($id)
    {
        $user = $this->model->with('roles.permissions')->byUuid($id)->firstOrFail();

        return fractal($user, new UserTransformer())->respond();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);
        $user = $this->model->create($request->all());
        if ($request->has('roles')) {
            $user->syncRoles($request['roles']);
        }

        return fractal($user, new UserTransformer())->respond(Response::HTTP_CREATED);
    }

    public function update(Request $request, $uuid)
    {
        $user = $this->model->byUuid($uuid)->firstOrFail();
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ];
        if ($request->method() == 'PATCH') {
            $rules = [
                'name' => 'sometimes|required',
                'email' => 'sometimes|required|email|unique:users,email,'.$user->id,
            ];
        }
        $this->validate($request, $rules);
        // Except password as we don't want to let the users change a password from this endpoint
        $user->update($request->except('_token', 'password'));
        if ($request->has('roles')) {
            $user->syncRoles($request['roles']);
        }

        return fractal($user->fresh(), new UserTransformer())->respond();
    }

    public function destroy(Request $request, $uuid)
    {
        $user = $this->model->byUuid($uuid)->firstOrFail();
        $user->delete();

        return response()->json(null, 204);
    }
}
