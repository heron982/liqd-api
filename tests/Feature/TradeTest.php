<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Market\Features\BtcPrice\Services\BtcPriceService;
use App\Modules\Shared\Helpers\CacheHelper;
use App\Modules\Wallet\Shared\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradeTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(float $brl = 10000, float $btc = 0): User
    {
        $user = User::factory()->create();

        Wallet::query()->create([
            'user_id' => $user->id,
            'balance_brl' => number_format($brl, 2, '.', ''),
            'balance_btc' => number_format($btc, 8, '.', ''),
        ]);

        return $user;
    }

    private function freezePrice(string $price = '250000.00'): void
    {
        CacheHelper::put(BtcPriceService::CACHE_KEY, $price, 60);
    }

    public function test_user_can_buy_btc_and_update_wallet(): void
    {
        $user = $this->actingUser();
        $this->freezePrice('250000.00');

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/trade/buy', ['amount_brl' => 2500]);

        $response->assertOk()
            ->assertJsonPath('wallet.balance_brl', '7500.00')
            ->assertJsonPath('wallet.balance_btc', '0.01000000')
            ->assertJsonPath('transaction.type', 'buy');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'buy',
            'amount_brl' => '2500.00',
            'btc_price' => '250000.00',
        ]);
    }

    public function test_buy_fails_with_insufficient_balance(): void
    {
        $user = $this->actingUser(brl: 100);
        $this->freezePrice();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/trade/buy', ['amount_brl' => 500])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['amount_brl']);
    }

    public function test_user_can_sell_btc_and_update_wallet(): void
    {
        $user = $this->actingUser(brl: 0, btc: 0.01);
        $this->freezePrice('250000.00');

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/trade/sell', ['amount_btc' => 0.01]);

        $response->assertOk()
            ->assertJsonPath('wallet.balance_brl', '2500.00')
            ->assertJsonPath('wallet.balance_btc', '0.00000000')
            ->assertJsonPath('transaction.type', 'sell');
    }

    public function test_sell_fails_with_insufficient_btc(): void
    {
        $user = $this->actingUser(btc: 0.001);
        $this->freezePrice();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/trade/sell', ['amount_btc' => 1])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['amount_btc']);
    }

    public function test_market_price_is_within_range(): void
    {
        CacheHelper::forget(BtcPriceService::CACHE_KEY);

        $user = $this->actingUser();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/market/btc')
            ->assertOk();

        $price = (float) $response->json('price');

        $this->assertGreaterThanOrEqual(200000, $price);
        $this->assertLessThanOrEqual(300000, $price);
    }

    public function test_transactions_history_lists_user_trades(): void
    {
        $user = $this->actingUser();
        $this->freezePrice('200000.00');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/trade/buy', ['amount_brl' => 2000])
            ->assertOk();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/transactions')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', 'buy');
    }

    public function test_btc_price_service_uses_cache(): void
    {
        CacheHelper::forget(BtcPriceService::CACHE_KEY);

        $service = app(BtcPriceService::class);
        $first = $service->execute()->price;
        $second = $service->execute()->price;

        $this->assertSame($first, $second);
    }
}
