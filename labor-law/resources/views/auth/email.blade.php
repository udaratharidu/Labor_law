@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-xl p-8 lg:p-12">
        <div class="rounded-2xl bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-bold">Passwordless Login</h2>
            <p class="mt-2 text-slate-600">Enter your email address and we will send a 6-digit OTP.</p>

            @if (session('status'))
                <p class="mt-4 rounded-md bg-green-50 p-3 text-sm text-green-700">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('auth.send-otp') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button class="w-full rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white">Send OTP</button>
            </form>
        </div>
    </div>
@endsection
