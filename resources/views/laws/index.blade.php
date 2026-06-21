@extends('layouts.main')

@section('content')
    <div class="absolute inset-0 flex min-h-0 flex-col overflow-hidden bg-slate-50">
        <section class="relative shrink-0 overflow-hidden px-4 py-8 text-white lg:px-12 lg:py-10"
                 style="background: linear-gradient(135deg, #050D1A 0%, #0B1729 50%, #1A3A5C 100%);">
            <div class="pointer-events-none absolute inset-0 lx-circuit-bg opacity-30"></div>
            <div class="relative">
                <p class="text-sm font-medium text-blue-300/70">
                    <a href="{{ route('home') }}" class="hover:text-blue-200 transition-colors">Home</a>
                    <span class="px-1 text-blue-400/40">/</span>
                    Browse Laws
                </p>
                <h2 class="lx-brand mt-2 text-2xl font-700 lg:text-4xl">Browse Laws &amp; Regulations</h2>
                <p class="mt-2 max-w-2xl text-blue-100/60">Acts currently available in the Ludexora legal database.</p>

                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach (['' => 'All', 'active' => 'Active', 'amended' => 'Amended', 'repealed' => 'Repealed'] as $value => $label)
                        <a href="{{ route('laws.index', array_filter(['status' => $value])) }}"
                           class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors {{ (string) $status === $value ? 'bg-white text-slate-900' : 'bg-white/[.08] text-white/80 hover:bg-white/[.15]' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="min-h-0 flex-1 overflow-y-auto">
            <div class="mx-auto w-full max-w-4xl px-4 py-8 lg:px-6">
                @if ($loadFailed)
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-amber-800">
                        <p class="font-semibold">Unable to load laws right now.</p>
                        <p class="mt-1 text-sm">The legal data service is unreachable. Please try again in a moment.</p>
                    </div>
                @elseif (empty($acts))
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">
                        No acts found for this filter.
                    </div>
                @else
                    <div class="grid gap-4">
                        @foreach ($acts as $act)
                            <a
                                href="{{ route('laws.show', $act['act_id']) }}"
                                class="lx-feature-card block rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm ring-1 ring-slate-100 hover:border-blue-200 hover:shadow-md no-underline"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-900">{{ $act['title'] }}</h3>
                                        <p class="mt-1 text-sm text-slate-500">{{ $act['short_title'] }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-full px-3 py-1 text-xs font-medium capitalize
                                        {{ match ($act['status']) {
                                            'active' => 'bg-green-100 text-green-700',
                                            'amended' => 'bg-amber-100 text-amber-700',
                                            'repealed' => 'bg-red-100 text-red-700',
                                            default => 'bg-slate-100 text-slate-600',
                                        } }}">
                                        {{ $act['status'] }}
                                    </span>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-x-6 gap-y-1 text-sm text-slate-500">
                                    <span>Commenced {{ \Illuminate\Support\Carbon::parse($act['commencement_date'])->format('j M Y') }}</span>
                                    @if ($act['gazette'])
                                        <span>Gazette {{ $act['gazette']['gazette_no'] }} ({{ \Illuminate\Support\Carbon::parse($act['gazette']['gazette_date'])->format('j M Y') }})</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if ($meta && $meta['last_page'] > 1)
                        <nav class="mt-8 flex items-center justify-center gap-2" aria-label="Pagination">
                            @for ($page = 1; $page <= $meta['last_page']; $page++)
                                <a
                                    href="{{ route('laws.index', array_filter(['status' => $status, 'page' => $page])) }}"
                                    class="rounded-lg px-3 py-1.5 text-sm font-medium {{ $page === $meta['current_page'] ? 'text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50' }}" @if($page === $meta['current_page']) style="background:linear-gradient(135deg,#1D4ED8,#3B82F6)" @endif
                                >
                                    {{ $page }}
                                </a>
                            @endfor
                        </nav>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
