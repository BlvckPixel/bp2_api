<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AllTemplateEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $bodyContent;
    public $subjectLine;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $subject, $body)
    {
        $this->name = $name;
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
        return $this->view('emails.allCustom')
            ->subject($this->subjectLine)  // Set custom subject
            ->with([                        // Pass body content to the email view
                'name' => $this->name,
                'bodyContent' => $this->bodyContent,
            ]);
    }
}
