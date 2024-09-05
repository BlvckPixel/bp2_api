<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $activationToken)
    {
        $this->user = $user;
        $this->token = $activationToken;    
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.accountActivation')
                    ->subject('Blvckpixel: Account Activation')
                    ->with([
                        'user' => $this->user,
                        'token' => $this->token
                    ]);
    }
}
