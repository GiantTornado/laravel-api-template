<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetBookRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'sort_by' => Rule::in(['price']),
            'sort_order' => Rule::in(['asc', 'desc']),
        ];
    }

    public function messages(): array {
        return [
            'sort_by' => "The 'sortBy' parameter accepts only 'price' value.",
            'sort_order' => "The 'sortOrder' parameter accepts only 'asc' or 'desc' value.",
        ];
    }
}
