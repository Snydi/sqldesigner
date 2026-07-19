<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider', 50)->default('robokassa');
            // Filled with the numeric Payment id before redirecting to Robokassa.
            $table->string('provider_invoice_id', 32)->nullable();
            $table->string('provider_payment_id', 191)->nullable();
            $table->string('status', 20)->index();
            $table->unsignedInteger('amount_minor');
            $table->char('currency', 3)->default('USD');
            $table->unsignedInteger('fee_minor')->nullable();
            $table->string('payer_email')->nullable();
            $table->string('payment_method', 100)->nullable();
            $table->string('paid_currency_label', 100)->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestampTz('paid_at')->nullable();
            $table->timestampTz('failed_at')->nullable();
            $table->timestampsTz();

            $table->index(['user_id', 'created_at']);
            $table->index(['subscription_id', 'status']);
            $table->unique(['provider', 'provider_invoice_id']);
            $table->unique(['provider', 'provider_payment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
