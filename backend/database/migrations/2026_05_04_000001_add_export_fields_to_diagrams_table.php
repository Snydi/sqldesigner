<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagrams', function (Blueprint $table) {
            $table->string('export_status')->nullable()->after('import_error');
            $table->text('export_error')->nullable()->after('export_status');
            $table->text('export_json')->nullable()->after('export_error');
        });
    }

    public function down(): void
    {
        Schema::table('diagrams', function (Blueprint $table) {
            $table->dropColumn(['export_status', 'export_error', 'export_json']);
        });
    }
};
