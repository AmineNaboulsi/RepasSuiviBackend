<?php

namespace App\Http\Requests\Meal;

use Illuminate\Foundation\Http\FormRequest;

class StoreMealRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => ["required", "string"],
            "meal_type" => ["required", "string"],
            "meal_image" => ["required", "string"],
        ];
    }

    public function message(){
        return [
            "name.required" => "Name is required",
            "meal_type.required" => "Meal type is required",
            "meal_image.required" => "Meal image is required",
        ];
    }
}
