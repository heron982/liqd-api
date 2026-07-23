<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Wallet\Domain\Models\Wallet;
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
}
