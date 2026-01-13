<?php

namespace App\Mail;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $fullName;
    public string $username;
    public string $activationLink;
    public ?CarbonInterface $expiresAt;

    public function __construct(string $fullName, string $username, string $activationLink, ?CarbonInterface $expiresAt)
    {
        $this->fullName = $fullName;
        $this->username = $username;
        $this->activationLink = $activationLink;
        $this->expiresAt = $expiresAt;
    }

    public function build()
    {
        return $this->subject('Activate your GSO IAMS account')
            ->view('emails.account_activation');
    }
}
