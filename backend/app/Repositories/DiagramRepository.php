<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\CreateDiagramDTO;
use App\DTOs\UpdateDiagramDTO;
use App\Enums\VisitorStatus;
use App\Models\Diagram;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class DiagramRepository implements DiagramRepositoryInterface
{
    /** @return Collection<int, Diagram> */
    public function all(User $user): Collection
    {
        return $user->diagrams()->get();
    }

    /** @return Collection<int, Diagram> */
    public function sharedWith(User $user): Collection
    {
        return Diagram::query()
            ->shared()
            ->where('user_id', '!=', $user->id)
            ->whereHas('visitors', function ($query) use ($user): void {
                $query
                    ->where('user_id', $user->id)
                    ->where('status', VisitorStatus::APPROVED);
            })
            ->get();
    }

    /** @deprecated Not used anywhere */
    public function find(int $id): Diagram
    {
        return Diagram::find($id);
    }

    public function create(CreateDiagramDTO $dto): Diagram
    {
        return Diagram::create([
            'name' => $dto->name,
            'db_type' => $dto->dbType,
            'schema' => $dto->schema,
            'user_id' => $dto->userId,
            'share_access' => $dto->shareAccess,
            'library' => $dto->library,
        ]);
    }

    public function update(Diagram $diagram, UpdateDiagramDTO $dto): bool
    {
        return $diagram->update($dto->toArray());
    }

    public function delete(Diagram $diagram): bool
    {
        return $diagram->delete();
    }
}
