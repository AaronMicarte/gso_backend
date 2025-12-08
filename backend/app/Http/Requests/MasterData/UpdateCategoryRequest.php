<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $categoryId = $this->route('id');

        return [
            'CategoryName' => [
                'required',
                'string',
                'max:100',
                Rule::unique('category', 'CategoryName')->ignore($categoryId, 'CategoryID'),
            ],
            'Description' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'CategoryName.required' => 'Category name is required.',
            'CategoryName.unique' => 'This category name already exists.',
            'CategoryName.max' => 'Category name cannot exceed 100 characters.',
            'Description.max' => 'Description cannot exceed 255 characters.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check your input.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
