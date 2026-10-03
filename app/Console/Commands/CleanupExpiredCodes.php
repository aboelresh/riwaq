<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanupExpiredCodes extends Command
{
    protected $signature   = 'app:cleanup-expired-codes';
    protected $description = 'Clear expired verification and password reset codes';

    public function handle(): void
    {
        $count = User::whereNotNull('verification_code')
            ->where('verification_code_expires_at', '<', now())
            ->update([
                'verification_code'            => null,
                'verification_code_expires_at' => null,
            ]);

        $this->info("Cleared {$count} expired verification codes.");
    }
}