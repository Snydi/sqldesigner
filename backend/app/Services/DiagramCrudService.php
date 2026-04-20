<?php

namespace App\Services;

use App\Models\Diagram;
use App\Models\User;
use App\Repositories\DiagramRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DiagramCrudService
{
    public function __construct(protected DiagramRepositoryInterface $diagramRepository)
    {
    }

    public function getUserDiagrams(User $user): Collection
    {
        return $this->diagramRepository->all($user);
    }

    public function createDiagram(array $data): Diagram
    {
        return $this->diagramRepository->create($data);
    }

    public function updateDiagram(Diagram $diagram, array $data): bool
    {
        return $this->diagramRepository->update($diagram, $data);
    }

    public function deleteDiagram(Diagram $diagram): bool
    {
        return $this->diagramRepository->delete($diagram);
    }

    public function getEmbedData(Diagram $diagram): array
    {
        return [
            'name'    => $diagram->name,
            'db_type' => $diagram->db_type,
            'schema'  => $diagram->schema,
        ];
    }
}
