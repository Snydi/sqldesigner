<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\ShareSettingsDTO;
use App\Enums\DiagramAccess;
use App\Enums\VisitorStatus;
use App\Models\Diagram;
use App\Models\DiagramVisitor;
use App\Models\User;
use App\Services\DiagramSharingService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiagramSharingServiceTest extends TestCase
{
    use DatabaseTransactions;

    private DiagramSharingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DiagramSharingService::class);
    }

    // --- ensureShared ---

    public function test_ensure_shared_sets_read_when_null(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => null]);
        $this->assertEquals(DiagramAccess::READ->value, $this->service->ensureShared($diagram));
        $this->assertDatabaseHas('diagrams', ['id' => $diagram->id, 'share_access' => DiagramAccess::READ->value]);
    }

    public function test_ensure_shared_returns_existing_access(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::WRITE]);
        $this->assertEquals(DiagramAccess::WRITE->value, $this->service->ensureShared($diagram));
    }

    // --- unshare ---

    public function test_unshare(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::WRITE, 'library' => true]);
        $this->service->unshare($diagram);
        $this->assertNull($diagram->share_access);
        $this->assertFalse((bool) $diagram->library);
    }

    // --- updateShareSettings ---

    public function test_update_share_settings(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::READ, 'require_approval' => false, 'library' => false]);
        $result = $this->service->updateShareSettings($diagram, new ShareSettingsDTO(DiagramAccess::PER_USER, true, true));
        $this->assertEquals(['share_access' => DiagramAccess::PER_USER->value, 'require_approval' => true, 'library' => true], $result);
    }

    public function test_update_share_settings_library_forces_per_user(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::READ, 'library' => false]);
        $result = $this->service->updateShareSettings($diagram, new ShareSettingsDTO(library: true));
        $this->assertEquals(DiagramAccess::PER_USER->value, $result['share_access']);
    }

    public function test_update_share_settings_library_skips_per_user_when_already(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::PER_USER, 'library' => false]);
        $result = $this->service->updateShareSettings($diagram, new ShareSettingsDTO(library: true));
        $this->assertEquals(DiagramAccess::PER_USER->value, $result['share_access']);
    }

    public function test_update_share_settings_null_inputs_skip(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::WRITE, 'require_approval' => false, 'library' => false]);
        $result = $this->service->updateShareSettings($diagram, new ShareSettingsDTO);
        $this->assertEquals(DiagramAccess::WRITE->value, $result['share_access']);
    }

    // --- getVisitors ---

    public function test_get_visitors(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create();
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => VisitorStatus::APPROVED, 'access' => DiagramAccess::READ]);
        $visitors = $this->service->getVisitors($diagram);
        $this->assertCount(1, $visitors);
        $this->assertEquals($user->id, $visitors[0]['user_id']);
        $this->assertEquals(VisitorStatus::APPROVED, $visitors[0]['status']);
    }

    public function test_remove_from_shared_with_me_deletes_users_visitor(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create();
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id]);

        $this->service->removeFromSharedWithMe($diagram, $user);

        $this->assertDatabaseMissing('diagram_visitors', ['diagram_id' => $diagram->id, 'user_id' => $user->id]);
    }

    // --- approveVisitor ---

    public function test_approve_visitor(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::READ]);
        $visitor = DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'status' => VisitorStatus::PENDING]);
        $this->assertEquals(VisitorStatus::APPROVED, $this->service->approveVisitor($diagram, $visitor)->status);
    }

    public function test_approve_visitor_per_user_sets_access_read(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::PER_USER]);
        $visitor = DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'status' => VisitorStatus::PENDING, 'access' => null]);
        $result = $this->service->approveVisitor($diagram, $visitor);
        $this->assertEquals(VisitorStatus::APPROVED, $result->status);
        $this->assertEquals(DiagramAccess::READ, $result->access);
    }

    // --- setVisitorAccess ---

    public function test_set_visitor_access_grant(): void
    {
        $diagram = Diagram::factory()->create();
        $visitor = DiagramVisitor::factory()->create(['diagram_id' => $diagram->id]);
        $result = $this->service->setVisitorAccess($diagram, $visitor, DiagramAccess::WRITE);
        $this->assertEquals(VisitorStatus::APPROVED, $result->status);
        $this->assertEquals(DiagramAccess::WRITE, $result->access);
    }

    public function test_set_visitor_access_revoke(): void
    {
        $diagram = Diagram::factory()->create();
        $visitor = DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'status' => VisitorStatus::APPROVED, 'access' => DiagramAccess::READ]);
        $result = $this->service->setVisitorAccess($diagram, $visitor, DiagramAccess::REVOKED);
        $this->assertEquals(VisitorStatus::REVOKED, $result->status);
        $this->assertNull($result->access);
    }

    // --- saveByToken ---

    public function test_save_by_token_per_user_write_access(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::PER_USER]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => VisitorStatus::APPROVED, 'access' => DiagramAccess::WRITE]);
        $this->assertTrue($this->service->saveByToken($diagram, $user, []));
    }

    public function test_save_by_token_per_user_read_only_returns_false(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::PER_USER]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => VisitorStatus::APPROVED, 'access' => DiagramAccess::READ]);
        $this->assertFalse($this->service->saveByToken($diagram, $user, []));
    }

    public function test_save_by_token_write_no_approval_required(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::WRITE, 'require_approval' => false]);
        $this->assertTrue($this->service->saveByToken($diagram, $user, []));
    }

    public function test_save_by_token_write_revoked_visitor_returns_false(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::WRITE, 'require_approval' => false]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => VisitorStatus::REVOKED]);
        $this->assertFalse($this->service->saveByToken($diagram, $user, []));
    }

    public function test_save_by_token_write_approval_required_approved(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::WRITE, 'require_approval' => true]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => VisitorStatus::APPROVED]);
        $this->assertTrue($this->service->saveByToken($diagram, $user, []));
    }

    public function test_save_by_token_write_approval_required_pending_returns_false(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::WRITE, 'require_approval' => true]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => VisitorStatus::PENDING]);
        $this->assertFalse($this->service->saveByToken($diagram, $user, []));
    }

    public function test_save_by_token_read_access_returns_false(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::READ]);
        $this->assertFalse($this->service->saveByToken($diagram, $user, []));
    }

    // --- resolveSharedAccess ---

    public function test_resolve_shared_access_owner(): void
    {
        $diagram = Diagram::factory()->create();
        $owner = User::find($diagram->user_id);
        $result = $this->service->resolveSharedAccess($diagram, $owner);
        $this->assertEquals('ok', $result['status']);
    }

    public function test_resolve_shared_access_not_shared(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => null]);
        $this->assertEquals(['status' => 'not_shared'], $this->service->resolveSharedAccess($diagram, User::factory()->create()));
    }

    public function test_resolve_shared_access_revoked(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::READ, 'require_approval' => false]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => VisitorStatus::REVOKED]);
        $this->assertEquals(['status' => VisitorStatus::REVOKED->value], $this->service->resolveSharedAccess($diagram, $user));
    }

    public function test_resolve_shared_access_per_user_approved(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::PER_USER, 'require_approval' => false]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => VisitorStatus::APPROVED, 'access' => DiagramAccess::WRITE]);
        $result = $this->service->resolveSharedAccess($diagram, $user);
        $this->assertEquals('ok', $result['status']);
        $this->assertEquals(DiagramAccess::WRITE, $result['diagram']->share_access);
    }

    public function test_resolve_shared_access_per_user_pending(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::PER_USER, 'require_approval' => false]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => VisitorStatus::PENDING]);
        $this->assertEquals(['status' => VisitorStatus::PENDING->value], $this->service->resolveSharedAccess($diagram, $user));
    }

    public function test_resolve_shared_access_require_approval_creates_visitor_and_returns_pending(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::READ, 'require_approval' => true]);
        $result = $this->service->resolveSharedAccess($diagram, $user);
        $this->assertEquals(['status' => VisitorStatus::PENDING->value], $result);
        $this->assertDatabaseHas('diagram_visitors', ['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => VisitorStatus::PENDING->value]);
    }

    public function test_resolve_shared_access_require_approval_approved(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::READ, 'require_approval' => true]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => VisitorStatus::APPROVED]);
        $this->assertEquals('ok', $this->service->resolveSharedAccess($diagram, $user)['status']);
    }

    public function test_resolve_shared_access_no_approval_required(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::READ, 'require_approval' => false]);
        $this->assertEquals('ok', $this->service->resolveSharedAccess($diagram, $user)['status']);
    }

    public function test_resolve_library_view_is_read_only_without_creating_visitor(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => DiagramAccess::PER_USER, 'library' => true]);

        $result = $this->service->resolveSharedAccess($diagram, $user, true);

        $this->assertEquals('ok', $result['status']);
        $this->assertEquals(DiagramAccess::READ, $result['diagram']->share_access);
        $this->assertDatabaseMissing('diagram_visitors', ['diagram_id' => $diagram->id, 'user_id' => $user->id]);
    }
}
