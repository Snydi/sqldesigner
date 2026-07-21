<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\FeatureFlag;
use App\Services\PlanLimitService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class TogglePlanLimits extends Command
{
    protected $signature = 'limits:toggle {state? : Pass "on" or "off" to set explicitly, omit to toggle}';

    protected $description = 'Enable or disable the free-plan diagram/export limits (feature flag switch)';

    public function handle(): void
    {
        $state = $this->argument('state');

        if ($state !== null && ! in_array($state, ['on', 'off'], true)) {
            $this->error('State must be "on" or "off".');

            return;
        }

        $flag = FeatureFlag::firstOrCreate(['key' => PlanLimitService::FLAG_KEY], ['enabled' => false]);

        $flag->enabled = match ($state) {
            'on' => true,
            'off' => false,
            default => ! $flag->enabled,
        };
        $flag->save();

        Cache::forget('feature_flag:'.PlanLimitService::FLAG_KEY);

        $this->info('Plan limits are now '.($flag->enabled ? 'ENABLED' : 'DISABLED').'.');
    }
}
