<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpCodeMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class OtpAuthController extends Controller
{
    public function showEmailForm()
    {
        return view('auth.email');
    }

    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $code = (string) random_int(100000, 999999);

        Otp::query()->where('email', $validated['email'])->delete();

        Otp::query()->create([
            'email' => $validated['email'],
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($validated['email'])->send(new OtpCodeMail($code));

        session(['otp_email' => $validated['email']]);

        return redirect()->route('auth.verify')->with('status', 'OTP sent successfully.');
    }

    public function showVerifyForm()
    {
        if (! session()->has('otp_email')) {
            return redirect()->route('auth.email');
        }

        return view('auth.verify', ['email' => session('otp_email')]);
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $email = session('otp_email');
        if (! $email) {
            throw ValidationException::withMessages([
                'code' => 'Email session expired. Please request a new OTP.',
            ]);
        }

        $otp = Otp::query()
            ->activeForEmail($email)
            ->where('code', $validated['code'])
            ->latest('id')
            ->first();

        if (! $otp) {
            throw ValidationException::withMessages([
                'code' => 'Invalid or expired OTP.',
            ]);
        }

        $defaults = ['email_verified_at' => now()];

        if (Schema::hasColumn('users', 'name')) {
            $defaults['name'] = Str::before($email, '@');
        }

        if (Schema::hasColumn('users', 'password')) {
            $defaults['password'] = Hash::make(Str::random(32));
        }

        $user = User::query()->firstOrCreate(['email' => $email], $defaults);

        if (! $user->email_verified_at) {
            $user->update(['email_verified_at' => now()]);
        }

        Auth::login($user);
        $otp->delete();
        session()->forget('otp_email');

        return redirect()->route('chat.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
