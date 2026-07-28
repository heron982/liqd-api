<?php

namespace Tests\Unit;

use App\Models\User;
use App\Modules\Shared\Helpers\CacheHelper;
use App\Modules\Transaction\Shared\Dtos\CreateTransactionDto;
use App\Modules\Transaction\Shared\Enums\TransactionType;
use App\Modules\Transaction\Shared\Repositories\TransactionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TransactionRepositoryCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_list_for_user_is_cached_across_calls(): void
    {
        $user = User::factory()->create();
        $repository = app(TransactionRepository::class);

        $repository->create(CreateTransactionDto::from([
            'user_id' => $user->id,
            'type' => TransactionType::Buy,
            'amount_brl' => '1000.00',
            'amount_btc' => '0.00500000',
            'btc_price' => '200000.00',
        ]));

        $first = $repository->listForUser($user->id);
        $second = $repository->listForUser($user->id);

        $this->assertNotNull(CacheHelper::get(TransactionRepository::listCacheKey($user->id)));
        $this->assertCount(1, $first);
        $this->assertCount(1, $second);
        $this->assertSame($first->first()->id, $second->first()->id);
    }

    public function test_create_invalidates_list_cache(): void
    {
        $user = User::factory()->create();
        $repository = app(TransactionRepository::class);

        $repository->create(CreateTransactionDto::from([
            'user_id' => $user->id,
            'type' => TransactionType::Buy,
            'amount_brl' => '1000.00',
            'amount_btc' => '0.00500000',
            'btc_price' => '200000.00',
        ]));

        $repository->listForUser($user->id);
        $this->assertNotNull(CacheHelper::get(TransactionRepository::listCacheKey($user->id)));

        $repository->create(CreateTransactionDto::from([
            'user_id' => $user->id,
            'type' => TransactionType::Sell,
            'amount_brl' => '500.00',
            'amount_btc' => '0.00250000',
            'btc_price' => '200000.00',
        ]));

        $this->assertNull(CacheHelper::get(TransactionRepository::listCacheKey($user->id)));
        $this->assertCount(2, $repository->listForUser($user->id));
    }
}
