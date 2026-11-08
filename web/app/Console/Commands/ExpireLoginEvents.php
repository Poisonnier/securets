<?php

namespace App\Console\Commands;

use App\Models\LoginEvent;
use Illuminate\Console\Command;

class ExpireLoginEvents extends Command
{
    protected $signature = 'integrations:expire-pending-events
                            {--minutes=45 : Mark pending events older than this many minutes as expired}';

    protected $description = 'Mark stale pending login events as expired so they do not queue indefinitely.';

    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');

        $count = LoginEvent::overduePending($minutes)
            ->update(['status' => 'expired']);

        $this->info(sprintf(
            '[%s] Expired %d pending login event(s) older than %d minutes.',
            now()->toDateTimeString(),
            $count,
            $minutes,
        ));

        return self::SUCCESS;
    }
}
