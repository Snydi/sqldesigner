<?php

namespace App\Services;

use App\Jobs\SendNewUserRegistrationEmail;
use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    protected AuthRepository $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }
    public function register(array $data): true
    {
        $this->authRepository->createNewUser($data);
        SendNewUserRegistrationEmail::dispatch($data['email']);
        return true;
    }

    public function login(array $data): string
    {
        $user = $this->authRepository->findUser($data['email']);
        return $user->createToken("API TOKEN")->plainTextToken;
    }

    public function logout(User $user): true
    {
        $user->tokens()->delete();
        Auth::guard('web')->logout();
        return true;
    }
}