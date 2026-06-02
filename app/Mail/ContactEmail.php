<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $email;
    public string $phone;
    public string $entreprise;
    public string $userMessage;

    public function __construct(string $name, string $email, string $phone, string $entreprise, string $userMessage)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->entreprise = $entreprise;
        $this->userMessage = $userMessage;
    }

    public function build()
    {
        return $this->subject('Nouveau message reçu - MadWork')
                    ->view('email.Contact')
                    ->with([
                        'name' => $this->name,
                        'email' => $this->email,
                        'phone' => $this->phone,
                        'entreprise' => $this->entreprise,
                        'userMessage' => $this->userMessage,
                    ]);
    }
}
