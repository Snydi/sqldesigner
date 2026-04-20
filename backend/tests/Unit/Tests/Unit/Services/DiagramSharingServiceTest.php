<?php

namespace Tests\Unit\Services;

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

    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(DiagramSharingService::class);
    }

    // --- ensureShared ---

    public function testEnsureSharedSetsReadWhenNull(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => null]);
        $this->assertEquals('read', $this->service->ensureShared($diagram));
        $this->assertDatabaseHas('diagrams', ['id' => $diagram->id, 'share_access' => 'read']);
    }

    public function testEnsureSharedReturnsExistingAccess(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => 'write']);
        $this->assertEquals('write', $this->service->ensureShared($diagram));
    }

    // --- unshare ---

    public function testUnshare(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => 'write', 'library' => true]);
        $this->service->unshare($diagram);
        $this->assertNull($diagram->share_access);
        $this->assertFalse((bool) $diagram->library);
    }

    // --- updateShareSettings ---

    public function testUpdateShareSettings(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => 'read', 'require_approval' => false, 'library' => false]);
        $result = $this->service->updateShareSettings($diagram, 'per_user', true, true);
        $this->assertEquals(['share_access' => 'per_user', 'require_approval' => true, 'library' => true], $result);
    }

    public function testUpdateShareSettingsLibraryForcesPerUser(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => 'read', 'library' => false]);
        $result = $this->service->updateShareSettings($diagram, null, null, true);
        $this->assertEquals('per_user', $result['share_access']);
    }

    public function testUpdateShareSettingsLibrarySkipsPerUserWhenAlready(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => 'per_user', 'library' => false]);
        $result = $this->service->updateShareSettings($diagram, null, null, true);
        $this->assertEquals('per_user', $result['share_access']);
    }

    public function testUpdateShareSettingsNullInputsSkip(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => 'write', 'require_approval' => false, 'library' => false]);
        $result = $this->service->updateShareSettings($diagram, null, null, null);
        $this->assertEquals('write', $result['share_access']);
    }

    // --- getVisitors ---

    public function testGetVisitors(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create();
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => 'approved', 'access' => 'read']);
        $visitors = $this->service->getVisitors($diagram);
        $this->assertCount(1, $visitors);
        $this->assertEquals($user->id, $visitors[0]['user_id']);
        $this->assertEquals('approved', $visitors[0]['status']);
    }

    // --- approveVisitor ---

    public function testApproveVisitor(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => 'read']);
        $visitor = DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'status' => 'pending']);
        $this->assertEquals('approved', $this->service->approveVisitor($diagram, $visitor)->status);
    }

    public function testApproveVisitorPerUserSetsAccessRead(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => 'per_user']);
        $visitor = DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'status' => 'pending', 'access' => null]);
        $result = $this->service->approveVisitor($diagram, $visitor);
        $this->assertEquals('approved', $result->status);
        $this->assertEquals('read', $result->access);
    }

    // --- setVisitorAccess ---

    public function testSetVisitorAccessGrant(): void
    {
        $diagram = Diagram::factory()->create();
        $visitor = DiagramVisitor::factory()->create(['diagram_id' => $diagram->id]);
        $result = $this->service->setVisitorAccess($diagram, $visitor, 'write');
        $this->assertEquals('approved', $result->status);
        $this->assertEquals('write', $result->access);
    }

    public function testSetVisitorAccessRevoke(): void
    {
        $diagram = Diagram::factory()->create();
        $visitor = DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'status' => 'approved', 'access' => 'read']);
        $result = $this->service->setVisitorAccess($diagram, $visitor, 'revoke');
        $this->assertEquals('revoked', $result->status);
        $this->assertNull($result->access);
    }

    // --- saveByToken ---

    public function testSaveByTokenPerUserWriteAccess(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => 'per_user']);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => 'approved', 'access' => 'write']);
        $this->assertTrue($this->service->saveByToken($diagram, $user, '[]'));
    }

    public function testSaveByTokenPerUserReadOnlyReturnsFalse(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => 'per_user']);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => 'approved', 'access' => 'read']);
        $this->assertFalse($this->service->saveByToken($diagram, $user, '[]'));
    }

    public function testSaveByTokenWriteNoApprovalRequired(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => 'write', 'require_approval' => false]);
        $this->assertTrue($this->service->saveByToken($diagram, $user, '[]'));
    }

    public function testSaveByTokenWriteRevokedVisitorReturnsFalse(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => 'write', 'require_approval' => false]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => 'revoked']);
        $this->assertFalse($this->service->saveByToken($diagram, $user, '[]'));
    }

    public function testSaveByTokenWriteApprovalRequiredApproved(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => 'write', 'require_approval' => true]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => 'approved']);
        $this->assertTrue($this->service->saveByToken($diagram, $user, '[]'));
    }

    public function testSaveByTokenWriteApprovalRequiredPendingReturnsFalse(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => 'write', 'require_approval' => true]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => 'pending']);
        $this->assertFalse($this->service->saveByToken($diagram, $user, '[]'));
    }

    public function testSaveByTokenReadAccessReturnsFalse(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => 'read']);
        $this->assertFalse($this->service->saveByToken($diagram, $user, '[]'));
    }

    // --- resolveSharedAccess ---

    public function testResolveSharedAccessOwner(): void
    {
        $diagram = Diagram::factory()->create();
        $owner = User::find($diagram->user_id);
        $result = $this->service->resolveSharedAccess($diagram, $owner);
        $this->assertEquals('ok', $result['status']);
    }

    public function testResolveSharedAccessNotShared(): void
    {
        $diagram = Diagram::factory()->create(['share_access' => null]);
        $this->assertEquals(['status' => 'not_shared'], $this->service->resolveSharedAccess($diagram, User::factory()->create()));
    }

    public function testResolveSharedAccessRevoked(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => 'read', 'require_approval' => false]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => 'revoked']);
        $this->assertEquals(['status' => 'revoked'], $this->service->resolveSharedAccess($diagram, $user));
    }

    public function testResolveSharedAccessPerUserApproved(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => 'per_user', 'require_approval' => false]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => 'approved', 'access' => 'write']);
        $result = $this->service->resolveSharedAccess($diagram, $user);
        $this->assertEquals('ok', $result['status']);
        $this->assertEquals('write', $result['diagram']->share_access);
    }

    public function testResolveSharedAccessPerUserPending(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => 'per_user', 'require_approval' => false]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => 'pending']);
        $this->assertEquals(['status' => 'pending'], $this->service->resolveSharedAccess($diagram, $user));
    }

    public function testResolveSharedAccessRequireApprovalCreatesVisitorAndReturnsPending(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => 'read', 'require_approval' => true]);
        $result = $this->service->resolveSharedAccess($diagram, $user);
        $this->assertEquals(['status' => 'pending'], $result);
        $this->assertDatabaseHas('diagram_visitors', ['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => 'pending']);
    }

    public function testResolveSharedAccessRequireApprovalApproved(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => 'read', 'require_approval' => true]);
        DiagramVisitor::factory()->create(['diagram_id' => $diagram->id, 'user_id' => $user->id, 'status' => 'approved']);
        $this->assertEquals('ok', $this->service->resolveSharedAccess($diagram, $user)['status']);
    }

    public function testResolveSharedAccessNoApprovalRequired(): void
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['share_access' => 'read', 'require_approval' => false]);
        $this->assertEquals('ok', $this->service->resolveSharedAccess($diagram, $user)['status']);
    }
}
