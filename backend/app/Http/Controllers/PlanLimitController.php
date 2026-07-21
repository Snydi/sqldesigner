<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PlanLimitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;

#[Group('Plan Limits')]
class PlanLimitController extends Controller
{
    public function __construct(private readonly PlanLimitService $planLimitService) {}

    public function show(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return $this->success([
            'diagram_limit' => $this->planLimitService->diagramLimit($user),
            'diagram_count' => $user->diagrams()->count(),
            'export_limit' => $this->planLimitService->exportLimit($user),
            'exports_used_today' => $this->planLimitService->exportsUsedToday($user),
        ]);
    }
}
