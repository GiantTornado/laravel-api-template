<?php

namespace App\Rules\Category;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCategoryNameRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^[a-zA-Z0-9\s]+$/', $value)) {
            $fail('The :attribute must be alphanumeric.');
        }
    }
}
