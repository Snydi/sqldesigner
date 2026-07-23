<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Diagram;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LibraryRepository
{
    /**
     * @return Collection<int, Diagram>
     */
    public function getFeatured(): Collection
    {
        return Diagram::featured()
            ->select(['id', 'name', 'share_token', 'featured_url', 'updated_at'])
            ->withCount('likes')
            ->orderByDesc('updated_at')
            ->get();
    }

    /**
     * Return a paginated list of non-featured, user-shared library diagrams.
     *
     * @return LengthAwarePaginator<int, Diagram>
     */
    public function getSharedByUsersPaginated(string $sort = 'likes', int $perPage = 24): LengthAwarePaginator
    {
        $query = $this->baseQuery()
            ->select(['id', 'name', 'share_token', 'created_at', 'updated_at'])
            ->withCount('likes');

        if ($sort === 'newest') {
            $query->orderByDesc('created_at');
        } else {
            $query->orderByDesc('likes_count')
                ->orderByDesc('created_at');
        }

        return $query->paginate($perPage);
    }

    /** @return Builder<Diagram> */
    private function baseQuery(): Builder
    {
        return Diagram::library()
            ->where('featured', false)
            ->shared();
    }
}
