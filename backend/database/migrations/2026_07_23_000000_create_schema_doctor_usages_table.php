<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schema_doctor_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('usage_date');
            $table->unsignedInteger('count')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'usage_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schema_doctor_usages');
    }
};
