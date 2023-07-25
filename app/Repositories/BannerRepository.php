<?php

namespace App\Repositories;

use App\Models\Banner;
use App\Repositories\AbstractRepository;

class BannerRepository extends AbstractRepository
{
    public function __construct(Banner $banner)
    {
        parent::__construct($banner);
    }

    public function model()
    {
        return app(Banner::class);
    }
}
