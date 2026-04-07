<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethods: string implements HasLabel
{
    case PAYPHONE = 'payphone';
    case CASH_ON_DELIVERY = 'cash_on_delivery';
    case BANK_TRANSFER = 'bank_transfer';

    public function getLabel(): string
    {
        return match ($this) {
            self::PAYPHONE => 'Payphone',
            self::CASH_ON_DELIVERY => 'Pago contra entrega',
            self::BANK_TRANSFER => 'Transferencia bancaria',
        };
    }
}
