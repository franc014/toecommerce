<?php

namespace App\Utils;

interface PayphonePayment
{
    public function confirm(string $id, string $clientTransactionId);
}
