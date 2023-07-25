<?php

namespace App\Services;

use App\Models\Variation;
use App\Repositories\VariationRepository;

class VariationService
{
    public function __construct(protected VariationRepository $repository) 
    {
        //
    }

    public function all() : object 
    {
        $variation = Variation::with(['items', 'items.products'])->get();
                
        return $variation;
    }

    public function addVariation(array $data) : object
    {
        $variation = $this->repository->create($data);

        if (request('types')) {
            foreach (explode(',', request('types')) as $item) {
                $variation->items()->create(['name' => $item]);
            }
        }

        return $variation;
    }

    public function editVariation(Variation $variation, array $data) : object
    {
        $variations = $this->repository->update($variation, $data);

        // Check for removed types and delete
        // $hasError = false;
        // $hasErrorCount = 0;
        // foreach ($variation->items()->with(['products'])->get() as $selectedSubvariation) {
        //     $found = false;
        //     if (request('types')) {
        //         foreach (request('types') as $allowableSubvariation) {
        //             if ($allowableSubvariation['id'] == $selectedSubvariation['id']) $found = true;
        //         }
        //     }
        //     if (!$found) {
        //         if (count($selectedSubvariation->products) == 0){
        //              $selectedSubvariation->delete();
        //         }else{
        //             $hasError = true;
        //             $hasErrorCount++;
        //         }
        //     }
        // }

        // Update or create type
        if (request('types')) {
            $updatedSubType = [];
            foreach (request('types') as $subType) {
                if (isset($subType['id'])) {
                    // Update existing subcategory
                    $subcategory = $variation->items()->find($subType['id']);
                    if ($subcategory) {
                        $subcategory->update(['name' => $subType['name']]);
                        $updatedSubType[] = $subcategory;
                    }
                } else {
                    // Create new subcategory
                    $newSubcategory = $variation->items()->create(['name' => $subType]);
                    $updatedSubType[] = $newSubcategory;
                }
            }
            // Remove subType that were not updated or added
            $variation->items()->whereNotIn('id', array_map(function ($subCategory) {
                return $subCategory['id'];
            }, $updatedSubType))->delete();
        }

        return $variations;
    }

    public function deleteVariation(Variation $variation) : bool
    {
        // Check if category has associated products
        if ($variation->items()->has('products')->count() > 0) {
            return false;
        }

        $variation->items()->delete();
        
        return $this->repository->destroy($variation);
    }
}
