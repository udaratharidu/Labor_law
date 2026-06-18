<x-guest-layout :title="'Verify email'">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-slate-900">Verify your email</h1>
        <p class="mt-1.5 text-sm text-slate-500">
            {{ __("Thanks for signing up! Please verify your email by clicking the link we just sent you. Didn't get it? We'll gladly send another.") }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
            {{ __('A new verification link has been sent to the email address you provided.') }}
        </div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
            @csrf
            <x-primary-button class="sm:w-auto sm:px-6">
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-slate-500 hover:text-slate-700">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
