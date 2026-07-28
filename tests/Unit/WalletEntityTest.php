<?php

namespace Tests\Unit;

use App\Modules\Wallet\Shared\Entities\Wallet;
use App\Modules\Wallet\Shared\Exceptions\InsufficientBalanceException;
use PHPUnit\Framework\TestCase;

class WalletEntityTest extends TestCase
{
    public function test_buy_flow_debits_brl_and_credits_btc(): void
    {
        $wallet = new Wallet(1, 1, '10000.00', '0.00000000');

        $wallet->debitBrl('2500.00');
        $wallet->creditBtc('0.01000000');

        $this->assertSame('7500.00', $wallet->balanceBrl());
        $this->assertSame('0.01000000', $wallet->balanceBtc());
    }

    public function test_debit_brl_fails_when_insufficient(): void
    {
        $wallet = new Wallet(1, 1, '100.00', '0.00000000');

        $this->expectException(InsufficientBalanceException::class);
        $wallet->debitBrl('500.00');
    }

    public function test_debit_btc_fails_when_insufficient(): void
    {
        $wallet = new Wallet(1, 1, '0.00', '0.00100000');

        $this->expectException(InsufficientBalanceException::class);
        $wallet->debitBtc('1.00000000');
    }
}
