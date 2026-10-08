<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SupportControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_send_support_message_returns_ok(): void
    {
        Queue::fake();

        $this->postJson('/api/support', [
            'message' => 'I need help with something.',
            'email' => 'user@example.com',
        ])->assertStatus(200)->assertJson(['status' => true]);
    }

    public function test_support_limit_is_not_consumed_by_stats_requests(): void
    {
        Queue::fake();
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.15']);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->getJson('/api/stats')->assertOk();
        }

        $this->postJson('/api/support', [
            'message' => 'I need help with something.',
            'email' => 'user@example.com',
        ])->assertOk()->assertJson(['status' => true]);
    }
}
