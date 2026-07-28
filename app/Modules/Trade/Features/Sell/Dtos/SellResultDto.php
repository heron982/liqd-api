<?php

namespace App\Modules\Trade\Features\Sell\Dtos;

use App\Modules\Transaction\Shared\Models\Transaction;
use App\Modules\Wallet\Shared\Models\Wallet;
use Spatie\LaravelData\Data;

class SellResultDto extends Data
{
    public function __construct(
        public Wallet $wallet,
        public Transaction $transaction,
    ) {}
}
