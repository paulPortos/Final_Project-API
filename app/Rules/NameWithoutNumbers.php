<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class NameWithoutNumbers implements ValidationRule
{
    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        Log::info("Validating {$attribute}: {$value}");
        try {
            if (preg_match('/\d/', $value)) {
                $fail("{$attribute} must not contain numbers");
            }
        } catch (\Throwable $th) {
            $fail("{$attribute} must not contain numbers");
        }
    }
}
