<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyTypeRequest extends FormRequest
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
            'PropertyClassificationID' => 'sometimes|required|integer|exists:propertyclassification,PropertyClassificationID',
            'PropertyName' => 'sometimes|required|string|max:100',
            'Description' => 'nullable|string|max:255',
        ];
    }
}
