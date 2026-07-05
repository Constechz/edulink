<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PlatformAdmin;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MfaController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show MFA verification page.
     */
    public function showMfaForm(Request $request)
    {
        if (!session()->has('mfa_user_id') || !session()->has('mfa_guard')) {
            return redirect()->route('login');
        }

        return view('auth.mfa');
    }

    /**
     * Verify the MFA OTP.
     */
    public function verifyMfa(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        if (!session()->has('mfa_user_id') || !session()->has('mfa_guard')) {
            return redirect()->route('login')->withErrors(['session' => 'Verification session expired.']);
        }

        $userId = session()->get('mfa_user_id');
        $guard = session()->get('mfa_guard');
        $code = $request->code;

        if ($this->authService->validateOtp($userId, $guard, $code)) {
            // Retrieve actual user
            $user = null;
            if ($guard === 'platform_admin') {
                $user = PlatformAdmin::find($userId);
            } else {
                $user = User::find($userId);
            }

            if (!$user) {
                return redirect()->route('login')->withErrors(['session' => 'User not found.']);
            }

            // Perform login
            Auth::guard($guard)->login($user);

            // Update login details
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Clear temp session details
            session()->forget(['mfa_user_id', 'mfa_guard']);

            // Redirect to dashboard
            if ($guard === 'platform_admin') {
                return redirect()->intended('/super-admin/dashboard');
            } else {
                return redirect()->intended('/dashboard');
            }
        }

        return redirect()->back()->withErrors(['code' => 'The verification code provided is invalid or has expired.']);
    }
}
