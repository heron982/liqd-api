<?php

namespace App\Modules\Market\Features\BtcPrice\Dtos;

use Spatie\LaravelData\Data;

class BtcPriceDto extends Data
{
    public function __construct(
        public string $price,
        public string $currency = 'BRL',
    ) {}
}
