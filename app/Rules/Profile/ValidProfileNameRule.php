<?php

namespace App\Rules\Profile;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidProfileNameRule implements ValidationRule {
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!preg_match('/^[a-zA-Z\s\-]+$/', $value)) {
            $fail('The :attribute may only contain letters, spaces and dashes.');
        }
    }
}
