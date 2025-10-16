<x-mail::message>
    # Orden Confirmada

    Felicidades, tu orden ha sido confirmada. Aquí están los detalles de tu orden:

    <x-mail::button :url="''">
        Ver mi orden
    </x-mail::button>

    Gracias,<br>
    {{ config('app.name') }}
</x-mail::message>
