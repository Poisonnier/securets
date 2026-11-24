<?php

namespace App\Console\Commands;

use App\Models\LoginEvent;
use Illuminate\Console\Command;

class ExpireLoginEvents extends Command
{
    protected $signature = 'integrations:expire-pending-events
                            {--seconds='.LoginEvent::PENDING_EXPIRY_SECONDS.' : Mark pending events older than this many seconds as expired}
                            {--minutes= : Legacy override in minutes}';

    protected $description = 'Mark stale pending login events as expired so they do not queue indefinitely.';

    public function handle(): int
    {
        $minutesOption = $this->option('minutes');
        $seconds = $minutesOption !== null && $minutesOption !== ''
            ? (int) $minutesOption * 60
            : (int) $this->option('seconds');

        $count = LoginEvent::overduePending($seconds)
            ->update(['status' => 'expired']);

        $this->info(sprintf(
            '[%s] Expired %d pending login event(s) older than %d seconds.',
            now()->toDateTimeString(),
            $count,
            $seconds,
        ));

        return self::SUCCESS;
    }
}
