<?php

namespace App\Modules\Trade\Features\Buy\Services;

use App\Modules\Market\Features\BtcPrice\Services\BtcPriceService;
use App\Modules\Shared\Helpers\MoneyHelper;
use App\Modules\Trade\Features\Buy\Dtos\BuyResultDto;
use App\Modules\Transaction\Shared\Dtos\CreateTransactionDto;
use App\Modules\Transaction\Shared\Enums\TransactionType;
use App\Modules\Transaction\Shared\Repositories\TransactionRepository;
use App\Modules\Wallet\Shared\Repositories\WalletRepository;
use Illuminate\Support\Facades\DB;

class BuyService
{
    public function __construct(
        private readonly WalletRepository $walletRepository,
        private readonly TransactionRepository $transactionRepository,
        private readonly BtcPriceService $btcPriceService,
    ) {}

    public function execute(int $userId, string $amountBrl): BuyResultDto
    {
        $amountBrl = MoneyHelper::roundBrl($amountBrl);

        return DB::transaction(function () use ($userId, $amountBrl) {
            $wallet = $this->walletRepository->lockByUserId($userId);
            $btcPrice = $this->btcPriceService->execute()->price;
            $amountBtc = MoneyHelper::brlToBtc($amountBrl, $btcPrice);

            $wallet->debitBrl($amountBrl);
            $wallet->creditBtc($amountBtc);

            $wallet = $this->walletRepository->save($wallet);

            $transaction = $this->transactionRepository->create(CreateTransactionDto::from([
                'user_id' => $userId,
                'type' => TransactionType::Buy,
                'amount_brl' => $amountBrl,
                'amount_btc' => $amountBtc,
                'btc_price' => $btcPrice,
            ]));

            return BuyResultDto::from([
                'wallet' => $wallet,
                'transaction' => $transaction,
            ]);
        });
    }
}
