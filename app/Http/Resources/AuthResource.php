<?php

namespace App\Http\Resources;

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
        $data['email_verified'] = $data['email_verified_at'] != null;
        $data['created_at'] = date('Y-m-d H:i:s', strtotime($data['created_at']));
        unset($data['updated_at'], $data['deleted_at'], $data['email_verified_at']);

        return $data;
    }
}
