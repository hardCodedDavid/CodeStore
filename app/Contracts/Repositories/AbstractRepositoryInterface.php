<?php

namespace App\Contracts\Repositories;
use App\Helpers\helper;

use Illuminate\Database\Eloquent\Model;

interface AbstractRepositoryInterface
{
    public function model();
    public function findByColumn(string $column, mixed $value);
    public function create(array $data);
    public function update(Model $model, array $data);
    public function destroy(Model $model);

    public function getAll(array $data);
}
