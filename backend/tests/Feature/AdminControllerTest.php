<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Diagram;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use DatabaseTransactions;

    private array $adminSession = ['admin_authenticated' => true];

    public function test_login_page_returns_ok(): void
    {
        $this->get('/admin/login')->assertStatus(200);
    }

    public function test_dashboard_redirects_without_auth(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_dashboard_returns_ok(): void
    {
        $this->withSession($this->adminSession)
            ->get('/admin')
            ->assertStatus(200);
    }

    public function test_library_returns_ok(): void
    {
        $this->withSession($this->adminSession)
            ->get('/admin/library')
            ->assertStatus(200);
    }

    public function test_reviews_returns_ok(): void
    {
        $this->withSession($this->adminSession)
            ->get('/admin/reviews')
            ->assertStatus(200);
    }

    public function test_billing_returns_ok(): void
    {
        $this->withSession($this->adminSession)
            ->get('/admin/billing')
            ->assertStatus(200);
    }

    public function test_user_activity_returns_ok(): void
    {
        $user = User::factory()->create();

        $this->withSession($this->adminSession)
            ->getJson("/admin/users/{$user->id}/activity")
            ->assertStatus(200);
    }

    public function test_feature_diagram_returns_ok(): void
    {
        $diagram = Diagram::factory()->create();

        $this->withSession($this->adminSession)
            ->postJson("/admin/diagrams/{$diagram->id}/feature", ['url' => 'https://example.com/img.png'])
            ->assertStatus(200)
            ->assertJson(['ok' => true]);
    }

    public function test_unfeature_diagram_returns_ok(): void
    {
        $diagram = Diagram::factory()->create(['featured' => true]);

        $this->withSession($this->adminSession)
            ->deleteJson("/admin/diagrams/{$diagram->id}/feature")
            ->assertStatus(204);
    }

    public function test_logout_redirects(): void
    {
        $this->withSession($this->adminSession)
            ->post('/admin/logout')
            ->assertRedirect('/admin/login');
    }

    public function test_login_with_correct_credentials_sets_session_and_redirects(): void
    {
        Config::set('app.admin_password', 'test-admin-secret');

        $this->post('/admin/login', ['username' => 'admin', 'password' => 'test-admin-secret'])
            ->assertRedirect('/admin');

        $this->assertTrue(session('admin_authenticated') === true);
    }

    public function test_login_with_wrong_credentials_returns_errors(): void
    {
        Config::set('app.admin_password', 'correct-secret');

        $this->post('/admin/login', ['username' => 'admin', 'password' => 'wrong-secret'])
            ->assertSessionHasErrors(['credentials']);
    }
}
