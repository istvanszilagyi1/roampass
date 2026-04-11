<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailSubject;
    public $emailContent;

    public function __construct($subject, $content)
    {
        $this->emailSubject = $subject;
        $this->emailContent = $content;
    }

    public function build()
    {
        return $this->subject($this->emailSubject)
                    ->view('emails.newsletter');
    }
}