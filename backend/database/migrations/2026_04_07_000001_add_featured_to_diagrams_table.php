<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagrams', function (Blueprint $table) {
            $table->boolean('featured')->default(false)->after('library');
            $table->string('featured_url')->nullable()->after('featured');
        });
    }

    public function down(): void
    {
        Schema::table('diagrams', function (Blueprint $table) {
            $table->dropColumn(['featured', 'featured_url']);
        });
    }
};
