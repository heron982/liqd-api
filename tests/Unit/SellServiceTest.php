<?php

namespace Tests\Unit;

use App\Modules\Market\Features\BtcPrice\Dtos\BtcPriceDto;
use App\Modules\Market\Features\BtcPrice\Services\BtcPriceService;
use App\Modules\Trade\Features\Sell\Services\SellService;
use App\Modules\Transaction\Dtos\CreateTransactionDto;
use App\Modules\Transaction\Enums\TransactionType;
use App\Modules\Transaction\Models\Transaction;
use App\Modules\Transaction\Repositories\TransactionRepository;
use App\Modules\Wallet\Models\Wallet;
use App\Modules\Wallet\Repositories\WalletRepository;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SellServiceTest extends TestCase
{
    public function test_sell_debits_btc_credits_brl_and_records_transaction(): void
    {
        $wallet = new Wallet([
            'user_id' => 1,
            'balance_brl' => '0.00',
            'balance_btc' => '0.01000000',
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
            'type' => TransactionType::Sell,
            'amount_brl' => '2500.00',
            'amount_btc' => '0.01000000',
            'btc_price' => '250000.00',
        ]);

        $transactionRepository = $this->createMock(TransactionRepository::class);
        $transactionRepository->expects($this->once())
            ->method('create')
            ->with($this->callback(function (CreateTransactionDto $dto) {
                return $dto->userId === 1
                    && $dto->type === TransactionType::Sell
                    && $dto->amountBrl === '2500.00'
                    && $dto->amountBtc === '0.01000000'
                    && $dto->btcPrice === '250000.00';
            }))
            ->willReturn($transaction);

        $btcPriceService = $this->createMock(BtcPriceService::class);
        $btcPriceService->expects($this->once())
            ->method('execute')
            ->willReturn(BtcPriceDto::from(['price' => '250000.00']));

        $service = new SellService($walletRepository, $transactionRepository, $btcPriceService);
        $result = $service->execute(1, '0.01000000');

        $this->assertSame('2500.00', (string) $result->wallet->balance_brl);
        $this->assertSame('0.00000000', (string) $result->wallet->balance_btc);
        $this->assertSame(TransactionType::Sell, $result->transaction->type);
    }

    public function test_sell_fails_when_btc_balance_is_insufficient(): void
    {
        $wallet = new Wallet([
            'user_id' => 1,
            'balance_brl' => '0.00',
            'balance_btc' => '0.00100000',
        ]);

        $walletRepository = $this->createMock(WalletRepository::class);
        $walletRepository->method('lockByUserId')->willReturn($wallet);
        $walletRepository->expects($this->never())->method('update');

        $transactionRepository = $this->createMock(TransactionRepository::class);
        $transactionRepository->expects($this->never())->method('create');

        $btcPriceService = $this->createMock(BtcPriceService::class);
        $btcPriceService->method('execute')->willReturn(BtcPriceDto::from(['price' => '250000.00']));

        $service = new SellService($walletRepository, $transactionRepository, $btcPriceService);

        $this->expectException(ValidationException::class);
        $service->execute(1, '1.00000000');
    }
}
