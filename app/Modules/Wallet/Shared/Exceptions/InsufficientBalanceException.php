<?php

namespace App\Modules\Wallet\Shared\Exceptions;

use DomainException;

final class InsufficientBalanceException extends DomainException
{
    public const ASSET_BRL = 'brl';

    public const ASSET_BTC = 'btc';

    public function __construct(
        public readonly string $asset,
    ) {
        parent::__construct(match ($asset) {
            self::ASSET_BRL => 'Insufficient BRL balance.',
            self::ASSET_BTC => 'Insufficient BTC balance.',
            default => 'Insufficient balance.',
        });
    }

    public function field(): string
    {
        return match ($this->asset) {
            self::ASSET_BRL => 'amount_brl',
            self::ASSET_BTC => 'amount_btc',
            default => 'amount',
        };
    }

    public function messageKey(): string
    {
        return match ($this->asset) {
            self::ASSET_BRL => 'messages.trade.insufficient_brl_balance',
            self::ASSET_BTC => 'messages.trade.insufficient_btc_balance',
            default => 'messages.trade.insufficient_brl_balance',
        };
    }
}
