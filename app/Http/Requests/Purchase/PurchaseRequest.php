<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'supplier' => ['required'],
            'date' => ['required', 'date'],
            'products' => ['required', 'array'],
            'products.*.product' => ['required'],
            'products.*.quantity' => ['required'],
            'products.*.price' => ['required'],
           'products.*.item_numbers' => ['required', 'array'],
            'products.*.item_numbers.*' => ['required', 'unique:item_numbers,no'],
        
            // 'products.*.product.required' => 'The product field is required',
            // 'products.*.quantity.required' => 'The quantity field is required',
            // 'products.*.price.required' => 'The price field is required',
            // 'products.*.item_numbers.*.required' => 'The item number field is required',
            // 'products.*.item_numbers.*.unique' => 'The item number already exist',
        ];
    }
}
