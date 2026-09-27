<?php

namespace App\Console\Commands;

use App\Models\AiUsageLog;
use Illuminate\Console\Command;

class CleanupAiLogs extends Command
{
    protected $signature   = 'app:cleanup-ai-logs';
    protected $description = 'Delete AI usage logs older than 3 months';

    public function handle(): void
    {
        $count = AiUsageLog::where('used_at', '<', now()->subMonths(3))->delete();
        $this->info("Deleted {$count} old AI usage log entries.");
    }
}