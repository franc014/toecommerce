<x-mail::message>
# @lang('storefront.order_status_updated')

@lang('storefront.hello_customer', ['name' => $order->user->name])

@lang('storefront.order_status_changed_message', ['code' => $order->code])

<x-mail::panel>
**@lang('storefront.new_status'):** {{ $newStatus->getLabel() }}

@if($oldStatus)
**@lang('storefront.previous_status'):** {{ $oldStatus->getLabel() }}
@endif
</x-mail::panel>

<x-mail::table>
| @lang('storefront.order_details') | |
|---|---|
| @lang('storefront.order_code') | {{ $order->code }} |
| @lang('storefront.total_amount') | {{ $order->total_amount_in_dollars }} |
| @lang('storefront.order_date') | {{ $order->created_at->format('d/m/Y H:i') }} |
</x-mail::table>

<x-mail::button :url="route('filament.customer.resources.orders.view', [
    'record' => $order->code,
])">
@lang('firesources.view') @lang('firesources.order')
</x-mail::button>

@if($newStatus === \App\Enums\OrderStatus::CANCELED)
@lang('storefront.order_canceled_message')
@else
@lang('storefront.thank_you_for_your_purchase')
@endif

@lang('storefront.questions_contact_us')

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
