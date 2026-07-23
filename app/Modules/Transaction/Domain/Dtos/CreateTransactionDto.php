<?php

namespace App\Modules\Transaction\Domain\Dtos;

use App\Modules\Transaction\Domain\Enums\TransactionType;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class CreateTransactionDto extends Data
{
    public function __construct(
        public int $userId,
        public TransactionType $type,
        public string $amountBrl,
        public string $amountBtc,
        public string $btcPrice,
    ) {}
}
