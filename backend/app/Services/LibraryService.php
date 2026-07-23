<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Diagram;
use App\Repositories\LibraryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class LibraryService
{
    private const VERSION_KEY = 'library.v';

    private const CACHE_TTL = 3600;

    public function __construct(private readonly LibraryRepository $libraryRepository) {}

    /**
     * Return featured and paginated library diagrams, served from cache.
     *
     * @return array{featured: Collection<int, Diagram>, diagrams: LengthAwarePaginator<int, Diagram>}
     */
    public function getLibraryData(string $sort = 'likes', int $page = 1): array
    {
        $v = (int) Cache::get(self::VERSION_KEY, 0);

        $featured = Cache::remember("library.featured.likes.{$v}", self::CACHE_TTL, function () {
            return $this->libraryRepository->getFeatured();
        });

        /** @var LengthAwarePaginator<int, Diagram> $diagrams */
        $diagrams = Cache::remember("library.diagrams.{$v}.{$sort}.{$page}", self::CACHE_TTL, function () use ($sort) {
            return $this->libraryRepository
                ->getSharedByUsersPaginated($sort)
                ->withPath(url('/library'))
                ->appends($sort === 'newest' ? ['sort' => 'newest'] : []);
        });

        return compact('featured', 'diagrams');
    }

    /**
     * Bump the cache version key to invalidate all cached library pages.
     */
    public function invalidate(): void
    {
        $v = (int) Cache::get(self::VERSION_KEY, 0);
        Cache::forever(self::VERSION_KEY, $v + 1);
    }
}
