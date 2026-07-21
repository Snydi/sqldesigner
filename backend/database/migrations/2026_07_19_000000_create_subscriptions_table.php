<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plan', 50);
            $table->string('status', 20)->index();
            $table->string('provider', 50)->default('robokassa');
            $table->string('provider_subscription_id', 191)->nullable();
            $table->unsignedInteger('amount_minor');
            $table->char('currency', 3)->default('USD');
            $table->timestampTz('starts_at')->nullable();
            $table->timestampTz('ends_at')->nullable()->index();
            $table->timestampTz('cancelled_at')->nullable();
            $table->timestampsTz();

            $table->index(['user_id', 'status', 'ends_at']);
            $table->unique(['provider', 'provider_subscription_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
