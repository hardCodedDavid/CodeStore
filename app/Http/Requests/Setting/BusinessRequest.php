<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class BusinessRequest extends FormRequest
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
            'name' => ['required'],
            'email' => ['required'],
            'phone_1' => ['required'],
            'address' => ['required'],
            'logo' => ['sometimes', 'mimes:jpg,jpeg,png,svg,gif', 'file', 'max:2048'],
            'dashboard_logo' => ['sometimes', 'mimes:jpg,jpeg,png,svg,gif', 'file', 'max:2048'],
        ];
    }
}
