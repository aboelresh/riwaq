<?php

namespace App\Jobs;

use App\Mail\TeamInviteMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTeamInviteEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(
        public string $toEmail,
        public string $teamName,
        public string $teamCode,
        public string $inviterName,
        public string $teamType = 'learning'
    ) {}

    public function handle(): void
    {
        Mail::to($this->toEmail)->send(
            new TeamInviteMail(
                $this->teamName,
                $this->teamCode,
                $this->inviterName,
                $this->teamType
            )
        );
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendTeamInviteEmailJob failed', [
            'to'    => $this->toEmail,
            'team'  => $this->teamName,
            'error' => $exception->getMessage(),
        ]);
    }
}