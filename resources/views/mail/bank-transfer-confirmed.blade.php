<x-mail::message>
# Orden confirmada - Transferencia bancaria

Tu orden ha sido confirmada con el método de pago por transferencia bancaria. Hemos recibido tu comprobante de pago.

**Código de orden:** {{ $order->code }}

**Total pagado:** {{ $order->total_amount_in_dollars }}

<x-mail::table>
| Concepto | Detalle |
|----------|---------|
| Método de pago | Transferencia bancaria |
| Total | {{ $order->total_amount_in_dollars }} |
</x-mail::table>

Tu pedido será procesado una vez que confirmemos tu pago. Te notificaremos cuando tu orden sea enviada.

<x-mail::button :url="route('filament.customer.resources.orders.view', [
    'record' => $order->code,
])">
Ver mi orden
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
