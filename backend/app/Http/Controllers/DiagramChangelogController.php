<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiagramChangelogResource;
use App\Models\Diagram;
use App\Models\DiagramChangelog;
use App\Models\DiagramVisitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group("Diagrams")]
#[Subgroup("Changelog")]
class DiagramChangelogController extends Controller
{
    public function index(Diagram $diagram, Request $request): AnonymousResourceCollection|JsonResponse
    {
        $user = $request->user();

        if (!$this->userCanAccess($diagram, $user)) {
            abort(403);
        }

        $entries = DiagramChangelog::where('diagram_id', $diagram->id)
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        return DiagramChangelogResource::collection($entries);
    }

    public function store(Diagram $diagram, Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$this->userCanWrite($diagram, $user)) {
            abort(403);
        }

        $validated = $request->validate([
            'action' => 'required|string|max:100',
            'details' => 'nullable|array',
        ]);

        DiagramChangelog::create([
            'diagram_id' => $diagram->id,
            'user_id' => $user->id,
            'user_name' => $user->email,
            'action' => $validated['action'],
            'details' => $validated['details'] ?? null,
        ]);

        return response()->json(['status' => true]);
    }

    private function userCanAccess(Diagram $diagram, $user): bool
    {
        if ($diagram->user_id === $user->id) {
            return true;
        }

        return DiagramVisitor::where('diagram_id', $diagram->id)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->exists();
    }

    private function userCanWrite(Diagram $diagram, $user): bool
    {
        if ($diagram->user_id === $user->id) {
            return true;
        }

        if ($diagram->share_access === 'write') {
            return true;
        }

        if ($diagram->share_access === 'per_user') {
            return DiagramVisitor::where('diagram_id', $diagram->id)
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('access', 'write')
                ->exists();
        }

        return false;
    }
}
