<?php

namespace App\Modules\Transaction\Shared\Repositories;

use App\Modules\Shared\Library\EloquentRepository\Adapters\EloquentRepositoryAdapter;
use App\Modules\Transaction\Shared\Dtos\CreateTransactionDto;
use App\Modules\Transaction\Shared\Models\Transaction;
use Illuminate\Support\Collection;

class TransactionRepository
{
    public function __construct(
        private readonly EloquentRepositoryAdapter $adapter,
    ) {}

    public function create(CreateTransactionDto $dto): Transaction
    {
        /** @var Transaction */
        return $this->adapter->create(Transaction::class, $dto->toArray());
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function listForUser(int $userId): Collection
    {
        return $this->adapter->query(Transaction::class)
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }
}
