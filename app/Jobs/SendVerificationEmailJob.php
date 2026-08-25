<?php

namespace App\Jobs;

use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendVerificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        public User   $user,
        public string $code
    ) {}

    public function handle(): void
    {
        Mail::to($this->user->email)
            ->send(new VerificationCodeMail($this->user, $this->code));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendVerificationEmailJob failed', [
            'user_id' => $this->user->id,
            'email'   => $this->user->email,
            'error'   => $exception->getMessage(),
        ]);
    }
}