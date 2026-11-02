<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integrations', function (Blueprint $table) {
            $table->string('gate_token', 64)->nullable()->after('secret_key');
        });

        foreach (DB::table('integrations')->whereNull('gate_token')->get() as $row) {
            do {
                $gateToken = Str::random(40);
            } while (DB::table('integrations')->where('gate_token', $gateToken)->exists());

            DB::table('integrations')->where('id', $row->id)->update([
                'gate_token' => $gateToken,
            ]);
        }

        Schema::table('integrations', function (Blueprint $table) {
            $table->unique('gate_token');
        });
    }

    public function down(): void
    {
        Schema::table('integrations', function (Blueprint $table) {
            $table->dropUnique(['gate_token']);
            $table->dropColumn('gate_token');
        });
    }
};
