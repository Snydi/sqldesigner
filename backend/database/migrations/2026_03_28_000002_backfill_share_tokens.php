<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        DB::table('diagrams')->whereNull('share_token')->orderBy('id')->each(function ($diagram) {
            DB::table('diagrams')->where('id', $diagram->id)->update([
                'share_token' => Str::uuid()->toString(),
            ]);
        });
    }

    public function down(): void {}
};
