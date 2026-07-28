<?php

namespace App\Modules\Transaction\Shared\Repositories;

use App\Modules\Shared\Helpers\CacheHelper;
use App\Modules\Shared\Library\EloquentRepository\Adapters\EloquentRepositoryAdapter;
use App\Modules\Transaction\Shared\Dtos\CreateTransactionDto;
use App\Modules\Transaction\Shared\Models\Transaction;
use Illuminate\Support\Collection;

class TransactionRepository
{
    private const CACHE_TTL_SECONDS = 60;

    public function __construct(
        private readonly EloquentRepositoryAdapter $adapter,
    ) {}

    public static function listCacheKey(int $userId): string
    {
        return "transactions:user:{$userId}";
    }

    public function create(CreateTransactionDto $dto): Transaction
    {
        /** @var Transaction $transaction */
        $transaction = $this->adapter->create(Transaction::class, $dto->toArray());

        CacheHelper::forget(self::listCacheKey($dto->userId));

        return $transaction;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function listForUser(int $userId): Collection
    {
        /** @var list<array<string, mixed>> $rows */
        $rows = CacheHelper::remember(
            self::listCacheKey($userId),
            self::CACHE_TTL_SECONDS,
            fn () => $this->adapter->query(Transaction::class)
                ->where('user_id', $userId)
                ->latest()
                ->get()
                ->map(fn (Transaction $transaction) => $transaction->getAttributes())
                ->values()
                ->all(),
        );

        return Transaction::hydrate($rows);
    }
}
