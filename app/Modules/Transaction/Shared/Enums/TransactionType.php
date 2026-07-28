<?php

namespace App\Modules\Transaction\Shared\Enums;

enum TransactionType: string
{
    case Buy = 'buy';
    case Sell = 'sell';
}
