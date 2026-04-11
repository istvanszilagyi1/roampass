<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\GymPass;

class PassExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pass;

    public function __construct(GymPass $pass)
    {
        $this->pass = $pass;
    }

    public function build()
    {
        return $this->subject('Figyelem: Hamarosan lejár a bérleted! ⏳')
                    ->view('emails.pass_expiring');
    }
}