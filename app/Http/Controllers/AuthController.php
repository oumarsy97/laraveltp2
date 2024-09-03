<?php
namespace App\Http\Controllers;

use App\Enums\EtatEnum;
use App\Enums\ResponseStatus;
use App\Enums\RoleEnum;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Client;
use App\Models\RefreshToken;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Support\Str;
use \Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Post(
     *      path="/register",
     *      operationId="register",
     *      tags={"Auth"},
     *      summary="Register",
     *      description="Register",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="nom",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="prenom",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="login",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="password_confirmation",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="photo",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="client_id",
     *                      type="integer",
     *                  ),
     *                  required={"nom","prenom","login","password","password_confirmation","client_id"}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="data",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                          ),
     *                          @OA\Property(
     *                              property="nom",
     *                              type="string",
     *                          ),
     *                          @OA\Property(
     *                              property="prenom",
     *                              type="string",
     *                          ),
     *                          @OA\Property(
     *                              property="login",
     *                              type="string",
     *                          ),
     *                          @OA\Property(
     *                              property="photo",
     *                              type="string",
     *                          )

     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="status",
     *                      type="string",
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *
     )
     * )
     **/

    public function store(StoreUserRequest $request)
    {

         $validate = $request->validated();
         $validateRequest = $request->only('client_id');
         $client = Client::with('user')->findOrFail($validateRequest['client_id']);
         if($client->user_id!=null){
             return $this->sendResponse($client, 'Ce client a deja un compte',Response::HTTP_CONFLICT,ResponseStatus::ECHEC);
         }
         if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('users', 'public');
            $validator['photo'] = $path;
        }
        $validate['password'] = bcrypt($validate['password']);
        $user = User::create([
            'nom' => $validate['nom'],
            'prenom' => $validate['prenom'],
            'login' => $validate['login'],
            'password' => $validate['password'],
            'photo' => $validate['photo'],
        ]);

        $user->client()->save($client);
        $client = Client::with('user')->find($validateRequest['client_id']);
        $token = $user->createToken('api-token')->accessToken;

        return $this->sendResponse(['token' => $token, 'client' => $client], 'utilisateur cree avec succes', Response::HTTP_OK,ResponseStatus::SUCCESS);

    }

    /**
     * @OA\Post(
     *      path="/login",
     *      operationId="login",
     *      tags={"Auth"},
     *      summary="Login",
     *      description="Login",
     *      @OA\RequestBody(

     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="login",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                  ),
     *                  required={"login", "password"}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="data",
     *                      type="object",
     *                      @OA\Property(
     *                          property="token",
     *                          type="string",
     *                      ),
     *                      @OA\Property(
     *                          property="refresh_token",
     *                          type="string",
     *                      )
     *                  ),
     *
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     )
     */
        public function login(LoginRequest $request)
    {
        try{
        $credentials = $request->only('login', 'password');
        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user **/
            $user = Auth::user();
            $role = Role::find($user->role_id)->libelle;
            if ($user->etat ==EtatEnum::INACTIF->value) {
                return $this->sendResponse(null, 'Compte inactif', Response::HTTP_FORBIDDEN,ResponseStatus::ECHEC);
            }
           

            $token = $user->createToken(['api-token', ['*']])->accessToken;
             $refreshToken = $this->createRefreshToken($user);
            return $this->sendResponse(['token' => $token, 'refresh_token' => $refreshToken->token], 'utilisateur connecté avec succes', Response::HTTP_OK,ResponseStatus::SUCCESS);
        }

        return $this->sendResponse(null, 'login ou mot de passe incorrects', Response::HTTP_UNAUTHORIZED,ResponseStatus::ECHEC);
        }catch(\Exception $e){
            return $this->sendResponse(null, $e->getMessage(), Response::HTTP_BAD_REQUEST,ResponseStatus::ECHEC);
        }
    }

    /**
     *  @OA\Post(
     *      path="/refresh",
     *      operationId="refresh",
     *      tags={"Auth"},
     *      summary="Refresh",
     *      description="Refresh",
     *      @OA\RequestBody(

     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="refresh_token",
     *                      type="string",
     *                  ),
     *                  required={"refresh_token"}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(

     *                  @OA\Property(
     *                      property="token",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="refresh_token",
     *                      type="string",
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *      )
     *      )
     *      )
     *
     */
    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required',
        ]);

        $refreshToken = RefreshToken::where('token', $request->refresh_token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$refreshToken) {
            return response()->json(['message' => 'Invalid refresh token'], 400);
        }

        $user = $refreshToken->user;

        // Révoque tous les tokens existants
        $user->tokens()->delete();

        // Crée un nouveau token
        $token = $user->createToken('api-token')->plainTextToken;

        // Optionnel : Régénère le refresh token
        $refreshToken->delete();
        $newRefreshToken = $this->createRefreshToken($user);

        return response()->json([
            'token' => $token,
            'refresh_token' => $newRefreshToken->token,
        ]);
    }

    private function createRefreshToken(User $user)
    {
        $expiresAt = now()->addDays(30);

        return RefreshToken::create([
            'user_id' => $user->id,
            'token' => Str::random(60),
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     *  @OA\Post(
     *      path="/logout",
     *      operationId="logout",
     *      tags={"Auth"},
     *      summary="Logout",
     *      description="Logout",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(

     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      )
     *      )
     *      )
     * */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        RefreshToken::where('user_id', $request->user()->id)->delete();

        return $this->sendResponse(null, 'utilisateur deconnecté avec succes', Response::HTTP_OK,ResponseStatus::SUCCESS);
    }

}
