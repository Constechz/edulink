<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Mail\Events\MessageSent;
use App\Models\EmailLog;
use App\Models\NotificationLog;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load global system SMTP settings dynamically if configured in database
        try {
            $platformName = \App\Models\SystemSetting::getVal('platform_name');
            if ($platformName) {
                config(['app.name' => $platformName]);
            } else {
                config(['app.name' => 'EduLink']);
            }

            $host = \App\Models\SystemSetting::getVal('smtp_host');
            if ($host) {
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => $host,
                    'mail.mailers.smtp.port' => (int) \App\Models\SystemSetting::getVal('smtp_port', 2525),
                    'mail.mailers.smtp.encryption' => \App\Models\SystemSetting::getVal('smtp_encryption', 'tls'),
                    'mail.mailers.smtp.username' => \App\Models\SystemSetting::getVal('smtp_username'),
                    'mail.mailers.smtp.password' => \App\Models\SystemSetting::getVal('smtp_password'),
                    'mail.from.address' => \App\Models\SystemSetting::getVal('mail_from_address', 'hello@example.com'),
                    'mail.from.name' => \App\Models\SystemSetting::getVal('mail_from_name', config('app.name') . ' Ghana ERP'),
                ]);
            }
        } catch (\Exception $e) {
            // Prevent failure during command line migrations, seeding or testing setups
        }

        Gate::before(function ($user, $ability) {
            if ($user instanceof \App\Models\PlatformAdmin) {
                return true;
            }
            if ($user instanceof \App\Models\User) {
                if ($user->hasPermission($ability)) {
                    return true;
                }
            }
        });

        // Intercept and database-log all outgoing emails system-wide
        Event::listen(MessageSent::class, function (MessageSent $event) {
            try {
                $message = $event->message;
                
                $recipients = [];
                if ($message->getTo()) {
                    foreach ($message->getTo() as $address) {
                        $recipients[] = $address->getAddress();
                    }
                }
                $recipientString = implode(', ', $recipients);

                $body = $message->getHtmlBody() ?: $message->getTextBody() ?: '';

                EmailLog::create([
                    'recipient_email' => $recipientString ?: 'Unknown',
                    'subject' => $message->getSubject() ?: '(No Subject)',
                    'body' => $body,
                    'status' => 'sent',
                ]);
            } catch (\Exception $e) {
                // Fail silently to prevent mail-send exceptions from crashing the app
            }
        });

        // View Composer to share unread notifications with layout header
        view()->composer('layouts.app', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $notifications = NotificationLog::where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
                $unreadCount = NotificationLog::where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->where('is_read', false)
                    ->count();
                $view->with(compact('notifications', 'unreadCount'));
            }
        });
    }
}
