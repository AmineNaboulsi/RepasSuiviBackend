<?php

namespace App\Http\Requests\Food;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFoodRequest extends FormRequest
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
            "name" => "required|string|unique:food,name",
            "calories" => "required|numeric",
            "proteins" => "required|numeric",
            "glucides" => "required|numeric",
            "lipides" => "required|numeric",
            "category" => "required|string|max:255",
        ];
    }
    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            "name.required" => "The name is required.",
            "name.unique" => "The name must be unique.",
            "calories.required" => "Calories are required.",
            "proteins.required" => "Proteins are required.",
            "glucides.required" => "glucides are required.",
            "lipides.required" => "Lipids are required.",
            "category.required" => "The category is required."
        ];
    }
}
