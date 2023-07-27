<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required'],
            'date' => ['required', 'date'],
            'products' => ['required', 'array'],
            'products.*.product' => ['required'],
            'products.*.quantity' => ['required'],
            'products.*.price' => ['required'],
            'products.*.old_item_numbers' => ['required', 'array'],
            'products.*.item_numbers' => ['sometimes', 'array']
        ];
    }
}
