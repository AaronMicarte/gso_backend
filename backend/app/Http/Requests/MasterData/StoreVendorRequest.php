<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'VendorName' => 'required|string|max:100',
            'Address' => 'nullable|string|max:255',
            'ContactPerson' => 'nullable|string|max:100',
            'ContactNumber' => 'nullable|string|max:20',
            'Email' => 'nullable|email|max:100',
            'IsActive' => 'sometimes|boolean',
        ];
    }
}
