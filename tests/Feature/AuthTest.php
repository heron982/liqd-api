<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Market\Features\BtcPrice\Services\BtcPriceService;
use App\Modules\Shared\Helpers\CacheHelper;
use App\Modules\Wallet\Shared\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_initial_wallet(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Felipe',
            'email' => 'felipe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('user.email', 'felipe@example.com')
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $response->json('user.id'),
            'balance_brl' => '10000.00',
            'balance_btc' => '0.00000000',
        ]);
    }

    public function test_user_can_login_and_access_me(): void
    {
        $user = User::factory()->create([
            'email' => 'felipe@example.com',
            'password' => 'password123',
        ]);

        Wallet::query()->create([
            'user_id' => $user->id,
            'balance_brl' => '10000.00',
            'balance_btc' => '0.00000000',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'felipe@example.com',
            'password' => 'password123',
        ]);

        $login->assertOk()->assertJsonStructure(['token']);

        $this->withToken($login->json('token'))
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('email', 'felipe@example.com');
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
    }

    public function test_wallet_endpoint_reflects_balance_after_trade_with_cache(): void
    {
        $user = User::factory()->create();

        Wallet::query()->create([
            'user_id' => $user->id,
            'balance_brl' => '10000.00',
            'balance_btc' => '0.00000000',
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/wallet')
            ->assertOk()
            ->assertJsonPath('balance_brl', '10000.00');

        CacheHelper::put(BtcPriceService::CACHE_KEY, '250000.00', 60);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/trade/buy', ['amount_brl' => 2500])
            ->assertOk();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/wallet')
            ->assertOk()
            ->assertJsonPath('balance_brl', '7500.00')
            ->assertJsonPath('balance_btc', '0.01000000');
    }
}
