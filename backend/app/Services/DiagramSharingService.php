<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ShareSettingsDTO;
use App\Enums\DiagramAccess;
use App\Enums\VisitorStatus;
use App\Events\VisitorAccessChanged;
use App\Events\VisitorRequested;
use App\Models\Diagram;
use App\Models\DiagramVisitor;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class DiagramSharingService
{
    public function __construct(private readonly LibraryService $libraryService) {}

    /**
     * Ensure the diagram is shared (defaults to 'read' if not already set).
     *
     * @return string The current share_access value.
     */
    public function ensureShared(Diagram $diagram): string
    {
        if (! $diagram->share_access) {
            $diagram->share_access = DiagramAccess::READ;
            $diagram->save();

            if ($diagram->library) {
                $this->libraryService->invalidate();
            }
        }

        return ($diagram->share_access ?? DiagramAccess::READ)->value;
    }

    /**
     * Remove sharing and library visibility from the diagram.
     */
    public function unshare(Diagram $diagram): void
    {
        $diagram->share_access = null;
        $diagram->library = false;
        $diagram->save();
        $this->libraryService->invalidate();
    }

    /** @return array{share_access: string|null, require_approval: bool, library: bool} */
    public function updateShareSettings(Diagram $diagram, ShareSettingsDTO $dto): array
    {
        if ($dto->access !== null) {
            $diagram->share_access = $dto->access;
        }

        if ($dto->requireApproval !== null) {
            $diagram->require_approval = $dto->requireApproval;
        }

        if ($dto->library !== null) {
            $diagram->library = $dto->library;
            if ($dto->library && $diagram->share_access !== DiagramAccess::PER_USER) {
                $diagram->share_access = DiagramAccess::PER_USER;
            }
        }

        $diagram->save();

        if ($dto->library !== null) {
            $this->libraryService->invalidate();
        }

        return [
            'share_access' => $diagram->share_access?->value,
            'require_approval' => (bool) $diagram->require_approval,
            'library' => (bool) $diagram->library,
        ];
    }

    /**
     * Return all visitors for a diagram, newest first.
     *
     * @return Collection<int, DiagramVisitor>
     */
    public function getVisitors(Diagram $diagram): Collection
    {
        return $diagram->visitors()->with('user')->orderByDesc('created_at')->get();
    }

    /**
     * Remove a diagram from a user's shared list without affecting other visitors.
     */
    public function removeFromSharedWithMe(Diagram $diagram, User $user): void
    {
        $diagram->visitors()->where('user_id', $user->id)->delete();
    }

    /**
     * Approve a visitor and broadcast the access change.
     */
    public function approveVisitor(Diagram $diagram, DiagramVisitor $visitor): DiagramVisitor
    {
        $visitor->status = VisitorStatus::APPROVED;
        if ($diagram->share_access === DiagramAccess::PER_USER) {
            $visitor->access = $visitor->access ?? DiagramAccess::READ;
        }
        $visitor->save();

        try {
            $access = $diagram->share_access === DiagramAccess::PER_USER
                ? ($visitor->access ?? DiagramAccess::READ)
                : ($diagram->share_access ?? DiagramAccess::READ);
            broadcast(new VisitorAccessChanged($visitor->user_id, $diagram->share_token, $access));
        } catch (Exception) {
        }

        return $visitor;
    }

    /**
     * Set a visitor's access level (or revoke it) and broadcast the change.
     */
    public function setVisitorAccess(Diagram $diagram, DiagramVisitor $visitor, DiagramAccess $access): DiagramVisitor
    {
        if ($access === DiagramAccess::REVOKED) {
            $visitor->status = VisitorStatus::REVOKED;
            $visitor->access = null;
        } else {
            $visitor->status = VisitorStatus::APPROVED;
            $visitor->access = $access;
        }
        $visitor->save();

        try {
            $broadcastAccess = $visitor->status === VisitorStatus::REVOKED
                ? DiagramAccess::REVOKED
                : ($visitor->access ?? $diagram->share_access ?? DiagramAccess::READ);
            broadcast(new VisitorAccessChanged($visitor->user_id, $diagram->share_token, $broadcastAccess));
        } catch (Exception) {
        }

        return $visitor;
    }

    /**
     * Save a schema update from a shared user if they have write access.
     *
     * @param  array<string, mixed>  $schema
     */
    public function saveByToken(Diagram $diagram, User $user, array $schema): bool
    {
        if (! $this->hasWriteAccess($diagram, $user)) {
            return false;
        }

        $diagram->schema = $schema;
        $diagram->save();

        return true;
    }

    /**
     * Resolve the shared access for a user and return the diagram if permitted.
     *
     * @return array{status: string, diagram?: Diagram}
     */
    public function resolveSharedAccess(Diagram $diagram, User $user, bool $libraryView = false): array
    {
        if ($user->id === $diagram->user_id) {
            return ['status' => 'ok', 'diagram' => $diagram];
        }

        if (! $diagram->share_access) {
            return ['status' => 'not_shared'];
        }

        // The public library is explicitly read-only. Browsing from it must not
        // create a visitor record or make the diagram appear in "Shared with me".
        if ($libraryView && $diagram->library) {
            $diagram->share_access = DiagramAccess::READ;

            return ['status' => 'ok', 'diagram' => $diagram];
        }

        $defaultStatus = $diagram->require_approval ? VisitorStatus::PENDING : VisitorStatus::APPROVED;
        $isPerUser = $diagram->share_access === DiagramAccess::PER_USER;

        $visitor = DiagramVisitor::firstOrCreate(
            ['diagram_id' => $diagram->id, 'user_id' => $user->id],
            $isPerUser ? ['status' => $defaultStatus, 'access' => DiagramAccess::READ] : ['status' => $defaultStatus]
        );

        if ($visitor->wasRecentlyCreated && $diagram->require_approval) {
            try {
                broadcast(new VisitorRequested($diagram->share_token));
            } catch (Exception) {
            }
        }

        if ($visitor->status === VisitorStatus::REVOKED) {
            return ['status' => 'revoked'];
        }

        if ($isPerUser) {
            if ($visitor->status !== VisitorStatus::APPROVED) {
                return ['status' => 'pending'];
            }
            $diagram->share_access = $visitor->access ?? DiagramAccess::READ;
        } else {
            if ($diagram->require_approval && $visitor->status !== VisitorStatus::APPROVED) {
                return ['status' => 'pending'];
            }
        }

        return ['status' => 'ok', 'diagram' => $diagram];
    }

    private function hasWriteAccess(Diagram $diagram, User $user): bool
    {
        if ($diagram->share_access === DiagramAccess::PER_USER) {
            return DiagramVisitor::where('diagram_id', $diagram->id)
                ->where('user_id', $user->id)
                ->where('status', VisitorStatus::APPROVED)
                ->where('access', DiagramAccess::WRITE)
                ->exists();
        }

        if ($diagram->share_access === DiagramAccess::WRITE) {
            $visitor = DiagramVisitor::where('diagram_id', $diagram->id)
                ->where('user_id', $user->id)
                ->first();

            if ($visitor?->status === VisitorStatus::REVOKED) {
                return false;
            }
            if ($diagram->require_approval) {
                return $visitor?->status === VisitorStatus::APPROVED;
            }

            return true;
        }

        return false;
    }
}
