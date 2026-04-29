<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Knuckles\Scribe\Attributes\Group;
use Laravel\Socialite\Facades\Socialite;

#[Group("Authentication")]
class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $token = $this->authService->register($request->validated());

        if (!$token) {
            return response()->json(['status' => false, 'message' => 'Wrong email or password'], 401);
        }

        return response()->json(['status' => true, 'token' => $token, 'message' => 'Registered successfully']);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login($request->input('email'), $request->input('password'));

        if (!$token) {
            return response()->json(['status' => false, 'message' => 'Wrong email or password'], 401);
        }

        return response()->json(['status' => true, 'token' => $token, 'message' => 'Logged in successfully']);
    }

    public function logout(Request $request): JsonResponse
    {
        return response()->json([
            'status' => $this->authService->logout($request->user()),
            'message' => 'Logged out successfully',
        ]);
    }

    public function oauthRedirect(string $driver): RedirectResponse
    {
        return Socialite::driver($driver)->redirect();
    }

    public function oauthCallback(string $driver): RedirectResponse
    {
        try {
            $oauthUser = Socialite::driver($driver)->user();
        } catch (Exception) {
            return redirect(config('app.url') . '/login?oauth_error=1');
        }

        $token = $this->authService->loginWithOAuth($driver, $oauthUser);

        return redirect(config('app.url') . '/auth/callback?token=' . $token . '&driver=' . $driver);
    }

    public function verifyEmail(string $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        if (!$this->authService->verifyEmail($user, $hash)) {
            abort(403);
        }

        return redirect('/diagrams?verified=1');
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $sent = $this->authService->resendVerification($request->user());

        return response()->json([
            'message' => $sent ? 'Verification email sent' : 'Email already verified',
        ]);
    }
}
