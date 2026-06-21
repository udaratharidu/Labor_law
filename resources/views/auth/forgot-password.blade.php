<x-guest-layout :title="'Forgot password'">
    <div class="mb-7">
        <h2 class="text-2xl font-bold text-slate-900">Forgot your password?</h2>
        <p class="mt-1.5 text-sm text-slate-500">Enter your email and we'll send you a reset link.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block mb-1.5 text-[13px] font-semibold text-slate-700">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autofocus placeholder="you@example.com"
                   class="lx-input w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition-all">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="lx-btn-primary w-full rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-md">
            Send Reset Link
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Back to sign in</a>
    </p>
</x-guest-layout>
