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
    public function register(array $data): true
    {
        $user = $this->authRepository->createNewUser($data);
        SendNewUserRegistrationEmail::dispatch($data['email']);
        SendVerificationEmail::dispatch($user);
        return true;
    }

    public function login(array $data): string
    {
        $user = $this->authRepository->findUser($data['email']);
        return $user->createToken("API TOKEN")->plainTextToken;
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

    public function logout(User $user): true
    {
        $user->tokens()->delete();
        Auth::guard('web')->logout();
        return true;
    }
}