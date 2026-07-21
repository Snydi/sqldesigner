<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ExportUsage;
use App\Models\FeatureFlag;
use App\Models\User;
use App\Services\PlanLimitService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PlanLimitConcurrencyTest extends TestCase
{
    public function test_fourth_simultaneous_free_export_allowance_is_rejected(): void
    {
        if (! function_exists('pcntl_fork')) {
            $this->markTestSkipped('pcntl is required to verify concurrent database connections.');
        }

        $originalFlag = FeatureFlag::where('key', PlanLimitService::FLAG_KEY)->first();
        $user = User::factory()->create(['email_verified_at' => now()]);

        try {
            FeatureFlag::updateOrCreate(['key' => PlanLimitService::FLAG_KEY], ['enabled' => true]);
            Cache::forget('feature_flag:'.PlanLimitService::FLAG_KEY);
            DB::disconnect();

            $children = [];
            for ($attempt = 0; $attempt < 4; $attempt++) {
                $pid = pcntl_fork();
                $this->assertNotSame(-1, $pid, 'Unable to fork an allowance verification process.');

                if ($pid === 0) {
                    DB::purge();
                    $attemptUser = User::findOrFail($user->id);
                    exit(app(PlanLimitService::class)->consumeExportAllowance($attemptUser) ? 0 : 1);
                }

                $children[] = $pid;
            }

            $successful = 0;
            foreach ($children as $pid) {
                pcntl_waitpid($pid, $status);
                $successful += pcntl_wexitstatus($status) === 0 ? 1 : 0;
            }

            $this->assertSame(3, $successful);
            $this->assertSame(3, ExportUsage::where('user_id', $user->id)->value('count'));
        } finally {
            ExportUsage::where('user_id', $user->id)->delete();
            $user->delete();

            if ($originalFlag) {
                $originalFlag->save();
            } else {
                FeatureFlag::where('key', PlanLimitService::FLAG_KEY)->delete();
            }
            Cache::forget('feature_flag:'.PlanLimitService::FLAG_KEY);
        }
    }
}
