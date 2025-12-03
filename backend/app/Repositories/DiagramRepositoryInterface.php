<?php

namespace App\Repositories;

use App\Models\Diagram;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface DiagramRepositoryInterface
{
    public function all(User $user): Collection;

    public function find(int $id): Diagram;

    public function create(array $data): Diagram;

    public function update(Diagram $diagram, array $data): bool;

    public function delete(Diagram $diagram): bool;
}
