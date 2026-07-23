<?php

namespace App\Modules\Trade\Features\Buy\Services;

use App\Modules\Market\Features\BtcPrice\Services\BtcPriceService;
use App\Modules\Shared\Helpers\MoneyHelper;
use App\Modules\Trade\Features\Buy\Dtos\BuyResultDto;
use App\Modules\Transaction\Domain\Dtos\CreateTransactionDto;
use App\Modules\Transaction\Domain\Enums\TransactionType;
use App\Modules\Transaction\Domain\Repositories\TransactionRepository;
use App\Modules\Wallet\Domain\Repositories\WalletRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

            if ((float) $wallet->balance_brl < (float) $amountBrl) {
                throw ValidationException::withMessages([
                    'amount_brl' => [trans('messages.trade.insufficient_brl_balance')],
                ]);
            }

            $amountBtc = MoneyHelper::brlToBtc($amountBrl, $btcPrice);

            $wallet = $this->walletRepository->update($wallet, [
                'balance_brl' => MoneyHelper::roundBrl((float) $wallet->balance_brl - (float) $amountBrl),
                'balance_btc' => MoneyHelper::roundBtc((float) $wallet->balance_btc + (float) $amountBtc),
            ]);

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
