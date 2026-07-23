<?php

namespace App\Modules\Shared\Helpers;

final class MoneyHelper
{
    public static function roundBrl(float|string $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }

    public static function roundBtc(float|string $value): string
    {
        return number_format((float) $value, 8, '.', '');
    }

    public static function brlToBtc(string $amountBrl, string $btcPrice): string
    {
        return self::roundBtc((float) $amountBrl / (float) $btcPrice);
    }

    public static function btcToBrl(string $amountBtc, string $btcPrice): string
    {
        return self::roundBrl((float) $amountBtc * (float) $btcPrice);
    }
}
