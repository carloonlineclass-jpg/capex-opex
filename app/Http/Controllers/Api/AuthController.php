<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Support\JwtService;

class AuthController extends Controller
{
    public function __construct(private JwtService $jwtService)
    {
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::with('department')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        if (Hash::needsRehash($user->password)) {
            $user->forceFill([
                'password' => Hash::driver('bcrypt')->make($request->password, [
                    'rounds' => (int) config('security.bcrypt_rounds', 12),
                ]),
            ])->save();
            $user->refresh();
        }

        $token = $this->jwtService->issueToken($user);

        return response()->json([
            'token_type' => 'Bearer',
            'token' => $token,
            'expires_in_minutes' => (int) config('security.jwt_ttl', 120),
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        return response()->json(['message' => 'Logged out successfully. Delete the JWT on the client side to finish logout.']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('department'));
    }
}
