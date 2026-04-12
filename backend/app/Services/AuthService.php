<?php

namespace App\Services;

use App\Jobs\SendNewUserRegistrationEmail;
use App\Jobs\SendVerificationEmail;
use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as OAuthUser;

class AuthService
{
    protected AuthRepository $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(array $data): ?string
    {
        $this->authRepository->createNewUser($data);
        SendNewUserRegistrationEmail::dispatch($data['email']);

        if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return null;
        }

        /** @var User $user */
        $user = Auth::user();
        SendVerificationEmail::dispatch($user);

        return $user->createToken('API TOKEN')->plainTextToken;
    }

    public function login(string $email, string $password): ?string
    {
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return null;
        }

        return Auth::user()->createToken('API TOKEN')->plainTextToken;
    }

    public function loginWithOAuth(string $driver, OAuthUser $oauthUser): string
    {
        $idField = "{$driver}_id";

        $user = User::where($idField, $oauthUser->getId())->first()
            ?? User::where('email', $oauthUser->getEmail())->first();

        if ($user) {
            if (!$user->$idField) {
                $user->update([$idField => $oauthUser->getId()]);
            }
        } else {
            $user = User::create([
                'email' => $oauthUser->getEmail(),
                $idField => $oauthUser->getId(),
                'password' => null,
                'email_verified_at' => now(),
            ]);
            SendNewUserRegistrationEmail::dispatch($user->email);
        }

        return $user->createToken('API TOKEN')->plainTextToken;
    }

    public function verifyEmail(User $user, string $hash): bool
    {
        if (!hash_equals(sha1($user->email), $hash)) {
            return false;
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return true;
    }

    public function resendVerification(User $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }

        SendVerificationEmail::dispatch($user);

        return true;
    }

    public function logout(User $user): true
    {
        $user->tokens()->delete();
        Auth::guard('web')->logout();
        return true;
    }
}
