<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\DbType;
use App\Enums\ExportStatus;
use App\Models\Diagram;
use App\Models\User;
use App\Services\DiagramSqlService;
use App\Services\PlanLimitService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExportDiagramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public int $tries = 3;

    public int $backoff = 30;

    public bool $deleteWhenMissingModels = true;

    public function __construct(private Diagram $diagram, private int $userId)
    {
        $this->onQueue('diagrams');
    }

    /** @return array<int, object> */
    public function middleware(): array
    {
        return [new WithoutOverlapping((string) $this->diagram->id)];
    }

    public function handle(DiagramSqlService $service, PlanLimitService $planLimitService): void
    {
        ini_set('memory_limit', '512M');

        $this->diagram->export_status = ExportStatus::PROCESSING;
        $this->diagram->save();

        $schemaJson = json_encode($this->diagram->schema);
        $sqlScript = $service->createScript($schemaJson, ($this->diagram->db_type ?? DbType::MYSQL)->value);

        $json = $service->createJson($schemaJson);

        $user = User::find($this->userId);
        if (! $user || ! $planLimitService->consumeExportAllowance($user)) {
            $this->diagram->export_status = ExportStatus::FAILED;
            $this->diagram->export_error = 'Free plan is limited to 3 exports per day. Try again after midnight (UTC+3) or upgrade to Pro.';
            $this->diagram->save();

            return;
        }

        $this->diagram->script = $sqlScript;
        $this->diagram->export_json = $json;
        $this->diagram->export_status = ExportStatus::DONE;
        $this->diagram->export_error = null;
        $this->diagram->save();
    }

    public function failed(Throwable $exception): void
    {
        $this->diagram->export_status = ExportStatus::FAILED;
        $this->diagram->export_error = $exception->getMessage();
        $this->diagram->save();

        Log::error('ExportDiagramJob failed', [
            'diagram_id' => $this->diagram->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
