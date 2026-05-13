<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => "required|min:3",
        'desc' => 'required',
        'slug' => 'required|unique:products,slug',
        'price' => 'nullable|numeric',
        'category_id' => 'required',
        
        'images' => 'nullable|array', 
        'images.*' => 'image|mimes:png,jpg,webp|max:2048',
        ];
    }
}
