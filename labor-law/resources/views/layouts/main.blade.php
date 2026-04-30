<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Consumer Protection Law System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="min-h-screen lg:flex">
        <aside class="w-full border-r border-slate-200 bg-white p-6 lg:w-64">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-blue-700">ConsumerLaw</h1>
                <p class="text-xs text-slate-500">Protection System</p>
            </div>
            <nav class="space-y-1">
                <a class="block rounded-lg px-3 py-2 hover:bg-blue-50" href="{{ route('home') }}">Home</a>
                <a class="block rounded-lg px-3 py-2 hover:bg-blue-50" href="{{ route('chat.index') }}">Legal Assistant</a>
                <a class="block rounded-lg px-3 py-2 hover:bg-blue-50" href="#">Data Browsing</a>
                <a class="block rounded-lg px-3 py-2 hover:bg-blue-50" href="#">Case Studies</a>
                <a class="block rounded-lg px-3 py-2 hover:bg-blue-50" href="#">Settings</a>
            </nav>
        </aside>
        <main class="flex-1">
            @yield('content')
        </main>
    </div>
</body>
</html>
