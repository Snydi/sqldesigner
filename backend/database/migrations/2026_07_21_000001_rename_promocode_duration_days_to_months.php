<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promocodes', function (Blueprint $table) {
            $table->renameColumn('duration_days', 'duration_months');
        });
    }

    public function down(): void
    {
        Schema::table('promocodes', function (Blueprint $table) {
            $table->renameColumn('duration_months', 'duration_days');
        });
    }
};
