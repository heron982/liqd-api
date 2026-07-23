<?php

namespace App\Modules\Transaction\Domain\Enums;

enum TransactionType: string
{
    case Buy = 'buy';
    case Sell = 'sell';
}
