<?php

use App\Console\Commands\ExpireLoginEvents;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Mark stale pending approval events as expired every 5 minutes.
Schedule::command(ExpireLoginEvents::class)->everyFiveMinutes();
