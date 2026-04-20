<?php

namespace Tests\Unit\Services;

use App\Models\Diagram;
use App\Models\User;
use App\Services\DiagramCrudService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiagramCrudServiceTest extends TestCase
{
    use DatabaseTransactions;

    private DiagramCrudService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(DiagramCrudService::class);
    }

    public function testGetUserDiagrams(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['user_id' => $user->id]);
        $this->assertEquals($diagram->id, $this->service->getUserDiagrams($user)[0]->id);
    }

    public function testCreateDiagram(): void
    {
        $data = ['name' => 'test', 'user_id' => User::factory()->create()->id];
        $this->assertDatabaseHas(Diagram::class, $this->service->createDiagram($data)->toArray());
    }

    public function testUpdateDiagram(): void
    {
        $this->assertTrue($this->service->updateDiagram(Diagram::factory()->create(), ['name' => 'Updated']));
    }

    public function testDeleteDiagram(): void
    {
        $diagram = Diagram::factory()->create();
        $this->assertTrue($this->service->deleteDiagram($diagram));
        $this->assertDatabaseMissing(Diagram::class, ['id' => $diagram->id]);
    }

    public function testGetEmbedData(): void
    {
        $diagram = Diagram::factory()->create(['name' => 'My Diagram', 'db_type' => 'mysql', 'schema' => '[]']);
        $this->assertEquals(['name' => 'My Diagram', 'db_type' => 'mysql', 'schema' => '[]'], $this->service->getEmbedData($diagram));
    }
}
