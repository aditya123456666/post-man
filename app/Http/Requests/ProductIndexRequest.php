<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'nullable', 'string', 'max:100'],
            'min_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'max_price' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
                Rule::when($this->filled('min_price'), ['gte:min_price']),
            ],
            'in_stock' => ['sometimes', 'boolean'],
            'sort' => ['sometimes', Rule::in(['name', 'price', 'created_at'])],
            'direction' => ['sometimes', Rule::in(['asc', 'desc'])],
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ];
    }
}
