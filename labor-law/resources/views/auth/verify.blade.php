@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-xl p-8 lg:p-12">
        <div class="rounded-2xl bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-bold">Verify OTP</h2>
            <p class="mt-2 text-slate-600">We sent an OTP to {{ $email }}.</p>

            <form method="POST" action="{{ route('auth.verify-otp') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium">6-digit OTP</label>
                    <input name="code" type="text" maxlength="6" value="{{ old('code') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button class="w-full rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white">Verify & Login</button>
            </form>
        </div>
    </div>
@endsection
