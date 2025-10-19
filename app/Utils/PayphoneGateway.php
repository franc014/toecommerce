<?php

namespace App\Utils;

use Illuminate\Support\Facades\Http;

class PayphoneGateway implements PayphonePayment
{
    public function confirm(string $id, string $clientTransactionId): string
    {
        return Http::withToken(config('app.payphone.token'))
            ->post(config('app.payphone.confirm_url'), [
                'id' => $id,
                'clientTxId' => $clientTransactionId,
            ]);
    }
}
