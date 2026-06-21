<x-guest-layout :title="'Create account'">
    <div class="mb-7">
        <h2 class="text-2xl font-bold text-slate-900">Create your account</h2>
        <p class="mt-1.5 text-sm text-slate-500">Start your legal research journey with Ludexora.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="block mb-1.5 text-[13px] font-semibold text-slate-700">Full name</label>
            <input
                id="name" type="text" name="name"
                value="{{ old('name') }}"
                required autofocus autocomplete="name"
                placeholder="Jane Doe"
                class="lx-input w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition-all"
            >
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="block mb-1.5 text-[13px] font-semibold text-slate-700">Email address</label>
            <input
                id="email" type="email" name="email"
                value="{{ old('email') }}"
                required autocomplete="username"
                placeholder="you@example.com"
                class="lx-input w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition-all"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block mb-1.5 text-[13px] font-semibold text-slate-700">Password</label>
            <input
                id="password" type="password" name="password"
                required autocomplete="new-password"
                placeholder="Min. 8 characters"
                class="lx-input w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition-all"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="block mb-1.5 text-[13px] font-semibold text-slate-700">Confirm password</label>
            <input
                id="password_confirmation" type="password" name="password_confirmation"
                required autocomplete="new-password"
                placeholder="••••••••"
                class="lx-input w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition-all"
            >
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit"
                class="lx-btn-primary w-full rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-md">
            Create account
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Sign in</a>
    </p>
</x-guest-layout>
