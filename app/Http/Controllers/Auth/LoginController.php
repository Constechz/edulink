<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PlatformAdmin;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show standard user login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $loginInput = $request->email;

        // Support both Email and Student ID login
        $user = null;
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $loginInput)->first();
        } else {
            $user = User::where('employee_id', $loginInput)->first();
        }

        if ($user && Hash::check($request->password, $user->password)) {
            // Check if student/parent portals are locked for this school
            if ($user->role && in_array($user->role->slug, ['student', 'parent'])) {
                $school = $user->school;
                if ($school) {
                    $schoolSettings = $school->settings ?: [];
                    $portalsUnlocked = isset($schoolSettings['portals_unlocked']) && $schoolSettings['portals_unlocked'] == true;
                    if (!$portalsUnlocked) {
                        return back()->withErrors([
                            'email' => 'Access denied. The student/parent portal access is currently inactive for this school. Please contact your school administrator.',
                        ])->onlyInput('email');
                    }
                }
            }

            // Check if MFA is active for this tenant user
            if ($user->mfa_enabled) {
                $this->authService->generateAndSendOtp($user, 'web');
                return redirect()->route('login.mfa');
            }

            // Normal login
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            return $this->redirectUser();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show platform admin login form.
     */
    public function showAdminLoginForm()
    {
        if (Auth::guard('platform_admin')->check()) {
            return redirect('/super-admin/dashboard');
        }
        return view('auth.admin_login');
    }

    /**
     * Handle platform admin login submission.
     */
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $admin = PlatformAdmin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            // Check if MFA is active for this platform admin
            if ($admin->mfa_enabled) {
                $this->authService->generateAndSendOtp($admin, 'platform_admin');
                return redirect()->route('login.mfa');
            }

            // Normal login
            Auth::guard('platform_admin')->login($admin, $request->boolean('remember'));
            $request->session()->regenerate();

            $admin->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            return redirect()->intended('/super-admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'The platform admin credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Logout standard or admin users.
     */
    public function logout(Request $request)
    {
        $isPlatformAdmin = Auth::guard('platform_admin')->check();

        Auth::logout();
        Auth::guard('platform_admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect($isPlatformAdmin ? '/admin/login' : '/login');
    }

    /**
     * Handle redirection logic based on user roles.
     */
    protected function redirectUser()
    {
        $user = Auth::user();
        if ($user->role && $user->role->slug === 'super-admin') {
            return redirect()->intended('/super-admin/dashboard');
        }
        
        // Save school_id in session for multi-tenancy
        if ($user->school_id) {
            session(['school_id' => $user->school_id]);
            
            $school = \App\Models\School::find($user->school_id);
            if ($school) {
                $subdomain = $school->subdomain;
                $path = '/dashboard';
                if (!$school->onboarding_completed) {
                    $path = '/school/onboarding';
                } elseif ($user->role) {
                    if ($user->role->slug === 'student') {
                        $path = '/school/student-portal/dashboard';
                    } elseif ($user->role->slug === 'parent') {
                        $path = '/school/parent-portal/dashboard';
                    }
                }
                
                // Redirect directly to the path-based school dashboard (e.g. /school-name/dashboard)
                return redirect()->to('/' . $subdomain . $path);
            }
        }
        
        return redirect()->intended('/');
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot_password');
    }

    /**
     * Handle the forgot password form submission.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Look up user to see if they exist (standard behavior for security/UX)
        $user = User::where('email', $request->email)->first();
        
        // Simulating email send
        return back()->with('status', 'We have emailed your password reset link! (Simulated)');
    }
}
