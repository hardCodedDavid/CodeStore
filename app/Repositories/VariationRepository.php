<?php

namespace App\Repositories;

use App\Models\Variation;
use App\Repositories\AbstractRepository;

class VariationRepository extends AbstractRepository
{
    public function __construct(Variation $variation)
    {
        parent::__construct($variation);
    }

    public function model()
    {
        return app(Variation::class);
    }
}
