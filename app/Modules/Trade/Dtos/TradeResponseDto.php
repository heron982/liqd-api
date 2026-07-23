<?php

namespace App\Modules\Trade\Dtos;

use App\Modules\Transaction\Dtos\TransactionResponseDto;
use Spatie\LaravelData\Data;

class TradeResponseDto extends Data
{
    /**
     * @param  array{balance_brl: string, balance_btc: string}  $wallet
     */
    public function __construct(
        public array $wallet,
        public TransactionResponseDto $transaction,
    ) {}
}
