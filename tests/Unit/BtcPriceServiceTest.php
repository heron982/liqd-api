<?php

namespace Tests\Unit;

use App\Modules\Market\Features\BtcPrice\Services\BtcPriceService;
use App\Modules\Shared\Helpers\CacheHelper;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class BtcPriceServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_generated_price_is_within_configured_range(): void
    {
        CacheHelper::forget(BtcPriceService::CACHE_KEY);

        $price = (float) app(BtcPriceService::class)->execute()->price;

        $this->assertGreaterThanOrEqual(200000, $price);
        $this->assertLessThanOrEqual(300000, $price);
    }

    public function test_price_is_cached_across_calls(): void
    {
        CacheHelper::forget(BtcPriceService::CACHE_KEY);

        $service = app(BtcPriceService::class);

        $this->assertSame($service->execute()->price, $service->execute()->price);
    }

    public function test_currency_is_brl(): void
    {
        CacheHelper::put(BtcPriceService::CACHE_KEY, '250000.00', 60);

        $dto = app(BtcPriceService::class)->execute();

        $this->assertSame('250000.00', $dto->price);
        $this->assertSame('BRL', $dto->currency);
    }
}
