<?php

namespace App\Modules\Wallet\Shared\Repositories;

use App\Modules\Shared\Helpers\CacheHelper;
use App\Modules\Shared\Helpers\MoneyHelper;
use App\Modules\Shared\Library\EloquentRepository\Adapters\EloquentRepositoryAdapter;
use App\Modules\Wallet\Shared\Entities\Wallet;
use App\Modules\Wallet\Shared\Models\Wallet as WalletModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WalletRepository
{
    private const CACHE_TTL_SECONDS = 60;

    public function __construct(
        private readonly EloquentRepositoryAdapter $adapter,
    ) {}

    public static function showCacheKey(int $userId): string
    {
        return "wallet:user:{$userId}";
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Wallet
    {
        /** @var WalletModel $model */
        $model = $this->adapter->create(WalletModel::class, $data);
        $wallet = $this->toEntity($model);

        CacheHelper::forget(self::showCacheKey($wallet->userId()));

        return $wallet;
    }

    public function find(int $id): ?Wallet
    {
        /** @var WalletModel|null $model */
        $model = $this->adapter->find(WalletModel::class, $id);

        return $model ? $this->toEntity($model) : null;
    }

    public function findByUserId(int $userId): ?Wallet
    {
        /** @var array{id: int, user_id: int, balance_brl: string, balance_btc: string}|null $data */
        $data = CacheHelper::remember(
            self::showCacheKey($userId),
            self::CACHE_TTL_SECONDS,
            function () use ($userId) {
                /** @var WalletModel|null $model */
                $model = $this->adapter->query(WalletModel::class)
                    ->where('user_id', $userId)
                    ->first();

                if (! $model) {
                    return null;
                }

                return [
                    'id' => (int) $model->id,
                    'user_id' => (int) $model->user_id,
                    'balance_brl' => MoneyHelper::roundBrl((string) $model->balance_brl),
                    'balance_btc' => MoneyHelper::roundBtc((string) $model->balance_btc),
                ];
            },
        );

        if ($data === null) {
            return null;
        }

        return new Wallet(
            id: $data['id'],
            userId: $data['user_id'],
            balanceBrl: $data['balance_brl'],
            balanceBtc: $data['balance_btc'],
        );
    }

    public function lockByUserId(int $userId): Wallet
    {
        /** @var WalletModel $model */
        $model = $this->adapter->query(WalletModel::class)
            ->where('user_id', $userId)
            ->lockForUpdate()
            ->firstOrFail();

        return $this->toEntity($model);
    }

    public function save(Wallet $wallet): Wallet
    {
        if ($wallet->id() === null) {
            throw (new ModelNotFoundException)->setModel(WalletModel::class);
        }

        /** @var WalletModel|null $model */
        $model = $this->adapter->find(WalletModel::class, $wallet->id());

        if (! $model) {
            throw (new ModelNotFoundException)->setModel(WalletModel::class, [$wallet->id()]);
        }

        /** @var WalletModel $model */
        $model = $this->adapter->update($model, [
            'balance_brl' => $wallet->balanceBrl(),
            'balance_btc' => $wallet->balanceBtc(),
        ]);

        $entity = $this->toEntity($model);

        CacheHelper::forget(self::showCacheKey($entity->userId()));

        return $entity;
    }

    public function delete(Wallet $wallet): bool
    {
        if ($wallet->id() === null) {
            return false;
        }

        /** @var WalletModel|null $model */
        $model = $this->adapter->find(WalletModel::class, $wallet->id());

        if (! $model) {
            return false;
        }

        $deleted = $this->adapter->delete($model);

        if ($deleted) {
            CacheHelper::forget(self::showCacheKey($wallet->userId()));
        }

        return $deleted;
    }

    private function toEntity(WalletModel $model): Wallet
    {
        return new Wallet(
            id: $model->id,
            userId: (int) $model->user_id,
            balanceBrl: MoneyHelper::roundBrl((string) $model->balance_brl),
            balanceBtc: MoneyHelper::roundBtc((string) $model->balance_btc),
        );
    }
}
