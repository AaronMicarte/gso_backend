<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled via middleware/policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $departmentId = $this->route('id');

        return [
            'DepartmentCode' => [
                'required',
                'string',
                'max:50',
                \Illuminate\Validation\Rule::unique('department', 'DepartmentCode')->ignore($departmentId, 'DepartmentID'),
            ],
            'DepartmentName' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('department', 'DepartmentName')->ignore($departmentId, 'DepartmentID'),
            ],
            'Description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'DepartmentCode.required' => 'Department code is required.',
            'DepartmentCode.unique' => 'This department code already exists.',
            'DepartmentCode.max' => 'Department code cannot exceed 50 characters.',
            'DepartmentName.required' => 'Department name is required.',
            'DepartmentName.unique' => 'This department name already exists.',
            'DepartmentName.max' => 'Department name cannot exceed 255 characters.',
            'Description.max' => 'Description cannot exceed 500 characters.',
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
