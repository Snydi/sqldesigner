<?php

namespace App\Services;

use App\Mail\Admin\NewUserRegistrationMail;
use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
        Mail::to(config('mail.from.address'))->send(new NewUserRegistrationMail($data['email'])); //TODO add this to queue, so the is no load time on register
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