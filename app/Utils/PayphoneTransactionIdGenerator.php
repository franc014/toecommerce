<?php

namespace App\Utils;

use Illuminate\Support\Str;

class PayphoneTransactionIdGenerator implements TransactionIdGenerator
{
    public function generate(): string
    {
        return Str::ulid();
    }
}
