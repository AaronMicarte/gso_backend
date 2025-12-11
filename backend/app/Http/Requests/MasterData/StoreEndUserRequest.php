<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class StoreEndUserRequest extends FormRequest
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
            'FullName' => 'required|string|max:100',
            'DepartmentID' => 'required|integer|exists:department,DepartmentID',
            'PositionID' => 'required|integer|exists:position,PositionID',
            'ContactNumber' => 'nullable|string|max:20',
            'Email' => 'nullable|email|max:100',
            'IsActive' => 'sometimes|boolean',
        ];
    }
}
