<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_register_returns_token(): void
    {
        $this->postJson('/api/register', [
            'email' => 'reg_'.uniqid().'@example.com',
            'password' => 'Secret1!',
        ])->assertStatus(201)->assertJsonFragment(['status' => true])->assertJsonStructure(['token']);
    }

    public function test_login_returns_token(): void
    {
        $user = User::factory()->create(['password' => bcrypt('Secret1!')]);

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'Secret1!'])
            ->assertStatus(200)
            ->assertJsonFragment(['status' => true])
            ->assertJsonStructure(['token']);
    }

    public function test_logout_revokes_only_the_current_token(): void
    {
        $user = User::factory()->create();
        $currentToken = $user->createToken('current session');
        $otherToken = $user->createToken('other session');

        $this->withToken($currentToken->plainTextToken)
            ->postJson('/api/logout')
            ->assertStatus(200);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $currentToken->accessToken->id,
        ]);
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $otherToken->accessToken->id,
            'tokenable_id' => $user->id,
        ]);
    }

    public function test_broadcast_auth_returns_json_unauthenticated_response(): void
    {
        $this->postJson('/broadcasting/auth', [
            'channel_name' => 'presence-diagram.test',
            'socket_id' => '123.456',
        ])->assertUnauthorized()
            ->assertJsonFragment(['message' => 'Unauthenticated.']);
    }

    public function test_resend_verification_succeeds(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/email/resend')
            ->assertStatus(200)
            ->assertJsonStructure(['message']);
    }

    public function test_register_then_verify_email(): void
    {
        Queue::fake();

        $email = 'verify_'.uniqid().'@example.com';

        $this->postJson('/api/register', ['email' => $email, 'password' => 'Secret1!'])
            ->assertStatus(201)
            ->assertJsonStructure(['token']);

        $user = User::where('email', $email)->firstOrFail();
        $this->assertNull($user->email_verified_at);

        $signedUrl = URL::signedRoute('verification.verify', [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $this->get($signedUrl)->assertRedirect('/diagrams?verified=1');

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_login_fails_with_wrong_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correct-pass')]);

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'wrong-pass'])
            ->assertStatus(401)
            ->assertJsonFragment(['status' => false]);
    }

    public function test_oauth_callback_creates_user_and_redirects_with_token(): void
    {
        $oauthUser = Mockery::mock(\Laravel\Socialite\Contracts\User::class);
        $oauthUser->shouldReceive('getId')->andReturn('google-test-id-'.uniqid());
        $oauthUser->shouldReceive('getEmail')->andReturn('oauth_'.uniqid().'@example.com');

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->andReturn($oauthUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get('/auth/google/callback')
            ->assertRedirect()
            ->assertRedirectContains('/auth/callback?token=');
    }
}
