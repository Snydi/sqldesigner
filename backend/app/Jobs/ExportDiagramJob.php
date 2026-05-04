<?php

namespace App\Jobs;

use App\Models\Diagram;
use App\Services\DiagramSqlService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ExportDiagramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 1;

    public function __construct(private Diagram $diagram) {}

    public function handle(DiagramSqlService $service): void
    {
        ini_set('memory_limit', '512M');

        $this->diagram->export_status = 'processing';
        $this->diagram->save();

        try {
            $sqlScript = $service->createScript($this->diagram->schema, $this->diagram->db_type ?? 'mysql');
            $jsonExport = $service->createJson($this->diagram->schema);

            $this->diagram->script        = json_encode($sqlScript);
            $this->diagram->export_json   = $jsonExport;
            $this->diagram->export_status = 'done';
            $this->diagram->export_error  = null;
            $this->diagram->save();
        } catch (Throwable $e) {
            $this->diagram->export_status = 'failed';
            $this->diagram->export_error  = $e->getMessage();
            $this->diagram->save();
        }
    }
}
