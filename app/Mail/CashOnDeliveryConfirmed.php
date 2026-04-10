<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CashOnDeliveryConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Orden confirmada - Pago contra entrega',
            from: new Address(
                name: config('mail.from.name'),
                address: config('mail.from.address'),
            ),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.cash-on-delivery-confirmed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
