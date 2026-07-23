<?php

namespace App\Modules\Trade\Features\Buy\Dtos;

use App\Modules\Transaction\Domain\Models\Transaction;
use App\Modules\Wallet\Domain\Models\Wallet;
use Spatie\LaravelData\Data;

class BuyResultDto extends Data
{
    public function __construct(
        public Wallet $wallet,
        public Transaction $transaction,
    ) {}
}
