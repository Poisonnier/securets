<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('extension_token');
            $table->string('oauth_exchange_code', 64)->nullable()->index()->after('google_id');
            $table->timestamp('oauth_exchange_expires_at')->nullable()->after('oauth_exchange_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['google_id']);
            $table->dropIndex(['oauth_exchange_code']);
            $table->dropColumn([
                'google_id',
                'oauth_exchange_code',
                'oauth_exchange_expires_at',
            ]);
        });
    }
};
