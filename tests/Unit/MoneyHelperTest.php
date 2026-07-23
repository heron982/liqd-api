<?php

namespace Tests\Unit;

use App\Modules\Shared\Helpers\MoneyHelper;
use PHPUnit\Framework\TestCase;

class MoneyHelperTest extends TestCase
{
    public function test_round_brl_keeps_two_decimals(): void
    {
        $this->assertSame('10.50', MoneyHelper::roundBrl(10.5));
        $this->assertSame('10.56', MoneyHelper::roundBrl(10.555));
    }

    public function test_round_btc_keeps_eight_decimals(): void
    {
        $this->assertSame('0.00040000', MoneyHelper::roundBtc(0.0004));
        $this->assertSame('1.12345679', MoneyHelper::roundBtc(1.123456789));
    }

    public function test_brl_to_btc_conversion(): void
    {
        $this->assertSame(
            '0.01000000',
            MoneyHelper::brlToBtc('2500.00', '250000.00'),
        );
    }

    public function test_btc_to_brl_conversion(): void
    {
        $this->assertSame(
            '2500.00',
            MoneyHelper::btcToBrl('0.01000000', '250000.00'),
        );
    }

    public function test_round_trip_same_price_preserves_brl(): void
    {
        $brlIn = '100.00';
        $price = '250000.00';

        $btc = MoneyHelper::brlToBtc($brlIn, $price);
        $brlOut = MoneyHelper::btcToBrl($btc, $price);

        $this->assertSame($brlIn, $brlOut);
    }

    public function test_price_drop_between_buy_and_sell_reduces_brl(): void
    {
        $btc = MoneyHelper::brlToBtc('100.00', '300000.00');
        $brlOut = MoneyHelper::btcToBrl($btc, '200000.00');

        $this->assertSame('0.00033333', $btc);
        $this->assertSame('66.67', $brlOut);
        $this->assertLessThan(100, (float) $brlOut);
    }
}
