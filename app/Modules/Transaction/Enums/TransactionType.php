<?php

namespace App\Modules\Transaction\Enums;

enum TransactionType: string
{
    case Buy = 'buy';
    case Sell = 'sell';
}
