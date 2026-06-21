<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ludexora — Legal Intelligence Platform</title>
    <link rel="icon" type="image/svg+xml" href="/images/ludexora-logo.svg">
    <link rel="icon" type="image/png" href="/images/ludexora-icon.png">
    <link rel="shortcut icon" href="/images/ludexora-icon.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&family=space-grotesk:500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/marked@12/marked.min.js"></script>
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        .lx-brand { font-family: 'Space Grotesk', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="h-full overflow-hidden bg-slate-50 text-slate-900 antialiased">
    @php $historyItems = $history ?? collect(); @endphp

    <div class="flex h-[100dvh] max-h-[100dvh] min-h-0 overflow-hidden"
         x-data="{ open: false, query: '' }">

        {{-- Mobile overlay --}}
        <div x-show="open" x-transition.opacity x-cloak
             class="fixed inset-0 z-30 bg-black/60 backdrop-blur-sm lg:hidden"
             @click="open = false"></div>

        {{-- ── Sidebar ──────────────────────────────────────────────── --}}
        <aside class="fixed inset-y-0 left-0 z-40 flex h-full w-60 shrink-0 flex-col transition-transform duration-200 lg:static lg:translate-x-0"
               :class="open ? 'translate-x-0' : '-translate-x-full'"
               style="background:linear-gradient(180deg,#060E1C 0%,#0B1A30 100%);">

            {{-- Brand --}}
            <div class="flex shrink-0 items-center gap-2.5 border-b border-white/[.06] px-4 py-4">
                <div class="lx-logo-glow flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                    <img src="/images/ludexora-logo.svg" alt="" class="h-8 w-8 object-contain">
                </div>
                <div class="min-w-0">
                    <p class="lx-brand text-[14px] font-semibold leading-none tracking-wide text-white">LUDEXORA</p>
                    <p class="mt-0.5 text-[9px] font-medium uppercase tracking-widest text-blue-400/60">Legal Intelligence</p>
                </div>
                {{-- Mobile close --}}
                <button type="button" class="ml-auto flex h-7 w-7 items-center justify-center rounded-md text-blue-300/50 hover:text-blue-200 lg:hidden"
                        @click="open = false">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- New chat button --}}
            <div class="shrink-0 px-3 pt-3">
                <a href="{{ route('chat.new') }}"
                   class="flex items-center gap-2 rounded-lg px-3 py-2 text-[13px] font-semibold text-white transition-all"
                   style="background:linear-gradient(135deg,#1D4ED8,#3B82F6);box-shadow:0 3px 10px -3px rgba(59,130,246,.5);">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                    </svg>
                    New Consultation
                </a>
            </div>

            {{-- Primary nav --}}
            <nav class="shrink-0 px-3 pt-2 pb-1 space-y-0.5">
                <a href="{{ route('home') }}"
                   class="lx-nav-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] text-blue-100/65 {{ request()->routeIs('home') ? 'active' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-blue-400/80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5V21a1 1 0 01-1 1h-5v-7h-6v7H4a1 1 0 01-1-1z"/>
                    </svg>
                    <span class="font-medium">Home</span>
                </a>
                <a href="{{ route('chat.index') }}"
                   class="lx-nav-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] text-blue-100/65 {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-blue-400/80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    <span class="font-medium">Legal Assistant</span>
                </a>
                <a href="{{ route('laws.index') }}"
                   class="lx-nav-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] text-blue-100/65 {{ request()->routeIs('laws.*') ? 'active' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-blue-400/80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="font-medium">Browse Laws</span>
                </a>
            </nav>

            {{-- Chat history --}}
            @auth
            <div class="flex min-h-0 flex-1 flex-col overflow-hidden px-3 pb-2">
                <div class="mb-1.5 flex shrink-0 items-center gap-1.5 px-3 pt-3">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400/40">Recent</p>
                </div>

                {{-- Inline search --}}
                <div class="mb-1.5 shrink-0">
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-blue-300/30"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/>
                        </svg>
                        <input type="text" x-model="query" placeholder="Search…"
                               class="w-full rounded-lg border border-white/[.07] bg-white/[.04] py-1.5 pl-7 pr-3 text-[12px] text-white/70 placeholder:text-blue-300/30 focus:border-blue-500/40 focus:outline-none focus:ring-1 focus:ring-blue-500/20">
                    </div>
                </div>

                <div class="sidebar-history-scroll min-h-0 flex-1 space-y-0.5 overflow-y-auto">
                    @forelse($historyItems as $item)
                        @php $title = $item->chat_title ?? 'Untitled'; @endphp
                        <a class="lx-nav-link flex items-center gap-2 rounded-lg px-3 py-1.5 text-[12px] text-blue-100/55 {{ request()->query('session') === $item->session_id ? 'active' : '' }}"
                           href="{{ route('chat.index', ['session' => $item->session_id]) }}"
                           x-data="{ title: @js(strtolower($title)) }"
                           x-show="!query || title.includes(query.toLowerCase())">
                            <svg class="h-3 w-3 shrink-0 text-blue-500/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            <span class="truncate">{{ \Illuminate\Support\Str::limit($title, 26) }}</span>
                        </a>
                    @empty
                        <p class="px-3 py-2 text-[11px] italic text-blue-300/30">No sessions yet.</p>
                    @endforelse
                </div>
            </div>
            @endauth

            {{-- User footer --}}
            @auth
            <div class="shrink-0 border-t border-white/[.05] px-3 py-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[11px] font-bold text-white"
                         style="background:linear-gradient(135deg,#2563EB,#06B6D4);">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[12px] font-medium text-white/80">{{ Auth::user()->name ?? 'User' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Sign out"
                                class="flex h-6 w-6 items-center justify-center rounded text-blue-300/40 transition-colors hover:text-blue-200">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </aside>

        {{-- ── Main ─────────────────────────────────────────────────── --}}
        <main class="flex min-h-0 flex-1 flex-col overflow-hidden">
            {{-- Mobile topbar --}}
            <div class="flex shrink-0 items-center gap-3 border-b border-slate-200 bg-white px-4 py-3 lg:hidden">
                <button type="button" class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100"
                        @click="open = true" aria-label="Open menu">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="flex items-center gap-2">
                    <img src="/images/ludexora-logo.svg" alt="" class="h-6 w-6 object-contain">
                    <span class="lx-brand text-[14px] font-semibold text-slate-900">LUDEXORA</span>
                </div>
            </div>

            <div class="relative flex min-h-0 flex-1 flex-col overflow-hidden">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
