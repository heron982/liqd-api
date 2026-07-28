<?php

namespace App\Modules\Trade\Shared\Dtos;

use App\Modules\Transaction\Shared\Dtos\TransactionResponseDto;
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
