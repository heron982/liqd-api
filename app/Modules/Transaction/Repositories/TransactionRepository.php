<?php

namespace App\Modules\Transaction\Repositories;

use App\Modules\Transaction\Dtos\CreateTransactionDto;
use App\Modules\Transaction\Models\Transaction;
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
