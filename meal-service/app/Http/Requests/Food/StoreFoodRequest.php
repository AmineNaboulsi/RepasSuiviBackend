<?php

namespace App\Http\Requests\Food;

use Illuminate\Foundation\Http\FormRequest;

class StoreFoodRequest extends FormRequest
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
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048"
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
            "calories.required" => "Les calories sont requises.",
            "proteins.required" => "Les protéines sont requises.",
            "glucides.required" => "Les glucides sont requis.",
            "lipides.required" => "Les lipides sont requis.",
            "category.required" => "La catégorie est requise.",
            "image.image" => "Le fichier doit être une image.",
            "image.mimes" => "L'image doit être au format jpeg, png, jpg ou gif.",
            "image.max" => "L'image ne doit pas dépasser 2 Mo."
        ];
    }
}