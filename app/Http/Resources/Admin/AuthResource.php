<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data['created_at'] = date('Y-m-d H:i:s', strtotime($data['created_at']));
        unset($data['updated_at'], $data['deleted_at']);

        return $data;
    }
}
