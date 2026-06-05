<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class UserStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $title;
    public string $messageBody;

    public function __construct(string $title, string $messageBody)
    {
        $this->title = $title;
        $this->messageBody = $messageBody;
    }

    public function build()
    {
        return $this->subject($this->title)
            ->html("
                <h3>{$this->title}</h3>
                <p>{$this->messageBody}</p>
            ");
    }
}