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
            'meal.name' => 'required|string|max:255',
            'meal.meal_type' => 'nullable|string',
            'meal.date' => 'required|date',
            'meal.meal_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            "meal_items" => 'nullable|array|min:1',
            "meal_items.*.id" => 'required|exists:food,id',
            "meal_items.*.quantity" => 'required|integer|min:1',
            'meal_items.*.unite' => 'required|in:g,ml,piece',
        ];
    }

    public function messages(): array
    {
        return [
            "meal.name.required" => "Name is required",
            "meal.name.string" => "Name must be a string",
            "meal.name.max" => "Name must not exceed 255 characters",
            "meal.meal_type.string" => "Meal type must be a string",
            "meal.date.required" => "Date is required",
            "meal.date.date" => "Date must be a valid date",
            "meal.unite.required" => "Unite is required",
            "meal.meal_image.image" => "File must be an image",
            "meal.meal_image.mimes" => "Image must be a jpeg, png, jpg or gif",
            "meal.meal_image.max" => "Image must not exceed 2MB",
            "meal_items.array" => "Meal items must be an array",
            "meal_items.min" => "At least one meal item is required",
            "meal_items.*.id.required" => "Food ID is required",
            "meal_items.*.id.exists" => "Food ID must exist in the food table",
            "meal_items.*.quantity.required" => "Quantity is required",
            "meal_items.*.quantity.integer" => "Quantity must be an integer",
            "meal_items.*.quantity.min" => "Quantity must be at least 1",
        ];
    }
}
