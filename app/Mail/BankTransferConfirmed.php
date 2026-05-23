<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use JFA\ToecommerceCore\Models\Order;

class BankTransferConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Orden confirmada - Transferencia bancaria',
            from: new Address(
                name: config('mail.from.name'),
                address: config('mail.from.address'),
            ),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.bank-transfer-confirmed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
