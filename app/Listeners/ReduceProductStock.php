<?php

namespace App\Listeners;

use App\Events\OrderConfirmed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Models\AppSettings;

class ReduceProductStock
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderConfirmed $event): void
    {

        if (AppSettings::isStockControlStrict()) {
            DB::transaction(function () use ($event) {

                foreach ($event->order->orderItems as $item) {

                    $purchasableId = $item->purchasable_id;
                    $purchasable = $item->purchasable_type::lockForUpdate()->find($purchasableId);

                    if (!$purchasable) {
                        return;
                    }
                    $purchasable->update([
                        'stock' => $purchasable->stock - $item->quantity
                    ]);
                }
            });
        }
    }
}
