<?php

namespace Tests\Unit;

use App\Models\User;
use App\Modules\Auth\Features\Me\Services\MeService;
use App\Modules\Auth\Shared\Repositories\UserRepository;
use App\Modules\Shared\Helpers\CacheHelper;
use App\Modules\Wallet\Shared\Repositories\WalletRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WalletAndMeCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_wallet_find_by_user_id_is_cached_across_calls(): void
    {
        $user = User::factory()->create();
        $repository = app(WalletRepository::class);

        $repository->create([
            'user_id' => $user->id,
            'balance_brl' => '10000.00',
            'balance_btc' => '0.00000000',
        ]);

        $first = $repository->findByUserId($user->id);
        $second = $repository->findByUserId($user->id);

        $this->assertNotNull(CacheHelper::get(WalletRepository::showCacheKey($user->id)));
        $this->assertSame($first?->balanceBrl(), $second?->balanceBrl());
        $this->assertSame('10000.00', $second?->balanceBrl());
    }

    public function test_wallet_save_invalidates_show_cache(): void
    {
        $user = User::factory()->create();
        $repository = app(WalletRepository::class);

        $wallet = $repository->create([
            'user_id' => $user->id,
            'balance_brl' => '10000.00',
            'balance_btc' => '0.00000000',
        ]);

        $repository->findByUserId($user->id);
        $this->assertNotNull(CacheHelper::get(WalletRepository::showCacheKey($user->id)));

        $wallet->debitBrl('2500.00');
        $repository->save($wallet);

        $this->assertNull(CacheHelper::get(WalletRepository::showCacheKey($user->id)));
        $this->assertSame('7500.00', $repository->findByUserId($user->id)?->balanceBrl());
    }

    public function test_me_profile_is_cached_across_calls(): void
    {
        $user = User::factory()->create([
            'name' => 'Felipe',
            'email' => 'felipe@example.com',
        ]);

        $service = app(MeService::class);

        $first = $service->execute($user->id);
        $second = $service->execute($user->id);

        $this->assertNotNull(CacheHelper::get(UserRepository::profileCacheKey($user->id)));
        $this->assertSame($first->email, $second->email);
        $this->assertSame('felipe@example.com', $second->email);
    }

    public function test_user_update_invalidates_me_cache(): void
    {
        $user = User::factory()->create([
            'name' => 'Felipe',
            'email' => 'felipe@example.com',
        ]);

        $service = app(MeService::class);
        $repository = app(UserRepository::class);

        $service->execute($user->id);
        $this->assertNotNull(CacheHelper::get(UserRepository::profileCacheKey($user->id)));

        $repository->update($user, ['name' => 'Felipe Atualizado']);

        $this->assertNull(CacheHelper::get(UserRepository::profileCacheKey($user->id)));
        $this->assertSame('Felipe Atualizado', $service->execute($user->id)->name);
    }
}
