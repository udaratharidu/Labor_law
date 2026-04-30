@extends('layouts.main')

@section('content')
    <div class="p-8 lg:p-12">
        <div class="mb-4 flex items-center justify-between gap-4">
            <h2 class="text-3xl font-bold">Legal Assistant</h2>
            <div class="flex items-center gap-2">
                @auth
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button class="rounded-lg border border-slate-300 px-4 py-2">Logout</button>
                    </form>
                @else
                    <a href="{{ route('auth.email') }}" class="rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white">Login with OTP</a>
                @endauth
            </div>
        </div>

        @if ($isNewChat)
            <div class="mb-6 rounded-xl border border-blue-100 bg-blue-50 p-6 text-blue-900">
                @auth
                    Welcome back. Your conversation is ready - ask your first question to begin.
                @else
                    Welcome to Legal Assistant. Start your first conversation by asking a consumer law question.
                @endauth
            </div>
        @endif

        <div class="space-y-4">
            @foreach ($chats as $chat)
                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <p class="font-semibold text-slate-700">You: {{ $chat->message }}</p>
                    <p class="mt-2 text-slate-600">Assistant: {{ $chat->response }}</p>
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ route('chat.store') }}" class="mt-8 rounded-xl bg-white p-5 shadow-sm">
            @csrf
            <label class="mb-2 block font-medium">Ask your question</label>
            <textarea name="message" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>{{ old('message') }}</textarea>
            @error('message')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <button class="mt-4 rounded-lg bg-blue-700 px-5 py-2 font-semibold text-white">Send Message</button>
        </form>
    </div>
@endsection
