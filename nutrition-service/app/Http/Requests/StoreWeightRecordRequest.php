<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWeightRecordRequest extends FormRequest
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
            'weight' => 'required|numeric',
            'bodyFat' => 'nullable|numeric',
            'date' => 'nullable|date',
            'note' => 'nullable|string|max:255',
        ];
    }
    
    public function messages(): array    {        
        return [
            'weight.required' => 'Weight is required',
            'weight.numeric' => 'Weight must be a number',
            'bodyFat.numeric' => 'Body fat must be a number',
            'note.string' => 'Note must be text',
            'note.max' => 'Note cannot exceed 255 characters',
        ];
    }
}