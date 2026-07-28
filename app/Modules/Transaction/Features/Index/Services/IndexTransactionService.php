<?php

namespace App\Modules\Transaction\Features\Index\Services;

use App\Modules\Transaction\Shared\Repositories\TransactionRepository;
use Illuminate\Support\Collection;

class IndexTransactionService
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
    ) {}

    /**
     * @return Collection<int, \App\Modules\Transaction\Shared\Models\Transaction>
     */
    public function execute(int $userId): Collection
    {
        return $this->transactionRepository->listForUser($userId);
    }
}
