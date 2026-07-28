<?php

namespace App\Modules\Wallet\Shared\Repositories;

use App\Modules\Shared\Library\EloquentRepository\Adapters\EloquentRepositoryAdapter;
use App\Modules\Wallet\Shared\Models\Wallet;

class WalletRepository
{
    public function __construct(
        private readonly EloquentRepositoryAdapter $adapter,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Wallet
    {
        /** @var Wallet */
        return $this->adapter->create(Wallet::class, $data);
    }

    public function find(int $id): ?Wallet
    {
        /** @var Wallet|null */
        return $this->adapter->find(Wallet::class, $id);
    }

    public function findByUserId(int $userId): ?Wallet
    {
        /** @var Wallet|null */
        return $this->adapter->query(Wallet::class)
            ->where('user_id', $userId)
            ->first();
    }

    public function lockByUserId(int $userId): Wallet
    {
        /** @var Wallet */
        return $this->adapter->query(Wallet::class)
            ->where('user_id', $userId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Wallet $wallet, array $data): Wallet
    {
        /** @var Wallet */
        return $this->adapter->update($wallet, $data);
    }

    public function delete(Wallet $wallet): bool
    {
        return $this->adapter->delete($wallet);
    }
}
