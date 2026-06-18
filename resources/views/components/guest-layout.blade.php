<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Sign in' }} - Consumer Protection Law System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 text-slate-900 antialiased">
    <div class="flex min-h-full flex-col items-center justify-center px-4 py-10 sm:px-6">
        <a href="{{ route('login') }}" class="mb-6 flex items-center gap-2.5">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-700 text-white shadow-sm">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 3v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3z" />
                </svg>
            </span>
            <span class="text-left">
                <span class="block text-lg font-bold leading-tight text-blue-700">ConsumerLaw</span>
                <span class="block text-xs leading-tight text-slate-500">Protection System</span>
            </span>
        </a>

        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-900/5 sm:p-10">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
