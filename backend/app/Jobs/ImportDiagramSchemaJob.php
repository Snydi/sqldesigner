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

class ImportDiagramSchemaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 1;

    public function __construct(private Diagram $diagram) {}

    public function handle(DiagramSqlService $service): void
    {
        ini_set('memory_limit', '512M');

        $this->diagram->import_status = 'processing';
        $this->diagram->save();

        try {
            $schema = $service->createSchema(json_decode($this->diagram->script));

            $this->diagram->schema        = $schema;
            $this->diagram->import_status = 'done';
            $this->diagram->import_error  = null;
            $this->diagram->save();
        } catch (Throwable $e) {
            $this->diagram->import_status = 'failed';
            $this->diagram->import_error  = $e->getMessage();
            $this->diagram->save();
        }
    }
}
