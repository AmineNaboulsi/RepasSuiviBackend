<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNutritionGoalsRequest extends FormRequest
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
            'proteinTarget' => 'required|numeric',
            'carbTarget' => 'required|numeric',
            'fatTarget' => 'required|numeric',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ];
    }
}
