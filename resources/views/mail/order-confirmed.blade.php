<x-mail::message>
# Orden Confirmada

Felicidades, tu orden ha sido confirmada. Aquí están los detalles de tu orden:

<x-mail::button url="{{ route('filament.customer.resources.orders.view', [
        'record' => $order->fresh()->code,
    ]) }}">
Ver mi orden
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>