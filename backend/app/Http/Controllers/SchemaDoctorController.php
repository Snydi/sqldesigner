<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Diagram;
use App\Models\User;
use App\Services\PlanLimitService;
use App\Services\SchemaDoctorService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;

#[Group('Schema Doctor')]
class SchemaDoctorController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly SchemaDoctorService $schemaDoctor,
        private readonly PlanLimitService $planLimits,
    ) {}

    public function scan(Diagram $diagram, Request $request): JsonResponse
    {
        $this->authorize('scan', $diagram);

        /** @var User $user */
        $user = $request->user();

        if ($this->planLimits->schemaDoctorLimitReached($user)) {
            return $this->limitReached($user);
        }

        $diagnostics = $this->schemaDoctor->scan($diagram->fresh());

        if (! $this->planLimits->consumeSchemaDoctorAllowance($user)) {
            return $this->limitReached($user);
        }

        $summary = ['errors' => 0, 'warnings' => 0, 'suggestions' => 0];
        foreach ($diagnostics as $diagnostic) {
            $key = match ($diagnostic['severity']) {
                'error' => 'errors',
                'warning' => 'warnings',
                default => 'suggestions',
            };
            $summary[$key]++;
        }

        return $this->success([
            'diagnostics' => $diagnostics,
            'summary' => $summary,
            'allowance' => $this->planLimits->schemaDoctorAllowance($user->fresh()),
        ]);
    }

    private function limitReached(User $user): JsonResponse
    {
        return $this->success([
            'message' => 'Free plan is limited to '.PlanLimitService::SCHEMA_DOCTOR_DAILY_LIMIT.' Schema Doctor scans per day. Try again after midnight (MSK) or upgrade to Pro.',
            'allowance' => $this->planLimits->schemaDoctorAllowance($user),
        ], 403);
    }
}
