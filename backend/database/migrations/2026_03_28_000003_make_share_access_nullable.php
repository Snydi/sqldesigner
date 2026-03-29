<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('diagrams', function (Blueprint $table) {
            $table->string('share_access')->nullable()->default(null)->change();
        });

        DB::table('diagrams')->where('share_access', 'read')->update(['share_access' => null]);
    }

    public function down(): void
    {
        Schema::table('diagrams', function (Blueprint $table) {
            $table->string('share_access')->nullable(false)->default('read')->change();
        });
    }
};
