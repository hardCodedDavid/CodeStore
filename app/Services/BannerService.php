<?php

namespace App\Services;

use App\Models\Banner;
use App\Repositories\BannerRepository;

class BannerService
{
    public function __construct(protected BannerRepository $repository) 
    {
        //
    }

    public function all() : array 
    {
        $brands = $this->repository->getAll(page: get_page(), per_page: get_per_page());
                    
        return $brands;
    }

    public function addBanner(array $data) : object
    {
        // Move file to folder
        $destination = 'banners';
        $transferFile = 'BN'.time().'.'.request('file')->getClientOriginalExtension();
        request('file')->move($destination, $transferFile);

        // Create banner
        $banner = Banner::create([
            'position' => request('position'), 'url' => $destination.'/'.$transferFile
        ]);

        return $banner;
    }

    public function editBanner(Banner $banner, array $data) : object
    {

        // Upload file if exists
        $data = request()->only('position');
        if (request('file')){
            // Remove old file
            unlink($banner['url']);

            // Upload new file
            $destination = 'banners';
            $transferFile = 'BN'.time().'.'.request('file')->getClientOriginalExtension();
            request('file')->move($destination, $transferFile);
            $data['url'] = $destination.'/'.$transferFile;
        }

        // Update banner
        $banner->update($data);

        return $banner;
    }

    public function deleteBanner(Banner $banner) : bool
    {
        unlink($banner['url']);

        return $this->repository->destroy($banner);
    }
}
