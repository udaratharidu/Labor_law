<x-guest-layout :title="'Forgot password'">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-slate-900">Forgot your password?</h1>
        <p class="mt-1.5 text-sm text-slate-500">
            {{ __('No problem. Enter your email and we will send you a password reset link.') }}
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button>
            {{ __('Email Password Reset Link') }}
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        <a href="{{ route('login') }}" class="font-medium text-blue-700 hover:text-blue-800">{{ __('Back to sign in') }}</a>
    </p>
</x-guest-layout>
