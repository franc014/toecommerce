<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserInfoEntryRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:billing,shipping',
            'email' => 'required|email',
            'first_name' => 'required|min:2|max:16',
            'last_name' => 'required|min:2|max:16',
            'country' => 'max:24',
            'city' => 'required|min:2|max:24',
            'address' => 'required|min:2|max:128',
            'state' => 'max:24',
            'phone' => 'max:24',
            'zipcode' => 'required|min:4|max:6',
        ];
    }
}
