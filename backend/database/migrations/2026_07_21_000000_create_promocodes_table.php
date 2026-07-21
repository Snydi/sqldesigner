<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promocodes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->unsignedSmallInteger('duration_days');
            $table->foreignId('redeemed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampTz('redeemed_at')->nullable();
            $table->timestampsTz();

            $table->index('redeemed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promocodes');
    }
};
