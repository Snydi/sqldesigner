<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\CreateDiagramDTO;
use App\DTOs\UpdateDiagramDTO;
use App\Enums\VisitorStatus;
use App\Models\Diagram;
use App\Models\DiagramVisitor;
use App\Models\User;
use App\Services\DiagramCrudService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiagramCrudServiceTest extends TestCase
{
    use DatabaseTransactions;

    private DiagramCrudService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DiagramCrudService::class);
    }

    public function test_get_user_diagrams(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['user_id' => $user->id]);
        $this->assertEquals($diagram->id, $this->service->getUserDiagrams($user)[0]->id);
    }

    public function test_get_shared_diagrams_returns_only_approved_accessible_diagrams(): void
    {
        $user = User::factory()->create();
        $approved = Diagram::factory()->create(['share_access' => 'read']);
        $pending = Diagram::factory()->create(['share_access' => 'read']);
        $unshared = Diagram::factory()->create(['share_access' => null]);

        DiagramVisitor::factory()->create([
            'diagram_id' => $approved->id,
            'user_id' => $user->id,
            'status' => VisitorStatus::APPROVED,
        ]);
        DiagramVisitor::factory()->create([
            'diagram_id' => $pending->id,
            'user_id' => $user->id,
            'status' => VisitorStatus::PENDING,
        ]);
        DiagramVisitor::factory()->create([
            'diagram_id' => $unshared->id,
            'user_id' => $user->id,
            'status' => VisitorStatus::APPROVED,
        ]);

        $diagrams = $this->service->getSharedDiagrams($user);

        $this->assertEquals([$approved->id], $diagrams->pluck('id')->all());
    }

    public function test_create_diagram(): void
    {
        $dto = new CreateDiagramDTO(name: 'test', userId: User::factory()->create()->id);
        $this->assertDatabaseHas(Diagram::class, $this->service->createDiagram($dto)->only(['name', 'user_id']));
    }

    public function test_update_diagram(): void
    {
        $this->assertTrue($this->service->updateDiagram(Diagram::factory()->create(), new UpdateDiagramDTO(name: 'Updated')));
    }

    public function test_delete_diagram(): void
    {
        $diagram = Diagram::factory()->create();
        $this->assertTrue($this->service->deleteDiagram($diagram));
        $this->assertDatabaseMissing(Diagram::class, ['id' => $diagram->id]);
    }

    public function test_get_embed_data(): void
    {
        $diagram = Diagram::factory()->create(['name' => 'My Diagram', 'db_type' => 'mysql', 'schema' => '[]']);
        $this->assertEquals(['name' => 'My Diagram', 'db_type' => 'mysql', 'schema' => '[]'], $this->service->getEmbedData($diagram));
    }
}
