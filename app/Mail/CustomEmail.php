<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $bodyContent;
    public $subjectLine;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $subject, $body)
    {
        $this->user = $user;
        $this->subjectLine = $subject;
        $this->bodyContent = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.customEmail')
            ->subject($this->subjectLine)  // Set custom subject
            ->with([                        // Pass body content to the email view
                'user' => $this->user,
                'bodyContent' => $this->bodyContent,
            ]);
    }
}
