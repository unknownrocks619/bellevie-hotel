<?php
namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class RecaptchaService
{
    public static function isEnabled(): bool
    {
        return Setting::get('recaptcha_enabled', '0') === '1'
            && (bool) Setting::get('recaptcha_site_key')
            && (bool) Setting::get('recaptcha_secret_key');
    }

    public static function siteKey(): ?string
    {
        return Setting::get('recaptcha_site_key');
    }

    /**
     * Verify a submitted g-recaptcha-response token against Google's API.
     */
    public static function verify(?string $token): bool
    {
        if (!$token) {
            return false;
        }

        $secret = Setting::get('recaptcha_secret_key');
        if (!$secret) {
            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => $secret,
                'response' => $token,
                'remoteip' => Request::ip(),
            ]);

            return (bool) ($response->json('success') ?? false);
        } catch (\Throwable $e) {
            Log::error('reCAPTCHA verification request failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
