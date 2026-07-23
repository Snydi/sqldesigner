<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ExportUsage;
use App\Models\FeatureFlag;
use App\Models\SchemaDoctorUsage;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PlanLimitService
{
    public const FLAG_KEY = 'plan_limits_enabled';

    public const SCHEMA_DOCTOR_DAILY_LIMIT = 3;

    public const DIAGRAM_LIMIT = 1;

    public const EXPORT_DAILY_LIMIT = 3;

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

    public function schemaDoctorLimit(User $user): ?int
    {
        return ($this->limitsEnabled() && ! $user->isPro()) ? self::SCHEMA_DOCTOR_DAILY_LIMIT : null;
    }

    public function schemaDoctorScansUsedToday(User $user): int
    {
        return (int) (SchemaDoctorUsage::where('user_id', $user->id)
            ->where('usage_date', $this->mskToday())
            ->value('count') ?? 0);
    }

    public function schemaDoctorLimitReached(User $user): bool
    {
        $limit = $this->schemaDoctorLimit($user);

        return $limit !== null && $this->schemaDoctorScansUsedToday($user) >= $limit;
    }

    /**
     * @return array{limited: bool, limit: int|null, used: int, remaining: int|null, resets_at: string|null}
     */
    public function schemaDoctorAllowance(User $user): array
    {
        $limit = $this->schemaDoctorLimit($user);
        $used = $limit === null ? 0 : $this->schemaDoctorScansUsedToday($user);

        return [
            'limited' => $limit !== null,
            'limit' => $limit,
            'used' => $used,
            'remaining' => $limit === null ? null : max(0, $limit - $used),
            'resets_at' => $limit === null ? null : Carbon::now('Europe/Moscow')->addDay()->startOfDay()->toIso8601String(),
        ];
    }

    /**
     * Atomically consumes one MSK daily export allowance when applicable.
     *
     * The conditional upsert makes checking the limit and incrementing the
     * count one PostgreSQL statement, so concurrent requests cannot exceed it.
     */
    public function consumeExportAllowance(User $user): bool
    {
        if (! $this->limitsEnabled() || $user->isPro()) {
            return true;
        }

        $now = now();
        $result = DB::select(
            <<<'SQL'
                INSERT INTO export_usages (user_id, usage_date, count, created_at, updated_at)
                VALUES (?, ?, 1, ?, ?)
                ON CONFLICT (user_id, usage_date) DO UPDATE
                SET count = export_usages.count + 1, updated_at = EXCLUDED.updated_at
                WHERE export_usages.count < ?
                RETURNING count
            SQL,
            [$user->id, $this->mskToday(), $now, $now, self::EXPORT_DAILY_LIMIT]
        );

        return $result !== [];
    }

    /**
     * Atomically consumes one MSK daily Schema Doctor allowance when applicable.
     */
    public function consumeSchemaDoctorAllowance(User $user): bool
    {
        if (! $this->limitsEnabled() || $user->isPro()) {
            return true;
        }

        $now = now();
        $result = DB::select(
            <<<'SQL'
                INSERT INTO schema_doctor_usages (user_id, usage_date, count, created_at, updated_at)
                VALUES (?, ?, 1, ?, ?)
                ON CONFLICT (user_id, usage_date) DO UPDATE
                SET count = schema_doctor_usages.count + 1, updated_at = EXCLUDED.updated_at
                WHERE schema_doctor_usages.count < ?
                RETURNING count
            SQL,
            [$user->id, $this->mskToday(), $now, $now, self::SCHEMA_DOCTOR_DAILY_LIMIT]
        );

        return $result !== [];
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
