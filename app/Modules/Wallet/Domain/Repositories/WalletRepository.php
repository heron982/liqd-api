<?php

namespace App\Modules\Wallet\Domain\Repositories;

use App\Modules\Wallet\Domain\Models\Wallet;

class WalletRepository
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Wallet
    {
        return Wallet::query()->create($data);
    }

    public function find(int $id): ?Wallet
    {
        return Wallet::query()->find($id);
    }

    public function findByUserId(int $userId): ?Wallet
    {
        return Wallet::query()->where('user_id', $userId)->first();
    }

    public function lockByUserId(int $userId): Wallet
    {
        return Wallet::query()
            ->where('user_id', $userId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Wallet $wallet, array $data): Wallet
    {
        $wallet->fill($data);
        $wallet->save();

        return $wallet->refresh();
    }

    public function delete(Wallet $wallet): bool
    {
        return (bool) $wallet->delete();
    }
}
