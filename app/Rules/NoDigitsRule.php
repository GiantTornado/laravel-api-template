<?php

namespace App\Rules;

use App\Helpers\StringHelper;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoDigitsRule implements ValidationRule {
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (StringHelper::doesContainDigits($value)) {
            $fail('The :attribute must not contain any digit.');
        }
    }
}
