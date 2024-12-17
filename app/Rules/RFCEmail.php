<?php

namespace App\Rules;

use Closure;
use Egulias\EmailValidator\Validation\RFCValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Egulias\EmailValidator\EmailValidator;

class RFCEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validator = new EmailValidator();
        $isValid = $validator->isValid($value, new RFCValidation());
        if (! $isValid) {
            $fail($attribute . ' is not a valid email address.');
        }
    }
}
