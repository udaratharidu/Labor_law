<x-guest-layout :title="'Create account'">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-slate-900">Create your account</h1>
        <p class="mt-1.5 text-sm text-slate-500">Sign up to save your chat history and get started.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Jane Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button>
            {{ __('Create account') }}
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="font-medium text-blue-700 hover:text-blue-800">{{ __('Sign in') }}</a>
    </p>
</x-guest-layout>
