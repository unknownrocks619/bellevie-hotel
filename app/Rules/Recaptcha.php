<?php
namespace App\Rules;

use App\Services\RecaptchaService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Recaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // No-op when reCAPTCHA isn't configured/enabled in Settings → Security.
        if (!RecaptchaService::isEnabled()) {
            return;
        }

        if (!RecaptchaService::verify(is_string($value) ? $value : null)) {
            $fail('Please verify that you are not a robot.');
        }
    }
}
