<?php

namespace App\Modules\Trade\Features\Buy\Dtos;

use App\Modules\Transaction\Models\Transaction;
use App\Modules\Wallet\Models\Wallet;
use Spatie\LaravelData\Data;

class BuyResultDto extends Data
{
    public function __construct(
        public Wallet $wallet,
        public Transaction $transaction,
    ) {}
}
