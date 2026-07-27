<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('consent_type', 50);
            $table->string('document_version', 20);
            $table->string('document_url', 255);
            $table->text('consent_text');
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestampTz('accepted_at');

            $table->index(['user_id', 'accepted_at']);
            $table->index(['payment_id', 'consent_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_consents');
    }
};
