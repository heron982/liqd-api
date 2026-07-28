<?php

namespace App\Modules\Wallet\Shared\Entities;

use App\Modules\Shared\Helpers\MoneyHelper;
use App\Modules\Wallet\Shared\Exceptions\InsufficientBalanceException;

final class Wallet
{
    public function __construct(
        private readonly ?int $id,
        private readonly int $userId,
        private string $balanceBrl,
        private string $balanceBtc,
    ) {
        $this->balanceBrl = MoneyHelper::roundBrl($balanceBrl);
        $this->balanceBtc = MoneyHelper::roundBtc($balanceBtc);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function userId(): int
    {
        return $this->userId;
    }

    public function balanceBrl(): string
    {
        return $this->balanceBrl;
    }

    public function balanceBtc(): string
    {
        return $this->balanceBtc;
    }

    public function debitBrl(string $amount): void
    {
        $amount = MoneyHelper::roundBrl($amount);

        if ((float) $this->balanceBrl < (float) $amount) {
            throw new InsufficientBalanceException(InsufficientBalanceException::ASSET_BRL);
        }

        $this->balanceBrl = MoneyHelper::roundBrl((float) $this->balanceBrl - (float) $amount);
    }

    public function creditBrl(string $amount): void
    {
        $amount = MoneyHelper::roundBrl($amount);
        $this->balanceBrl = MoneyHelper::roundBrl((float) $this->balanceBrl + (float) $amount);
    }

    public function debitBtc(string $amount): void
    {
        $amount = MoneyHelper::roundBtc($amount);

        if ((float) $this->balanceBtc < (float) $amount) {
            throw new InsufficientBalanceException(InsufficientBalanceException::ASSET_BTC);
        }

        $this->balanceBtc = MoneyHelper::roundBtc((float) $this->balanceBtc - (float) $amount);
    }

    public function creditBtc(string $amount): void
    {
        $amount = MoneyHelper::roundBtc($amount);
        $this->balanceBtc = MoneyHelper::roundBtc((float) $this->balanceBtc + (float) $amount);
    }
}
