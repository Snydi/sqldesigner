<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\VisitorStatus;
use App\Models\Diagram;
use App\Models\DiagramVisitor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DiagramControllerTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    private Diagram $diagram;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->diagram = Diagram::factory()->create(['user_id' => $this->user->id, 'schema' => []]);
    }

    private function auth(): static
    {
        return $this->actingAs($this->user, 'sanctum');
    }

    public function test_index_returns_diagrams(): void
    {
        $this->auth()->getJson('/api/diagrams')->assertStatus(200);
    }

    public function test_shared_returns_diagrams_the_user_can_access(): void
    {
        $shared = Diagram::factory()->create(['share_access' => 'read']);
        DiagramVisitor::factory()->create([
            'diagram_id' => $shared->id,
            'user_id' => $this->user->id,
            'status' => VisitorStatus::APPROVED,
        ]);

        $this->auth()
            ->getJson('/api/diagrams/shared-with-me')
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $shared->id)
            ->assertJsonPath('data.0.is_owner', false);
    }

    public function test_store_creates_diagram(): void
    {
        $this->auth()
            ->postJson('/api/diagrams', ['name' => 'New '.uniqid()])
            ->assertStatus(201)
            ->assertJsonFragment(['status' => true]);
    }

    public function test_show_returns_diagram(): void
    {
        $this->auth()
            ->getJson("/api/diagrams/{$this->diagram->id}")
            ->assertStatus(200);
    }

    public function test_update_saves_diagram(): void
    {
        $this->auth()
            ->putJson("/api/diagrams/{$this->diagram->id}", ['name' => 'Updated '.uniqid()])
            ->assertStatus(200)
            ->assertJsonFragment(['status' => true]);
    }

    public function test_destroy_deletes_diagram(): void
    {
        $diagram = Diagram::factory()->create(['user_id' => $this->user->id]);

        $this->auth()
            ->deleteJson("/api/diagrams/{$diagram->id}")
            ->assertStatus(204);
    }

    public function test_share_returns_access(): void
    {
        $this->auth()
            ->postJson("/api/diagrams/{$this->diagram->id}/share")
            ->assertStatus(200)
            ->assertJsonStructure(['share_access']);
    }

    public function test_unshare_succeeds(): void
    {
        $this->auth()
            ->deleteJson("/api/diagrams/{$this->diagram->id}/share")
            ->assertStatus(204);
    }

    public function test_update_share_access_succeeds(): void
    {
        $this->auth()
            ->patchJson("/api/diagrams/{$this->diagram->id}/share", ['access' => 'read'])
            ->assertStatus(200)
            ->assertJsonStructure(['share_access']);
    }

    public function test_get_visitors_returns_list(): void
    {
        $this->auth()
            ->getJson("/api/diagrams/{$this->diagram->id}/visitors")
            ->assertStatus(200);
    }

    public function test_import_returns_pending(): void
    {
        Queue::fake();
        Event::fake();

        // script column is JSON type — must be a JSON-encoded string value
        $this->auth()
            ->postJson("/api/diagrams/sql/import/{$this->diagram->id}", ['script' => json_encode('CREATE TABLE t (id INT);')])
            ->assertStatus(202)
            ->assertJsonFragment(['status' => 'pending']);
    }

    public function test_import_status_returns_status(): void
    {
        $this->auth()
            ->getJson("/api/diagrams/sql/import-status/{$this->diagram->id}")
            ->assertStatus(200)
            ->assertJsonStructure(['status']);
    }

    public function test_export_returns_pending(): void
    {
        Queue::fake();

        $this->auth()
            ->postJson("/api/diagrams/sql/export/{$this->diagram->id}")
            ->assertStatus(202)
            ->assertJsonFragment(['status' => 'pending']);
    }

    public function test_export_status_returns_status(): void
    {
        $this->auth()
            ->getJson("/api/diagrams/sql/export-status/{$this->diagram->id}")
            ->assertStatus(200)
            ->assertJsonStructure(['status']);
    }

    public function test_export_json_returns_ok(): void
    {
        $this->auth()
            ->getJson("/api/diagrams/json/export/{$this->diagram->id}")
            ->assertStatus(200);
    }

    public function test_export_migration_returns_zip(): void
    {
        // ZipArchive::close() deletes the temp file when no entries are added (empty schema).
        // Provide a schema with one table so the archive is non-empty and survives close().
        $this->diagram->update(['schema' => [
            ['id' => 't1', 'type' => 'table', 'label' => 'users', 'data' => ['uniqueTogether' => [], 'fulltextIndexes' => []]],
            ['id' => 'r1', 'type' => 'row', 'label' => 'id', 'parentNode' => 't1', 'data' => ['sqlType' => 'INT', 'nullable' => false, 'unsigned' => false, 'keyMod' => 'PRIMARY KEY', 'defaultValue' => null, 'comment' => null]],
        ]]);

        $this->auth()
            ->get("/api/diagrams/migration/export/{$this->diagram->id}")
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/zip');
    }

    public function test_show_embed_returns_data(): void
    {
        $this->diagram->update(['share_access' => 'read']);

        $this->getJson("/api/diagrams/embed/{$this->diagram->share_token}")
            ->assertStatus(200);
    }

    public function test_show_by_token_returns_diagram(): void
    {
        $this->auth()
            ->getJson("/api/diagrams/shared/{$this->diagram->share_token}")
            ->assertStatus(200);
    }

    public function test_show_returns_403_for_non_owner(): void
    {
        $other = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($other, 'sanctum')
            ->getJson("/api/diagrams/{$this->diagram->id}")
            ->assertStatus(403);
    }

    public function test_update_returns_403_for_non_owner(): void
    {
        $other = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($other, 'sanctum')
            ->putJson("/api/diagrams/{$this->diagram->id}", ['name' => 'Hijacked'])
            ->assertStatus(403);
    }

    public function test_destroy_returns_403_for_non_owner(): void
    {
        $other = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($other, 'sanctum')
            ->deleteJson("/api/diagrams/{$this->diagram->id}")
            ->assertStatus(403);
    }
}
