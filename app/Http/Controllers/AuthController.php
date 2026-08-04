<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Artist;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:artist,record_label',
        ]);

        // Generate verification code
        $verificationCode = rand(100000, 999999);

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active', // active but email/phone unverified
            'verification_code' => $verificationCode,
            'notification_preferences' => [
                'email' => true,
                'sms' => true,
                'approvals' => true,
                'royalties' => true,
            ]
        ]);

        // For convenience in local testing, we log the code and save it in session
        Log::info("Verification code for User ID {$user->id} ({$user->email}): {$verificationCode}");
        session(['verify_email' => $user->email, 'debug_verification_code' => $verificationCode]);

        // Audit Log
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'register',
            'description' => "User registered as {$request->role} with email {$request->email}.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return redirect()->route('verify.show')->with('success', 'Registration successful! Enter the verification code sent to your email/phone.');
    }

    public function showVerify()
    {
        return view('auth.verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'code' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email address not found.'])->withInput();
        }

        if ($user->verification_code === $request->code) {
            $user->email_verified_at = now();
            $user->phone_verified_at = now(); // verify phone as well
            $user->verification_code = null;
            $user->save();

            // If user is an artist, automatically initialize their artist profile
            if ($user->role === 'artist' && !$user->artist) {
                Artist::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'verification_status' => 'pending',
                ]);
            }

            // Log user in
            Auth::login($user);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'verify_account',
                'description' => 'Account verified successfully.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return redirect()->route('dashboard')->with('success', 'Account verified and logged in!');
        }

        return back()->withErrors(['code' => 'Invalid verification code. Please check and try again.'])->withInput();
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // can be email or phone
            'password' => 'required|string',
        ]);

        // Check if login input is email or phone
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $user = User::where($loginType, $request->login)->first();

        if (!$user) {
            return back()->withErrors(['login' => 'No account found with those credentials.'])->withInput();
        }

        if ($user->status === 'suspended') {
            return back()->withErrors(['login' => 'Your account has been suspended. Please contact support.'])->withInput();
        }

        if ($user->isDistributor()) {
            return back()->withErrors(['login' => 'Distributors are not allowed to sign in.'])->withInput();
        }

        if ($user->status === 'deactivated') {
            // Reactivate account
            $user->status = 'active';
            $user->save();
        }

        // Attempt login
        $remember = $request->has('remember');
        if (Auth::attempt([$loginType => $request->login, 'password' => $request->password], $remember)) {
            // Check if verified
            if (is_null(Auth::user()->email_verified_at)) {
                $verificationCode = rand(100000, 999999);
                Auth::user()->verification_code = $verificationCode;
                Auth::user()->save();

                session(['verify_email' => Auth::user()->email, 'debug_verification_code' => $verificationCode]);
                Auth::logout();

                return redirect()->route('verify.show')->with('warning', 'Please verify your account before logging in. A new verification code was generated.');
            }

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'description' => 'User logged in successfully.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['password' => 'Incorrect password.'])->withInput();
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'logout',
                'description' => 'User logged out.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            Auth::logout();
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    public function showRecover()
    {
        return view('auth.recover');
    }

    public function recover(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
        ]);

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::where($loginType, $request->login)->first();

        if (!$user) {
            return back()->withErrors(['login' => 'No account found with this email/phone.'])->withInput();
        }

        // Generate recovery code
        $code = rand(100000, 999999);
        $user->verification_code = $code;
        $user->save();

        Log::info("Recovery code for User ID {$user->id}: {$code}");
        session(['reset_email' => $user->email, 'debug_reset_code' => $code]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'request_password_reset',
            'description' => 'Requested password recovery code.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return redirect()->route('reset.show')->with('success', 'A password recovery code has been sent!');
    }

    public function showReset()
    {
        return view('auth.reset');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'code' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email not found.'])->withInput();
        }

        if ($user->verification_code === $request->code) {
            $user->password = Hash::make($request->password);
            $user->verification_code = null;
            $user->save();

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'reset_password',
                'description' => 'Password reset successfully.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return redirect()->route('login')->with('success', 'Password reset successfully! You can now log in.');
        }

        return back()->withErrors(['code' => 'Invalid recovery code.'])->withInput();
    }
}
