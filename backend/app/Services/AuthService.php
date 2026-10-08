<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\RegisterDTO;
use App\Jobs\SendVerificationEmail;
use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as OAuthUser;
use SensitiveParameter;

class AuthService
{
    public function __construct(private readonly AuthRepository $authRepository) {}

    /**
     * @throws AuthenticationException If authentication after creation unexpectedly fails.
     */
    public function register(RegisterDTO $dto): string
    {
        $this->authRepository->createNewUser($dto);

        if (! Auth::attempt(['email' => $dto->email, 'password' => $dto->password])) {
            throw new AuthenticationException('Registration succeeded but authentication failed.');
        }

        /** @var User $user */
        $user = Auth::user();
        SendVerificationEmail::dispatch($user);

        return $user->createToken('API TOKEN')->plainTextToken;
    }

    /**
     * @throws AuthenticationException If the credentials are invalid.
     */
    public function login(string $email, #[SensitiveParameter] string $password): string
    {
        if (! Auth::attempt(['email' => $email, 'password' => $password])) {
            throw new AuthenticationException('Invalid credentials.');
        }

        return Auth::user()->createToken('API TOKEN')->plainTextToken;
    }

    public function loginWithOAuth(string $driver, OAuthUser $oauthUser): string
    {
        $idField = "{$driver}_id";

        $user = User::where($idField, $oauthUser->getId())->first()
            ?? User::where('email', $oauthUser->getEmail())->first();

        if ($user) {
            if (! $user->$idField) {
                $user->update([$idField => $oauthUser->getId()]);
            }
        } else {
            $user = User::create([
                'email' => $oauthUser->getEmail(),
                $idField => $oauthUser->getId(),
                'password' => null,
                'email_verified_at' => now(),
            ]);
        }

        return $user->createToken('API TOKEN')->plainTextToken;
    }

    public function verifyEmail(User $user, string $hash): bool
    {
        if (! hash_equals(sha1($user->email), $hash)) {
            return false;
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return true;
    }

    /**
     * @return bool True if the email was sent; false if already verified.
     */
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
        $user->currentAccessToken()?->delete();
        Auth::guard('web')->logout();

        return true;
    }
}
