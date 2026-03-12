<?php

namespace App\Facades;

use App\Utils\TransactionIdGenerator;
use Illuminate\Support\Facades\Facade;

class PayphoneClientTransactionIdGenerator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TransactionIdGenerator::class;
    }

    protected static function getMockableClass()
    {
        return static::getFacadeAccessor();
    }
}
