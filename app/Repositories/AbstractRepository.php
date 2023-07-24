<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Repositories\AbstractRepositoryInterface;

abstract class AbstractRepository implements AbstractRepositoryInterface
{
    public function __construct(protected Model $model)
    {
    }

    public abstract function model();

    /**
     * Gets all model
     *
     * @param array $conditions
     * @param array $columns
     * @param string $order
     * @param string $dir
     * @param string|int $page
     * @param int $per_page
     * @return Collection|array
     */

     public function getAll(
        array $conditions = [],
        array $columns = ['*'],
        string $order = 'created_at',
        string $dir = 'desc',
        string|int $page = '*',
        int $per_page = 50
    ): Collection|array {
        $query = $this->model->query()->where($conditions)->orderBy($order, $dir);
        if ($page == '*') return $query->get($columns);
        return [
            // 'total' => $query->count(),
            'data' => $query->offset(($page - 1) * $per_page)->limit($per_page)->get($columns),
            'meta' => [
                        'page' => get_page(),
                        'per_page' => get_per_page(),
                        'total' => $query->count()
                    ]
        ];
    }

     /**
     * search model
     *
     * @param string $term
     * @param array $searchColumns
     * @param string[][] $relations
     * @param string|int $page
     * @param int $per_page
     * @param array $conditions
     * @param array $columns
     * @param string $order
     * @param string $dir
     * @return Collection|array
     */
    public function search(
        ?string $term = '',
        array $searchColumns = [],
        array $relations = [],
        string|int $page = '*',
        int $per_page = 50,
        array $conditions = [],
        array $columns = ['*'],
        string $order = 'created_at',
        string $dir = 'desc',
    ): Collection|array {
        if (!$term) {
            if ($page == '*') return [];
            return ['total' => 0, 'items' => []];
        }

        $query = $this->model->query()->where(function ($query) use ($term, $searchColumns, $relations, $conditions) {
            $query->where($conditions);

            if (count($relations) > 0) {
                $i = 0;
                foreach ($relations as $relation => $columnArray) {
                    if ($i == 0)
                        $query->whereHas($relation, function ($q) use ($columnArray, $term) {
                            foreach ($columnArray as $key => $column) {
                                if ($key == 0)
                                    $q->where("$column", 'LIKE', "%$term%");
                                else
                                    $q->orWhere("$column", 'LIKE', "%$term%");
                            }
                        });
                    else
                        $query->orWhereHas($relation, function ($q) use ($columnArray, $term) {
                            foreach ($columnArray as $key => $column) {
                                if ($key == 0)
                                    $q->where("$column", 'LIKE', "%$term%");
                                else
                                    $q->orWhere("$column", 'LIKE', "%$term%");
                            }
                        });
                    $i++;
                }
            }

            foreach ($searchColumns as $column)
                $query->orWhere("$column", 'LIKE', "%$term%");
        });

        if ($page == '*') return $query->orderBy($order, $dir)->get($columns);

        return [
            'total' => $query->count(),
            'items' => $query->orderBy($order, $dir)->offset(($page - 1) * $per_page)->limit($per_page)->get($columns)
        ];
    }

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
     * Find a model by ID
     *
     * @param integer $id
     * @param array $columns
     * @return object|null
     */
    public function find(int $id, array $columns = ['*']): object|null
    {
        return $this->model->query()->find($id, $columns);
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
