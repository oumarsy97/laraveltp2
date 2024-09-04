<?php
namespace App\Http\Controllers;

use App\Enums\EtatEnum;
use App\Enums\ResponseStatus;
use App\Enums\RoleEnum;
use App\Events\ImageUploaded;
use App\Facades\UploadFacade;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreUserRequest;
use App\Mail\CarteFideliteMail;
use App\Mail\LoyaltyCardMail;
use App\Models\Client;
use App\Models\RefreshToken;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\CarteFideliteService;
use App\Services\Contracts\LoyaltyCardServiceInterface;
use App\Services\Contracts\TokenServiceInterface;
use App\Traits\ApiResponser;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use \Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AuthController extends Controller
{
    use ApiResponser;
    protected $tokenService;
    protected $loyaltyCardService;

    public function __construct(TokenServiceInterface $tokenService, LoyaltyCardServiceInterface $loyaltyCardService)
    {
        $this->tokenService = $tokenService;
        $this->loyaltyCardService = $loyaltyCardService;
    }

    public function store(StoreUserRequest $request)
{
    $validated = $request->validated();
    $clientId = $request->input('client_id');

    // Trouver le client
    $client = Client::with('user')->findOrFail($clientId);

    if ($client->user_id !== null) {
        return $this->sendResponse($client, 'Ce client a déjà un compte', Response::HTTP_CONFLICT, ResponseStatus::ECHEC);
    }

    $photoPath = null;

    // Créer l'utilisateur
    $user = User::create([
        'nom' => $validated['nom'],
        'prenom' => $validated['prenom'],
        'login' => $validated['login'],
        'password' => bcrypt($validated['password']),
        'photo' => $photoPath, // Stocker le chemin de l'image
    ]);

    // Associer l'utilisateur au client
    $client->user()->associate($user);
    $client->save();

      $client = Client::with('user')->find($clientId);


    return [
        // 'photo_base64' => $photoBase64, // Inclure la base64 dans la réponse
        'client' => $client,
    ];


}



    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('login', 'password');

            if (Auth::attempt($credentials)) {
                /** @var \App\Models\User $user **/
                $user = Auth::user();

                if ($user->etat == EtatEnum::INACTIF->value) {
                    return $this->sendResponse(null, 'Compte inactif', Response::HTTP_FORBIDDEN, ResponseStatus::ECHEC);
                }

                $tokenString = $this->tokenService->createToken($user);
                $refreshToken = $this->tokenService->createRefreshToken($user);

                return [
                    'token' => $tokenString,
                    'refresh_token' => $refreshToken,
                ];
             }

            return $this->sendResponse(null, 'Login ou mot de passe incorrects', Response::HTTP_UNAUTHORIZED, ResponseStatus::ECHEC);
        } catch (\Exception $e) {
            return $this->sendResponse(null, $e->getMessage(), Response::HTTP_BAD_REQUEST, ResponseStatus::ECHEC);
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
