<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
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
            'SupplierName' => 'sometimes|required|string|max:100',
            'Address' => 'nullable|string|max:255',
            'ContactPerson' => 'nullable|string|max:100',
            'ContactNumber' => 'nullable|string|max:20',
            'Email' => 'nullable|email|max:100',
            'IsActive' => 'sometimes|boolean',
        ];
    }
}
