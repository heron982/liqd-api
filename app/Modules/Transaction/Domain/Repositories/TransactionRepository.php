<?php

namespace App\Modules\Transaction\Domain\Repositories;

use App\Modules\Transaction\Domain\Dtos\CreateTransactionDto;
use App\Modules\Transaction\Domain\Models\Transaction;
use Illuminate\Support\Collection;

class TransactionRepository
{
    public function create(CreateTransactionDto $dto): Transaction
    {
        return Transaction::query()->create($dto->toArray());
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function listForUser(int $userId): Collection
    {
        return Transaction::query()
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }
}
