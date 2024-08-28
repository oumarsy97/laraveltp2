<?php
namespace App\Http\Controllers;

use App\Models\RefreshToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponser;
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;
            $refreshToken = $this->createRefreshToken($user);

            return response()->json([
                'user' => $user,
                'token' => $token,
                'refresh_token' => $refreshToken->token,
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //     if (!Auth::attempt($credentials)) {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }

    //     $user = Auth::user();
    //      $token = $user->createToken('API Token')->accessToken;

    //     return response()->json(['token' => 1], 200);
    // }
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

        return response()->json(['message' => 'Logged out successfully']);
    }

}
