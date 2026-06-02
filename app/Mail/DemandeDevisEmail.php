<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemandeDevisEmail extends Mailable
{
    use Queueable, SerializesModels;

    public object $customer;

    public function __construct(object $customer)
    {
        $this->customer = $customer;
    }

    public function build()
    {
        return $this->subject('Nouvelle demande de devis')
                    ->view('email.DemandeDevis')
                    ->with([
                        'customer' => $this->customer
                    ]);
    }
}
