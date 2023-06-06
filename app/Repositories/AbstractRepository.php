<?php

namespace App\Repositories;

use App\Contracts\Repositories\AbstractRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository implements AbstractRepositoryInterface
{
    public function __construct(protected Model $model)
    {
    }

    public abstract function model();

    /**
     * @param string $column
     * @param mixed $value
     * @return mixed
     */
    public function findByColumn(string $column, mixed $value = ''): mixed
    {
        return $this->model()->where("$column", "$value")->first();
    }

    /**
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        $model = $this->model()->create($data);
        return $model->refresh();
    }

    /**
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        return $model->refresh();
    }

    /**
     * @param Model $model
     * @return bool
     */
    public function destroy(Model $model): bool
    {
        return $model->delete();
    }
}
