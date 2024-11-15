<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailTemplate;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    /**
     * Create a new message instance.
     *
     * @param string $token
     * @return void
     */
    public function __construct($token, $user)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $template = EmailTemplate::where('name', 'Password Reset')->firstOrFail();


        $parsedBody = str_replace(
            ['{{ $user }}', '{{ $token }}'],
            [$this->user, $this->token],
            $template->body
        );

        return $this->html($parsedBody)
            ->subject($template->subject);

        // return $this->view('emails.password_reset')
        //     ->with([
        //         'user' => $this->user,
        //         'token' => $this->token,
        //     ]);
    }
}
