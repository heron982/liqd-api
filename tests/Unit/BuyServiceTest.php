<?php

namespace Tests\Unit;

use App\Modules\Market\Features\BtcPrice\Dtos\BtcPriceDto;
use App\Modules\Market\Features\BtcPrice\Services\BtcPriceService;
use App\Modules\Trade\Features\Buy\Services\BuyService;
use App\Modules\Transaction\Shared\Dtos\CreateTransactionDto;
use App\Modules\Transaction\Shared\Enums\TransactionType;
use App\Modules\Transaction\Shared\Models\Transaction;
use App\Modules\Transaction\Shared\Repositories\TransactionRepository;
use App\Modules\Wallet\Shared\Models\Wallet;
use App\Modules\Wallet\Shared\Repositories\WalletRepository;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BuyServiceTest extends TestCase
{
    public function test_buy_debits_brl_credits_btc_and_records_transaction(): void
    {
        $wallet = new Wallet([
            'user_id' => 1,
            'balance_brl' => '10000.00',
            'balance_btc' => '0.00000000',
        ]);

        $walletRepository = $this->createMock(WalletRepository::class);
        $walletRepository->expects($this->once())
            ->method('lockByUserId')
            ->with(1)
            ->willReturn($wallet);
        $walletRepository->expects($this->once())
            ->method('update')
            ->willReturnCallback(function (Wallet $current, array $data) {
                $current->fill($data);

                return $current;
            });

        $transaction = new Transaction([
            'user_id' => 1,
            'type' => TransactionType::Buy,
            'amount_brl' => '2500.00',
            'amount_btc' => '0.01000000',
            'btc_price' => '250000.00',
        ]);

        $transactionRepository = $this->createMock(TransactionRepository::class);
        $transactionRepository->expects($this->once())
            ->method('create')
            ->with($this->callback(function (CreateTransactionDto $dto) {
                return $dto->userId === 1
                    && $dto->type === TransactionType::Buy
                    && $dto->amountBrl === '2500.00'
                    && $dto->amountBtc === '0.01000000'
                    && $dto->btcPrice === '250000.00';
            }))
            ->willReturn($transaction);

        $btcPriceService = $this->createMock(BtcPriceService::class);
        $btcPriceService->expects($this->once())
            ->method('execute')
            ->willReturn(BtcPriceDto::from(['price' => '250000.00']));

        $service = new BuyService($walletRepository, $transactionRepository, $btcPriceService);
        $result = $service->execute(1, '2500.00');

        $this->assertSame('7500.00', (string) $result->wallet->balance_brl);
        $this->assertSame('0.01000000', (string) $result->wallet->balance_btc);
        $this->assertSame(TransactionType::Buy, $result->transaction->type);
    }

    public function test_buy_fails_when_brl_balance_is_insufficient(): void
    {
        $wallet = new Wallet([
            'user_id' => 1,
            'balance_brl' => '100.00',
            'balance_btc' => '0.00000000',
        ]);

        $walletRepository = $this->createMock(WalletRepository::class);
        $walletRepository->method('lockByUserId')->willReturn($wallet);
        $walletRepository->expects($this->never())->method('update');

        $transactionRepository = $this->createMock(TransactionRepository::class);
        $transactionRepository->expects($this->never())->method('create');

        $btcPriceService = $this->createMock(BtcPriceService::class);
        $btcPriceService->method('execute')->willReturn(BtcPriceDto::from(['price' => '250000.00']));

        $service = new BuyService($walletRepository, $transactionRepository, $btcPriceService);

        $this->expectException(ValidationException::class);
        $service->execute(1, '500.00');
    }
}
