<?php

namespace App\Policies;

use App\Models\Diagram;
use App\Models\User;

class DiagramPolicy
{
    public function view(User $user, Diagram $diagram): bool
    {
        return $user->id === $diagram->user_id;
    }

    public function update(User $user, Diagram $diagram): bool
    {
        return $user->id === $diagram->user_id;
    }

    public function delete(User $user, Diagram $diagram): bool
    {
        return $user->id === $diagram->user_id;
    }

    public function import(User $user, Diagram $diagram): bool
    {
        return $user->id === $diagram->user_id;
    }

    public function export(User $user, Diagram $diagram): bool
    {
        return $user->id === $diagram->user_id;
    }
}
