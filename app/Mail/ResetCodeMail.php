<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code; // Variable pública para pasarla a la vista

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Código de Recuperación - SuperGym')
                    ->view('emails.reset-code'); // Apunta a la vista que crearemos abajo
    }
}