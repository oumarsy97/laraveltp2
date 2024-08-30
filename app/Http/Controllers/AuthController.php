<?php
namespace App\Http\Controllers;

use App\Enums\ResponseStatus;
use App\Enums\RoleEnum;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\RefreshToken;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Support\Str;
use \Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ApiResponser;

    public function register(RegisterRequest $request)
    {
        $validator =  $request->only('prenom', 'nom', 'login', 'password');

        $validator['password'] = bcrypt($request->password);
        if($request->role == 'ADMIN'){
            $validator['role_id'] = RoleEnum::ADMIN;
        }else{
            $validator['role_id'] = RoleEnum::BOUTIQUIER;
        }

        $user = User::create($validator);
        $token = $user->createToken('api-token')->accessToken;

        return $this->sendResponse(['token' => $token, 'user' => $user], 'utilisateur cree avec succes', Response::HTTP_OK,ResponseStatus::SUCCESS);
    }
    public function login(LoginRequest $request)
    {
        try{

        $credentials = $request->only('login', 'password');

        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user **/

            $user = Auth::user();


            $role = Role::find($user->role_id)->libelle;

           $scopes = [$role];
            $token = $user->createToken('api-token', ['ADMIN'])->accessToken;
             $refreshToken = $this->createRefreshToken($user);

            return $this->sendResponse(['token' => $token, 'refresh_token' => $refreshToken->token], 'utilisateur connecté avec succes', Response::HTTP_OK,ResponseStatus::SUCCESS);
        }

        return $this->sendResponse(null, 'login ou mot de passe incorrects', Response::HTTP_UNAUTHORIZED,ResponseStatus::ECHEC);
        }catch(\Exception $e){
            return $this->sendResponse(null, $e->getMessage(), Response::HTTP_BAD_REQUEST,ResponseStatus::ECHEC);
        }
    }

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

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        RefreshToken::where('user_id', $request->user()->id)->delete();

        return $this->sendResponse(null, 'utilisateur deconnecté avec succes', Response::HTTP_OK,ResponseStatus::SUCCESS);
    }

}
