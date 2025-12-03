<?php

namespace App\Repositories;

use App\Models\Diagram;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class DiagramRepository implements DiagramRepositoryInterface
{
    public function all(User $user): Collection
    {
        return $user->diagrams()->get();
    }

    public function find(int $id): Diagram //not used
    {
        return Diagram::find($id);
    }

    public function create(array $data): Diagram
    {
        return Diagram::create([
            'name' => $data['name'],
            'schema' => NULL,
            'user_id' => $data['user_id'],
        ]);
    }

    public function update(Diagram $diagram, $data): bool
    {
        return $diagram->update($data);
    }

    public function delete(Diagram $diagram): bool
    {
        return $diagram->delete();
    }
}
