<?php
namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\ImageService::class);
    }

    public function boot(): void
    {
        $this->configureMail();
    }

    /**
     * Local dev always mails through Mailhog via .env (MAIL_HOST/MAIL_PORT above).
     * In every other environment, SMTP is instead sourced from the admin
     * Settings page (Email/SMTP tab) so the host can change it without a deploy.
     */
    private function configureMail(): void
    {
        if ($this->app->environment('local', 'testing')) {
            return;
        }

        try {
            if (!Schema::hasTable('settings')) {
                return;
            }

            $host = Setting::get('mail_host');
            if (!$host) {
                return;
            }

            config([
                'mail.default'               => 'smtp',
                'mail.mailers.smtp.host'     => $host,
                'mail.mailers.smtp.port'     => Setting::get('mail_port', 587),
                'mail.mailers.smtp.username' => Setting::get('mail_username'),
                'mail.mailers.smtp.password' => Setting::get('mail_password'),
                'mail.from.address'          => Setting::get('mail_from_address', config('mail.from.address')),
                'mail.from.name'             => Setting::get('mail_from_name', config('mail.from.name')),
            ]);
        } catch (\Throwable $e) {
            // DB unavailable at boot (e.g. during initial deploy/migrate) — fall back to .env mail config.
        }
    }
}
