<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagram_visitors', function (Blueprint $table) {
            $table->string('access')->nullable()->after('status'); // read | write
        });
    }

    public function down(): void
    {
        Schema::table('diagram_visitors', function (Blueprint $table) {
            $table->dropColumn('access');
        });
    }
};
