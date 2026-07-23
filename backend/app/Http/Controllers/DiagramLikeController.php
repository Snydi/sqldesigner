<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Diagram;
use App\Services\LibraryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiagramLikeController extends Controller
{
    public function __construct(private readonly LibraryService $libraryService) {}

    public function index(Request $request): JsonResponse
    {
        return $this->success([
            'diagram_ids' => $request->user()
                ->diagramLikes()
                ->pluck('diagram_id'),
        ]);
    }

    public function store(Request $request, Diagram $diagram): JsonResponse
    {
        $this->ensureDiagramIsInLibrary($diagram);

        $request->user()->diagramLikes()->firstOrCreate([
            'diagram_id' => $diagram->id,
        ]);
        $this->libraryService->invalidate();

        return $this->success([
            'liked' => true,
            'likes_count' => $diagram->likes()->count(),
        ]);
    }

    public function destroy(Request $request, Diagram $diagram): JsonResponse
    {
        $this->ensureDiagramIsInLibrary($diagram);

        $request->user()
            ->diagramLikes()
            ->where('diagram_id', $diagram->id)
            ->delete();
        $this->libraryService->invalidate();

        return $this->success([
            'liked' => false,
            'likes_count' => $diagram->likes()->count(),
        ]);
    }

    private function ensureDiagramIsInLibrary(Diagram $diagram): void
    {
        abort_unless($diagram->library && $diagram->share_access !== null, 404);
    }
}
