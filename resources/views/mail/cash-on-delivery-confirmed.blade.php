<x-mail::message>
# Orden confirmada - Pago contra entrega

Tu orden ha sido confirmada con el método de pago contra entrega. Aquí están los detalles:

**Código de orden:** {{ $order->code }}

**Total a pagar:** ${{ $order->total_amount_in_dollars }}

<x-mail::table>
| Concepto | Detalle |
|----------|---------|
| Método de pago | Pago contra entrega |
| Total | ${{ $order->total_amount_in_dollars }} |
</x-mail::table>

Paga en efectivo cuando recibas tu pedido. Asegúrate de tener el monto exacto disponible al momento de la entrega.

<x-mail::button :url="route('filament.customer.resources.orders.view', [
    'record' => $order->code,
])">
Ver mi orden
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>