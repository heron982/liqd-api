<?php

namespace App\Modules\Wallet\Features\Show\Dtos;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class WalletBalanceDto extends Data
{
    public function __construct(
        public string $balanceBrl,
        public string $balanceBtc,
    ) {}
}
