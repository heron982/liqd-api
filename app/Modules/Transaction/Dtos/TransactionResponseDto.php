<?php

namespace App\Modules\Transaction\Dtos;

use App\Modules\Transaction\Enums\TransactionType;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class TransactionResponseDto extends Data
{
    public function __construct(
        public int $id,
        public TransactionType $type,
        public string $amountBrl,
        public string $amountBtc,
        public string $btcPrice,
        public ?CarbonInterface $createdAt,
    ) {}
}
