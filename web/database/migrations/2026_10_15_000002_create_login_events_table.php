<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address')->nullable();
            $table->string('device')->nullable();          // browser/OS string
            $table->enum('status', ['pending', 'approved', 'declined', 'auto'])
                ->default('pending');
            $table->boolean('is_first_login')->default(false);
            $table->string('credential_username')->nullable(); // saved on first login
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_events');
    }
};
