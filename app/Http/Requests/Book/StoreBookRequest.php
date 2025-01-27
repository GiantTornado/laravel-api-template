<?php

namespace App\Http\Requests\Book;

use App\Rules\NoDigitsRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest {
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
            'title' => ['required', 'string', 'max:255', 'unique:books,title', new NoDigitsRule],
            'description' => ['string', 'max:1000'],
            'published_at' => ['date', 'before_or_equal:'.now()->toDateString()],
            'price' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'string', 'exists:categories,id'],
            'author_ids' => ['array'],
            'author_ids.*' => ['integer', 'exists:authors,id'],
        ];
    }
}
