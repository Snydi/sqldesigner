<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ExportUsage;
use App\Models\FeatureFlag;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class PlanLimitService
{
    public const FLAG_KEY = 'plan_limits_enabled';

    private const DIAGRAM_LIMIT = 1;

    private const EXPORT_DAILY_LIMIT = 3;

    public function limitsEnabled(): bool
    {
        return Cache::rememberForever(
            'feature_flag:'.self::FLAG_KEY,
            fn () => (bool) (FeatureFlag::where('key', self::FLAG_KEY)->value('enabled') ?? false)
        );
    }

    public function diagramLimitReached(User $user): bool
    {
        if (! $this->limitsEnabled() || $user->isPro()) {
            return false;
        }

        return $user->diagrams()->count() >= self::DIAGRAM_LIMIT;
    }

    public function exportLimitReached(User $user): bool
    {
        if (! $this->limitsEnabled() || $user->isPro()) {
            return false;
        }

        return $this->todaysExportCount($user) >= self::EXPORT_DAILY_LIMIT;
    }

    public function diagramLimit(User $user): ?int
    {
        return ($this->limitsEnabled() && ! $user->isPro()) ? self::DIAGRAM_LIMIT : null;
    }

    public function exportLimit(User $user): ?int
    {
        return ($this->limitsEnabled() && ! $user->isPro()) ? self::EXPORT_DAILY_LIMIT : null;
    }

    public function exportsUsedToday(User $user): int
    {
        return $this->todaysExportCount($user);
    }

    public function recordExport(User $user): void
    {
        $usage = ExportUsage::firstOrNew([
            'user_id' => $user->id,
            'usage_date' => $this->mskToday(),
        ]);
        $usage->count = ($usage->count ?? 0) + 1;
        $usage->save();
    }

    private function todaysExportCount(User $user): int
    {
        return (int) (ExportUsage::where('user_id', $user->id)
            ->where('usage_date', $this->mskToday())
            ->value('count') ?? 0);
    }

    private function mskToday(): string
    {
        return Carbon::now('Europe/Moscow')->toDateString();    //UTC+3
    }
}
