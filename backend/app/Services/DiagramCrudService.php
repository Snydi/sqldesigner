<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateDiagramDTO;
use App\DTOs\UpdateDiagramDTO;
use App\Models\Diagram;
use App\Models\User;
use App\Repositories\DiagramRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DiagramCrudService
{
    public function __construct(protected DiagramRepositoryInterface $diagramRepository) {}

    /** @return Collection<int, Diagram> */
    public function getUserDiagrams(User $user): Collection
    {
        return $this->diagramRepository->all($user);
    }

    /** @return Collection<int, Diagram> */
    public function getSharedDiagrams(User $user): Collection
    {
        return $this->diagramRepository->sharedWith($user);
    }

    public function createDiagram(CreateDiagramDTO $dto): Diagram
    {
        return $this->diagramRepository->create($dto);
    }

    public function updateDiagram(Diagram $diagram, UpdateDiagramDTO $dto): bool
    {
        return $this->diagramRepository->update($diagram, $dto);
    }

    public function deleteDiagram(Diagram $diagram): bool
    {
        return $this->diagramRepository->delete($diagram);
    }

    /** @return array{name: string, db_type: string, schema: mixed} */
    public function getEmbedData(Diagram $diagram): array
    {
        return [
            'name' => $diagram->name,
            'db_type' => $diagram->db_type->value,
            'schema' => $diagram->schema,
        ];
    }
}
