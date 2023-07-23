<?php

namespace App\Http\Requests\Product;

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
            'name' => ['required', 'string'],
            'description' => ['required'],
            'full_description' => ['required'],
            'buy_price' => ['required', 'numeric'],
            'sell_price' => ['required', 'numeric', 'gte:buy_price'],
            'discount' => ['required', 'numeric'],
            'in_stock' => ['required', 'string'],
            // 'quantity' => ['required', 'numeric'],
            // 'item_number' => ['required', 'unique:products,item_number'],
            'weight' => ['numeric'],
            // 'categories' => ['required', 'array'],
            // 'media' => ['required', 'array', 'max:5'],
            // 'media.*' => ['file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
