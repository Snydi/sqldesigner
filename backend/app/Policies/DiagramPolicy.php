<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\DiagramAccess;
use App\Enums\VisitorStatus;
use App\Models\Diagram;
use App\Models\DiagramVisitor;
use App\Models\User;

class DiagramPolicy
{
    public function view(User $user, Diagram $diagram): bool
    {
        return $this->ownsDiagram($user, $diagram);
    }

    public function update(User $user, Diagram $diagram): bool
    {
        return $this->ownsDiagram($user, $diagram);
    }

    public function delete(User $user, Diagram $diagram): bool
    {
        return $this->ownsDiagram($user, $diagram);
    }

    public function import(User $user, Diagram $diagram): bool
    {
        return $this->ownsDiagram($user, $diagram);
    }

    public function export(User $user, Diagram $diagram): bool
    {
        return $this->ownsDiagram($user, $diagram);
    }

    public function scan(User $user, Diagram $diagram): bool
    {
        if ($this->ownsDiagram($user, $diagram)) {
            return true;
        }

        $visitor = DiagramVisitor::where('diagram_id', $diagram->id)
            ->where('user_id', $user->id)
            ->where('status', VisitorStatus::APPROVED)
            ->first();

        if ($diagram->require_approval && $visitor === null) {
            return false;
        }

        if ($diagram->share_access === DiagramAccess::WRITE) {
            return true;
        }

        return $diagram->share_access === DiagramAccess::PER_USER
            && $visitor?->access === DiagramAccess::WRITE;
    }

    public function viewChangelog(User $user, Diagram $diagram): bool
    {
        if ($this->ownsDiagram($user, $diagram)) {
            return true;
        }

        return DiagramVisitor::where('diagram_id', $diagram->id)
            ->where('user_id', $user->id)
            ->where('status', VisitorStatus::APPROVED)
            ->exists();
    }

    public function addChangelog(User $user, Diagram $diagram): bool
    {
        if ($this->ownsDiagram($user, $diagram)) {
            return true;
        }

        if ($diagram->share_access === DiagramAccess::WRITE) {
            return true;
        }

        if ($diagram->share_access === DiagramAccess::PER_USER) {
            return DiagramVisitor::where('diagram_id', $diagram->id)
                ->where('user_id', $user->id)
                ->where('status', VisitorStatus::APPROVED)
                ->where('access', DiagramAccess::WRITE)
                ->exists();
        }

        return false;
    }

    private function ownsDiagram(User $user, Diagram $diagram): bool
    {
        return $user->id === $diagram->user_id;
    }
}
