<x-mail::message>
# Nuevo mensaje de contacto.

Nombres: {{ $contact->first_name }}
Apellidos: {{ $contact->last_name }}
Email: {{ $contact->email }}
Mensaje:
{{ $contact->message }}
Saludos,<br>
{{ config('app.name') }}

</x-mail::message>
