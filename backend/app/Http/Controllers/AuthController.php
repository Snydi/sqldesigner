<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Jobs\SendVerificationEmail;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $status = $this->authService->register($request->validated());
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong email or password',
            ], 401);
        }
        $token = $this->authService->login($request->validated());
        return response()->json([
            'status' => $status,
            'token' => $token,
            'message' => 'Registered successfully',
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong email or password',
            ], 401);
        }

        return response()->json([
            'status' => true,
            'token' => $this->authService->login($request->validated()),
            'message' => 'Logged in successfully',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        return response()->json([
            'status' => $this->authService->logout($request->user()),
            'message' => 'Logged out successfully'
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
        } catch (\Exception $e) {
            return redirect(config('app.url') . '/login?oauth_error=1');
        }

        $token = $this->authService->loginWithOAuth($driver, $oauthUser);
        return redirect(config('app.url') . '/auth/callback?token=' . $token . '&driver=' . $driver);
    }

    public function verifyEmail(Request $request, string $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->email), $hash)) {
            abort(403);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect('/diagrams?verified=1');
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified']);
        }

        SendVerificationEmail::dispatch($user);

        return response()->json(['message' => 'Verification email sent']);
    }
}
