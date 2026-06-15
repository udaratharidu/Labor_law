<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Consumer Protection Law System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full overflow-hidden bg-slate-100 text-slate-900">
    @php
        $historyItems = $history ?? collect();
    @endphp
    <div class="flex h-[100dvh] max-h-[100dvh] min-h-0 overflow-hidden" x-data="{ sidebarCollapsed: false, searchOpen: false, query: '' }">
        <aside
            class="flex h-full shrink-0 flex-col border-r border-slate-200/80 bg-slate-50 p-4 transition-all duration-300 lg:p-5"
            :class="sidebarCollapsed ? 'lg:w-16' : 'lg:w-64'"
        >
            <div class="mb-6 flex items-center gap-2">
                <button
                    type="button"
                    class="rounded-lg p-2 text-blue-700 transition-colors hover:bg-blue-100"
                    aria-label="Toggle sidebar"
                    @click="sidebarCollapsed = !sidebarCollapsed; if (sidebarCollapsed) { searchOpen = false; query = '' }"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <button
                    type="button"
                    class="rounded-lg p-2 text-blue-700 transition-colors hover:bg-blue-100"
                    aria-label="Search history"
                    @click="searchOpen = !searchOpen; if (!searchOpen) { query = '' }"
                    x-show="!sidebarCollapsed"
                    x-transition.opacity
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3"></path>
                    </svg>
                </button>
                <div x-show="!sidebarCollapsed" x-transition.opacity>
                    <h1 class="text-xl font-bold text-blue-700">ConsumerLaw</h1>
                    <p class="text-xs text-slate-500">Protection System</p>
                </div>
            </div>

            <nav class="flex min-h-0 flex-1 flex-col space-y-1 overflow-hidden">
                <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-slate-700 transition-colors hover:bg-blue-50" href="{{ route('home') }}" title="Home">
                    <svg class="h-4 w-4 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7h-6v7H4a1 1 0 0 1-1-1v-10.5z" />
                    </svg>
                    <span x-show="!sidebarCollapsed" x-transition.opacity>Home</span>
                </a>
                <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-slate-700 transition-colors hover:bg-blue-50" href="{{ route('chat.index') }}" title="Legal Assistant">
                    <svg class="h-4 w-4 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M3 12h18" />
                    </svg>
                    <span x-show="!sidebarCollapsed" x-transition.opacity>Legal Assistant</span>
                </a>
                <a class="mt-2 flex items-center gap-3 rounded-lg bg-blue-700 px-3 py-2 text-white transition-colors hover:bg-blue-800" href="{{ route('chat.new') }}" title="New Chat">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                    </svg>
                    <span x-show="!sidebarCollapsed" x-transition.opacity>New Chat</span>
                </a>

                @auth
                    <div class="mt-5 flex min-h-0 flex-1 flex-col overflow-hidden" x-show="!sidebarCollapsed" x-transition.opacity>
                        <div class="flex items-center px-3 py-1 text-blue-800">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Chat History</p>
                        </div>

                        <div x-cloak x-show="searchOpen" class="mt-2 px-3">
                            <input
                                type="text"
                                x-model="query"
                                placeholder="Search chats..."
                                class="w-full rounded-lg border border-blue-100 bg-white px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >
                        </div>

                        <div class="sidebar-history-scroll mt-2 min-h-0 max-h-[55vh] flex-1 space-y-1 overflow-y-auto pr-1">
                            @forelse ($historyItems as $item)
                                <a
                                    class="block rounded-lg px-3 py-2.5 text-sm text-slate-700 transition-colors hover:bg-blue-50"
                                    href="{{ route('chat.index') }}"
                                    x-data="{ title: @js(\Illuminate\Support\Str::lower($item->message)) }"
                                    x-show="title.includes(query.toLowerCase())"
                                >
                                    <span class="block truncate">{{ \Illuminate\Support\Str::limit($item->message, 20) }}</span>
                                </a>
                            @empty
                                <p class="px-3 py-2 text-sm text-slate-500">No chats yet.</p>
                            @endforelse
                        </div>
                    </div>
                @endauth
            </nav>
        </aside>
        <main class="flex min-h-0 flex-1 flex-col overflow-hidden bg-slate-50 transition-all duration-300">
            {{-- Ensures page content (e.g. chat) can use flex-1 + min-h-0 for a full-height column --}}
            <div class="relative flex min-h-0 flex-1 flex-col overflow-hidden">
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
