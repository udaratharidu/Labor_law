<x-guest-layout :title="'Sign in'">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-slate-900">Welcome back</h2>
        <p class="mt-1 text-sm text-slate-400">Sign in to continue your legal research.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block mb-1.5 text-[13px] font-semibold text-slate-700">Email address</label>
            <input
                id="email" type="email" name="email"
                value="{{ old('email') }}"
                required autofocus autocomplete="username"
                placeholder="you@example.com"
                class="lx-input w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition-all"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <label for="password" class="text-[13px] font-semibold text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-[13px] font-medium text-blue-600 hover:text-blue-700">
                        Forgot password?
                    </a>
                @endif
            </div>
            <input
                id="password" type="password" name="password"
                required autocomplete="current-password"
                placeholder="••••••••"
                class="lx-input w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition-all"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="flex items-center gap-2.5 cursor-pointer">
            <input id="remember_me" type="checkbox" name="remember"
                   class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-slate-600">Remember me</span>
        </label>

        <button type="submit"
                class="lx-btn-primary w-full rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-md">
            Sign in to Ludexora
        </button>
    </form>

    @if (Route::has('register'))
        <p class="mt-6 text-center text-sm text-slate-500">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700">Create one free</a>
        </p>
    @endif
</x-guest-layout>
