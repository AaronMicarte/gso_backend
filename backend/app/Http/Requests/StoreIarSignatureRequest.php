<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIarSignatureRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'role' => 'required|string|in:inspected,accepted',
            'signature' => 'required', // can be file upload or base64, validation in controller
            'source' => 'nullable|string|in:canvas,upload,typed',
        ];
    }

    public function messages()
    {
        return [
            'role.in' => 'Role must be either inspected or accepted.',
            'signature.required' => 'Signature file or base64 is required.',
        ];
    }
}
