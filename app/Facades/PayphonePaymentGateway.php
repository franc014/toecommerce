<?php

namespace App\Facades;

use App\Utils\PayphonePayment;
use Illuminate\Support\Facades\Facade;

class PayphonePaymentGateway extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PayphonePayment::class;
    }

    protected static function getMockableClass()
    {
        return static::getFacadeAccessor();
    }
}
