<?php

namespace App\Repositories;

use App\Models\Diagram;
use Illuminate\Database\Eloquent\Collection;

class DiagramRepository implements DiagramRepositoryInterface
{
    public function all($request): Collection
    {
        return $request->user()->diagrams()->get();
    }

    public function find($id)
    {
        return Diagram::find($id);
    }

    public function create($request): void
    {
        Diagram::create([
            'name' => $request->name,
            'schema' => NULL,
            'user_id' => $request->user()->id
        ]);
    }

    public function update($id, $data): void
    {
        $diagram = $this->find($id);
        $diagram->update($data);
    }

    public function delete($id): void
    {
        Diagram::destroy($id);
    }
}
