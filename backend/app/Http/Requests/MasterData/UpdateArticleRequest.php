<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
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
        $articleId = $this->route('id') ?? $this->route('article');
        
        return [
            'ArticleName' => 'required|string|max:255|unique:articles,ArticleName,' . $articleId . ',ArticleID',
            'Description' => 'nullable|string|max:1000',
            'ArticleImage' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'RemoveImage' => 'nullable|boolean',
            'PropertyTypeID' => 'required|integer|exists:propertytype,PropertyTypeID',
            'DefaultUnitOfMeasureID' => 'required|integer|exists:unitofmeasure,UnitOfMeasureID',
            'IsActive' => 'boolean',
        ];
    }
}