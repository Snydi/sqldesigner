<?php

namespace App\Services;

use App\Enums\DiagramAccess;
use App\Events\VisitorAccessChanged;
use App\Events\VisitorRequested;
use App\Models\Diagram;
use App\Models\DiagramVisitor;
use App\Models\User;
use Exception;

class DiagramSharingService
{
    public function ensureShared(Diagram $diagram): string
    {
        if (!$diagram->share_access) {
            $diagram->share_access = 'read';
            $diagram->save();
        }

        return $diagram->share_access;
    }

    public function unshare(Diagram $diagram): void
    {
        $diagram->share_access = null;
        $diagram->library = false;
        $diagram->save();
    }

    public function updateShareSettings(Diagram $diagram, ?string $access, ?bool $requireApproval, ?bool $library): array
    {
        if ($access !== null) {
            $diagram->share_access = $access;
        }

        if ($requireApproval !== null) {
            $diagram->require_approval = $requireApproval;
        }

        if ($library !== null) {
            $diagram->library = $library;
            if ($library && $diagram->share_access !== 'per_user') {
                $diagram->share_access = 'per_user';
            }
        }

        $diagram->save();

        return [
            'share_access'     => $diagram->share_access,
            'require_approval' => (bool) $diagram->require_approval,
            'library'          => (bool) $diagram->library,
        ];
    }

    public function getVisitors(Diagram $diagram): array
    {
        return $diagram->visitors()->with('user')->orderByDesc('created_at')->get()->map(fn($v) => [
            'id'      => $v->id,
            'user_id' => $v->user_id,
            'name'    => $v->user->name ?: $v->user->email,
            'email'   => $v->user->email,
            'status'  => $v->status,
            'access'  => $v->access,
        ])->all();
    }

    public function approveVisitor(Diagram $diagram, DiagramVisitor $visitor): DiagramVisitor
    {
        $visitor->status = 'approved';
        if ($diagram->share_access === 'per_user') {
            $visitor->access = $visitor->access ?? 'read';
        }
        $visitor->save();

        try {
            $accessStr = $diagram->share_access === 'per_user' ? ($visitor->access ?? 'read') : $diagram->share_access;
            broadcast(new VisitorAccessChanged($visitor->user_id, $diagram->share_token, DiagramAccess::from($accessStr)));
        } catch (Exception) {}

        return $visitor;
    }

    public function setVisitorAccess(Diagram $diagram, DiagramVisitor $visitor, string $access): DiagramVisitor
    {
        if ($access === 'revoke') {
            $visitor->status = 'revoked';
            $visitor->access = null;
        } else {
            $visitor->status = 'approved';
            $visitor->access = $access;
        }
        $visitor->save();

        try {
            $broadcastAccessStr = $visitor->status === 'revoked' ? 'revoked' : ($visitor->access ?? $diagram->share_access ?? 'read');
            broadcast(new VisitorAccessChanged($visitor->user_id, $diagram->share_token, DiagramAccess::from($broadcastAccessStr)));
        } catch (Exception) {}

        return $visitor;
    }

    public function saveByToken(Diagram $diagram, User $user, string $schema): bool
    {
        if (!$this->hasWriteAccess($diagram, $user)) {
            return false;
        }

        $diagram->schema = $schema;
        $diagram->save();

        return true;
    }

    public function resolveSharedAccess(Diagram $diagram, User $user): array
    {
        if ($user->id === $diagram->user_id) {
            return ['status' => 'ok', 'diagram' => $diagram];
        }

        if (!$diagram->share_access) {
            return ['status' => 'not_shared'];
        }

        $defaultStatus = $diagram->require_approval ? 'pending' : 'approved';
        $isPerUser     = $diagram->share_access === 'per_user';

        $visitor = DiagramVisitor::firstOrCreate(
            ['diagram_id' => $diagram->id, 'user_id' => $user->id],
            $isPerUser ? ['status' => $defaultStatus, 'access' => 'read'] : ['status' => $defaultStatus]
        );

        if ($visitor->wasRecentlyCreated && $diagram->require_approval) {
            try {
                broadcast(new VisitorRequested($diagram->share_token));
            } catch (Exception) {}
        }

        if ($visitor->status === 'revoked') return ['status' => 'revoked'];

        if ($isPerUser) {
            if ($visitor->status !== 'approved') return ['status' => 'pending'];
            $diagram->share_access = $visitor->access ?? 'read';
        } else {
            if ($diagram->require_approval && $visitor->status !== 'approved') return ['status' => 'pending'];
        }

        return ['status' => 'ok', 'diagram' => $diagram];
    }

    private function hasWriteAccess(Diagram $diagram, User $user): bool
    {
        if ($diagram->share_access === 'per_user') {
            return DiagramVisitor::where('diagram_id', $diagram->id)
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('access', 'write')
                ->exists();
        }

        if ($diagram->share_access === 'write') {
            $visitor = DiagramVisitor::where('diagram_id', $diagram->id)
                ->where('user_id', $user->id)
                ->first();

            if ($visitor?->status === 'revoked') return false;
            if ($diagram->require_approval) return $visitor?->status === 'approved';
            return true;
        }

        return false;
    }
}
