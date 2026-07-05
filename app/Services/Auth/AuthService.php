<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthService
{
    /**
     * Generate and send OTP code to user.
     */
    public function generateAndSendOtp($user, string $guard): bool
    {
        $otp = sprintf('%06d', mt_rand(100000, 999999));
        $cacheKey = "mfa_otp_{$guard}_{$user->id}";

        // Store OTP in cache for 10 minutes
        Cache::put($cacheKey, $otp, now()->addMinutes(10));

        // Store temp user in session for the controller to know who is logging in
        session()->put('mfa_user_id', $user->id);
        session()->put('mfa_guard', $guard);

        // For local development and automated testing, log the OTP
        Log::info("MFA OTP Generated for {$user->email} [Guard: {$guard}]: {$otp}");

        // Send email
        try {
            Mail::send([], [], function ($message) use ($user, $otp) {
                $message->to($user->email)
                    ->subject('Your ' . config('app.name', 'EduLink') . ' MFA Verification Code')
                    ->html("
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;'>
                            <h2 style='color: #1e293b; text-align: center;'>" . config('app.name', 'EduLink') . " MFA Code</h2>
                            <p style='color: #475569;'>Hello {$user->name},</p>
                            <p style='color: #475569;'>Your dynamic verification code is:</p>
                            <div style='background-color: #f8fafc; border: 1px dashed #cbd5e1; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 4px; color: #0f172a; margin: 20px 0;'>
                                {$otp}
                            </div>
                            <p style='color: #64748b; font-size: 12px; text-align: center;'>This code is valid for 10 minutes. If you did not request this, please secure your account immediately.</p>
                        </div>
                    ");
            });
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send MFA email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate the OTP code.
     */
    public function validateOtp(int $userId, string $guard, string $code): bool
    {
        $cacheKey = "mfa_otp_{$guard}_{$userId}";
        $storedOtp = Cache::get($cacheKey);

        if ($storedOtp && $storedOtp === $code) {
            Cache::forget($cacheKey);
            return true;
        }

        return false;
    }
}
