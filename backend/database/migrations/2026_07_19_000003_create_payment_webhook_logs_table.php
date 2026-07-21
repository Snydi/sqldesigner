<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider', 50)->default('robokassa');
            $table->string('provider_invoice_id', 32)->nullable()->index();
            $table->string('status', 20)->index();
            $table->unsignedSmallInteger('http_status');
            $table->string('message', 500);
            $table->json('payload')->nullable();
            $table->timestampsTz();

            $table->index(['provider', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_logs');
    }
};
