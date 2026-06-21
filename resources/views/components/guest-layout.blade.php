<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Sign in' }} — Ludexora</title>
    <link rel="icon" type="image/svg+xml" href="/images/ludexora-logo.svg">
    <link rel="icon" type="image/png" href="/images/ludexora-icon.png">
    <link rel="shortcut icon" href="/images/ludexora-icon.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=space-grotesk:500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body      { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        .lx-brand { font-family: 'Space Grotesk', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="h-full antialiased">
<div class="flex min-h-full">

    {{-- ── Left: brand panel ─────────────────────────────────────── --}}
    <div class="relative hidden w-[420px] shrink-0 overflow-hidden lg:flex lg:flex-col"
         style="background:linear-gradient(150deg,#050D1A 0%,#0B1F40 60%,#0D2A5A 100%);">

        {{-- Subtle glow --}}
        <div class="pointer-events-none absolute inset-0"
             style="background:radial-gradient(ellipse at 50% 38%,rgba(59,130,246,.18) 0%,transparent 65%);"></div>

        {{-- All content vertically centered --}}
        <div class="relative flex flex-1 flex-col items-center justify-center px-10 py-12 text-center">

            {{-- Big Y-axis flipping logo --}}
            <div class="lx-logo-spin"
                 style="filter:drop-shadow(0 0 28px rgba(96,165,250,.65)) drop-shadow(0 0 10px rgba(34,211,238,.3));">
                <img src="/images/ludexora-logo.svg" alt="Ludexora" class="h-32 w-32 object-contain">
            </div>

            {{-- Brand name --}}
            <p class="lx-brand mt-8 text-[24px] font-semibold tracking-[.2em] text-white">LUDEXORA</p>
            <p class="mt-1.5 text-[9px] font-semibold uppercase tracking-[.3em] text-blue-400/55">Legal Intelligence</p>

            {{-- Divider --}}
            <div class="my-8 flex items-center gap-3 w-full px-6">
                <div class="flex-1 h-px" style="background:rgba(96,165,250,.12);"></div>
                <div class="h-1 w-1 rounded-full" style="background:rgba(96,165,250,.3);"></div>
                <div class="flex-1 h-px" style="background:rgba(96,165,250,.12);"></div>
            </div>

            {{-- Tagline --}}
            <h2 class="lx-brand text-[22px] font-semibold leading-snug text-white">
                Legal clarity,<br>
                <span style="background:linear-gradient(90deg,#60A5FA,#34D399);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;color:transparent;">
                    intelligently delivered.
                </span>
            </h2>
            <p class="mt-4 max-w-[240px] text-[12.5px] leading-relaxed text-blue-100/40">
                Consumer protection law research, AI-powered guidance, and regulation browsing.
            </p>

            {{-- Bullet points — centered --}}
            <ul class="mt-8 space-y-3 w-full max-w-[220px]">
                @foreach([
                    'Instant AI answers to legal questions',
                    'Full browser of acts & regulations',
                    'Saved consultation history',
                ] as $item)
                <li class="flex items-center gap-2.5 text-[12px] text-blue-100/55">
                    <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full"
                          style="background:rgba(96,165,250,.14);">
                        <svg class="h-2.5 w-2.5 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    {{ $item }}
                </li>
                @endforeach
            </ul>
        </div>

        <p class="relative pb-5 text-center text-[10px] text-blue-300/18">Informational use only. Not legal advice.</p>
    </div>

    {{-- ── Right: form ───────────────────────────────────────────── --}}
    <div class="flex flex-1 flex-col items-center justify-center bg-white px-6 py-10 sm:px-12">

        {{-- Mobile: compact logo + name (shown only on small screens) --}}
        <div class="mb-8 flex flex-col items-center text-center lg:hidden">
            <div class="lx-logo-spin mb-3"
                 style="filter:drop-shadow(0 0 14px rgba(96,165,250,.5));">
                <img src="/images/ludexora-logo.svg" alt="Ludexora" class="h-14 w-14 object-contain">
            </div>
            <p class="lx-brand text-[18px] font-semibold tracking-widest text-slate-900">LUDEXORA</p>
            <p class="text-[9px] font-medium uppercase tracking-[.22em] text-slate-400">Legal Intelligence</p>
        </div>

        <div class="w-full max-w-sm">
            {{ $slot }}
        </div>
    </div>
</div>
</body>
</html>
