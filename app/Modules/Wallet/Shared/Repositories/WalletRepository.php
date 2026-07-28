<?php

namespace App\Modules\Wallet\Shared\Repositories;

use App\Modules\Shared\Helpers\MoneyHelper;
use App\Modules\Shared\Library\EloquentRepository\Adapters\EloquentRepositoryAdapter;
use App\Modules\Wallet\Shared\Entities\Wallet;
use App\Modules\Wallet\Shared\Models\Wallet as WalletModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        /** @var WalletModel $model */
        $model = $this->adapter->create(WalletModel::class, $data);

        return $this->toEntity($model);
    }

    public function find(int $id): ?Wallet
    {
        /** @var WalletModel|null $model */
        $model = $this->adapter->find(WalletModel::class, $id);

        return $model ? $this->toEntity($model) : null;
    }

    public function findByUserId(int $userId): ?Wallet
    {
        /** @var WalletModel|null $model */
        $model = $this->adapter->query(WalletModel::class)
            ->where('user_id', $userId)
            ->first();

        return $model ? $this->toEntity($model) : null;
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

        return $this->toEntity($model);
    }

    public function delete(Wallet $wallet): bool
    {
        if ($wallet->id() === null) {
            return false;
        }

        /** @var WalletModel|null $model */
        $model = $this->adapter->find(WalletModel::class, $wallet->id());

        return $model ? $this->adapter->delete($model) : false;
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
