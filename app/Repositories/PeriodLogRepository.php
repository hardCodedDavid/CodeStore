<?php

namespace App\Repositories;

use App\Contracts\Repositories\PeriodLogRepositoryInterface;
use App\Models\PeriodLog;

class PeriodLogRepository extends AbstractRepository implements PeriodLogRepositoryInterface
{
    public function __construct(PeriodLog $periodLog)
    {
        parent::__construct($periodLog);
    }

    public function model()
    {
        return app(PeriodLog::class);
    }
}
