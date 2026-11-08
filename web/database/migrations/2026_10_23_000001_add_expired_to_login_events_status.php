<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds 'expired' to the login_events.status column.
 *
 * Laravel's Blueprint::enum() compiles to a VARCHAR with a CHECK constraint
 * on PostgreSQL (no separate enum type is created). We therefore:
 *   1. Drop the existing check constraint.
 *   2. Add a new one that includes 'expired'.
 *
 * On SQLite (used in tests) enums are plain VARCHAR — no constraint to manage.
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            // Drop Laravel's auto-generated check constraint and replace it.
            // The name Laravel generates follows the pattern:
            //   {table}_{column}_check
            DB::statement(
                'ALTER TABLE login_events DROP CONSTRAINT IF EXISTS login_events_status_check'
            );
            DB::statement(
                "ALTER TABLE login_events ADD CONSTRAINT login_events_status_check
                 CHECK (status IN ('pending','approved','declined','auto','expired'))"
            );
        }
        // SQLite: VARCHAR column already accepts any value — nothing to alter.
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE login_events DROP CONSTRAINT IF EXISTS login_events_status_check'
            );
            DB::statement(
                "ALTER TABLE login_events ADD CONSTRAINT login_events_status_check
                 CHECK (status IN ('pending','approved','declined','auto'))"
            );
        }
    }
};
