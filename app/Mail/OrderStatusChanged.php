<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use JFA\ToecommerceCore\Enums\OrderStatus;
use JFA\ToecommerceCore\Models\Order;

class OrderStatusChanged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Order $order,
        public OrderStatus $newStatus,
        public ?OrderStatus $oldStatus = null
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('storefront.order_status_changed_subject', ['code' => $this->order->code]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.order-status-changed',
            with: [
                'order' => $this->order,
                'newStatus' => $this->newStatus,
                'oldStatus' => $this->oldStatus,
            ],
        );
    }
}
