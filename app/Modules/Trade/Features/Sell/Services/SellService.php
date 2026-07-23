<?php

namespace App\Modules\Trade\Features\Sell\Services;

use App\Modules\Market\Features\BtcPrice\Services\BtcPriceService;
use App\Modules\Shared\Helpers\MoneyHelper;
use App\Modules\Trade\Features\Sell\Dtos\SellResultDto;
use App\Modules\Transaction\Domain\Dtos\CreateTransactionDto;
use App\Modules\Transaction\Domain\Enums\TransactionType;
use App\Modules\Transaction\Domain\Repositories\TransactionRepository;
use App\Modules\Wallet\Domain\Repositories\WalletRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

            if ((float) $wallet->balance_btc < (float) $amountBtc) {
                throw ValidationException::withMessages([
                    'amount_btc' => [trans('messages.trade.insufficient_btc_balance')],
                ]);
            }

            $amountBrl = MoneyHelper::btcToBrl($amountBtc, $btcPrice);

            $wallet = $this->walletRepository->update($wallet, [
                'balance_btc' => MoneyHelper::roundBtc((float) $wallet->balance_btc - (float) $amountBtc),
                'balance_brl' => MoneyHelper::roundBrl((float) $wallet->balance_brl + (float) $amountBrl),
            ]);

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
