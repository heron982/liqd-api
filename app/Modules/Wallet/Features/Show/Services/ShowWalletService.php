<?php

namespace App\Modules\Wallet\Features\Show\Services;

use App\Modules\Wallet\Models\Wallet;
use App\Modules\Wallet\Repositories\WalletRepository;
use App\Modules\Wallet\Features\Show\Dtos\WalletBalanceDto;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShowWalletService
{
    public function __construct(
        private readonly WalletRepository $walletRepository,
    ) {}

    public function execute(int $userId): WalletBalanceDto
    {
        $wallet = $this->walletRepository->findByUserId($userId);

        if (! $wallet) {
            throw (new ModelNotFoundException)->setModel(Wallet::class);
        }

        return WalletBalanceDto::from([
            'balance_brl' => (string) $wallet->balance_brl,
            'balance_btc' => (string) $wallet->balance_btc,
        ]);
    }
}
