<?php

namespace App\Modules\Trade\Features\Sell\Services;

use App\Modules\Market\Features\BtcPrice\Services\BtcPriceService;
use App\Modules\Shared\Helpers\MoneyHelper;
use App\Modules\Trade\Features\Sell\Dtos\SellResultDto;
use App\Modules\Transaction\Shared\Dtos\CreateTransactionDto;
use App\Modules\Transaction\Shared\Enums\TransactionType;
use App\Modules\Transaction\Shared\Repositories\TransactionRepository;
use App\Modules\Wallet\Shared\Repositories\WalletRepository;
use Illuminate\Support\Facades\DB;

class SellService
{
    public function __construct(
        private readonly WalletRepository $walletRepository,
        private readonly TransactionRepository $transactionRepository,
        private readonly BtcPriceService $btcPriceService,
    ) {}

    public function execute(int $userId, string $amountBtc): SellResultDto
    {
        $amountBtc = MoneyHelper::roundBtc($amountBtc);

        return DB::transaction(function () use ($userId, $amountBtc) {
            $wallet = $this->walletRepository->lockByUserId($userId);
            $btcPrice = $this->btcPriceService->execute()->price;
            $amountBrl = MoneyHelper::btcToBrl($amountBtc, $btcPrice);

            $wallet->debitBtc($amountBtc);
            $wallet->creditBrl($amountBrl);

            $wallet = $this->walletRepository->save($wallet);

            $transaction = $this->transactionRepository->create(CreateTransactionDto::from([
                'user_id' => $userId,
                'type' => TransactionType::Sell,
                'amount_brl' => $amountBrl,
                'amount_btc' => $amountBtc,
                'btc_price' => $btcPrice,
            ]));

            return SellResultDto::from([
                'wallet' => $wallet,
                'transaction' => $transaction,
            ]);
        });
    }
}
