<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $teamName;
    public $teamCode;
    public $inviterName;
    public $teamType;

    public function __construct(string $teamName, string $teamCode, string $inviterName, string $teamType = 'learning')
    {
        $this->teamName = $teamName;
        $this->teamCode = $teamCode;
        $this->inviterName = $inviterName;
        $this->teamType = $teamType;
    }

    public function build()
    {
        return $this->subject("You're invited to join \"{$this->teamName}\" on LearningGuided")
                    ->view('emails.team-invite');
    }
}
