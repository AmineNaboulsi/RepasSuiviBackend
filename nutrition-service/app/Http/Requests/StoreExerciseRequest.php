<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExerciseRequest extends FormRequest
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
            'type' => 'nullable|integer',
            'BurnedCatories' => 'required|numeric',
            'dateActivity' => 'required|date',
            'timeStart' => 'required|date_format:H:i',
            'timeEnd' => 'required|date_format:H:i|after:timeStart',
        ];
    }
}
