<?php

namespace App\Modules\Market\Features\BtcPrice\Services;

use App\Modules\Market\Features\BtcPrice\Dtos\BtcPriceDto;
use App\Modules\Shared\Helpers\CacheHelper;
use App\Modules\Shared\Helpers\MoneyHelper;

class BtcPriceService
{
    public const CACHE_KEY = 'market:btc:price';

    private const CACHE_TTL_SECONDS = 15;

    private const MIN_PRICE = 200000.00;

    private const MAX_PRICE = 300000.00;

    public function execute(): BtcPriceDto
    {
        $price = CacheHelper::remember(
            self::CACHE_KEY,
            self::CACHE_TTL_SECONDS,
            fn () => $this->generatePrice(),
        );

        return BtcPriceDto::from([
            'price' => MoneyHelper::roundBrl($price),
        ]);
    }

    private function generatePrice(): string
    {
        $cents = random_int(
            (int) (self::MIN_PRICE * 100),
            (int) (self::MAX_PRICE * 100),
        );

        return MoneyHelper::roundBrl($cents / 100);
    }
}
