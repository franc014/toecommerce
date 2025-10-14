<?php

namespace App\Utils;

interface TransactionIdGenerator
{
    public function generate(): string;
}
