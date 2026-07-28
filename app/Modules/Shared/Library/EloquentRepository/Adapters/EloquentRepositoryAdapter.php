<?php

namespace App\Modules\Shared\Library\EloquentRepository\Adapters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EloquentRepositoryAdapter
{
    /**
     * @param  class-string<Model>  $modelClass
     * @return Builder<Model>
     */
    public function query(string $modelClass): Builder
    {
        return $modelClass::query();
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $data
     */
    public function create(string $modelClass, array $data): Model
    {
        return $this->query($modelClass)->create($data);
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    public function find(string $modelClass, int $id): ?Model
    {
        return $this->query($modelClass)->find($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Model $model, array $data): Model
    {
        $model->fill($data);
        $model->save();

        return $model->refresh();
    }

    public function delete(Model $model): bool
    {
        return (bool) $model->delete();
    }
}
