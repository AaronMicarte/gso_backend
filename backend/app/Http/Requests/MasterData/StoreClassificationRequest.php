<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'CategoryID' => 'required|integer|exists:category,CategoryID',
            'ClassificationName' => 'required|string|max:100',
            'Description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'CategoryID.required' => 'Category is required.',
            'CategoryID.exists' => 'Selected category does not exist.',
            'ClassificationName.required' => 'Classification name is required.',
            'ClassificationName.max' => 'Classification name cannot exceed 100 characters.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check your input.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
