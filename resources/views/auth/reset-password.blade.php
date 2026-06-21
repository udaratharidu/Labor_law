<x-guest-layout :title="'Reset password'">
    <div class="mb-7">
        <h2 class="text-2xl font-bold text-slate-900">Reset your password</h2>
        <p class="mt-1.5 text-sm text-slate-500">Choose a strong new password for your account.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="block mb-1.5 text-[13px] font-semibold text-slate-700">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                   required autofocus autocomplete="username"
                   class="lx-input w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition-all">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block mb-1.5 text-[13px] font-semibold text-slate-700">New password</label>
            <input id="password" type="password" name="password"
                   required autocomplete="new-password" placeholder="Min. 8 characters"
                   class="lx-input w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition-all">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="block mb-1.5 text-[13px] font-semibold text-slate-700">Confirm new password</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   required autocomplete="new-password" placeholder="••••••••"
                   class="lx-input w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition-all">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="lx-btn-primary w-full rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-md">
            Reset Password
        </button>
    </form>
</x-guest-layout>
