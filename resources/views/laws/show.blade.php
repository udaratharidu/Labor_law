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
                    <a href="{{ route('laws.index') }}" class="hover:text-blue-200 transition-colors">Browse Laws</a>
                    @if (! $loadFailed)
                        <span class="px-1 text-blue-400/40">/</span>
                        <span class="text-blue-200/80">{{ \Illuminate\Support\Str::limit($act['title'], 40) }}</span>
                    @endif
                </p>
                @if (! $loadFailed)
                    <h2 class="lx-brand mt-2 text-2xl font-700 lg:text-4xl">{{ $act['title'] }}</h2>
                    <span class="mt-3 inline-block rounded-full bg-white/[.08] px-3 py-1 text-xs font-medium capitalize text-white/80 border border-white/10">
                        {{ $act['status'] }}
                    </span>
                @else
                    <h2 class="lx-brand mt-2 text-2xl font-700 lg:text-4xl">Act not found</h2>
                @endif
            </div>
        </section>

        <div class="min-h-0 flex-1 overflow-y-auto">
            <div class="mx-auto w-full max-w-4xl px-4 py-8 lg:px-6">
                @if ($loadFailed)
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-amber-800">
                        <p class="font-semibold">Unable to load this act right now.</p>
                        <p class="mt-1 text-sm">It may not exist, or the legal data service is unreachable.</p>
                        <a href="{{ route('laws.index') }}" class="mt-3 inline-block text-sm font-semibold text-blue-700 hover:underline">&larr; Back to Browse Laws</a>
                    </div>
                @elseif (empty($act['nodes']))
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">
                        This act has no sections yet.
                    </div>
                @else
                    @php
                        $nodes = $act['nodes'];
                    @endphp
                    <div
                        x-data="{ activeTab: 0 }"
                        class="flex flex-col gap-0"
                    >
                        {{-- Tab bar --}}
                        <div class="flex overflow-x-auto rounded-t-2xl border border-b-0 border-slate-200/80 bg-white">
                            @foreach ($nodes as $i => $node)
                                @php
                                    $tabLabel = trim(($node['node_type'] ?? '') . ' ' . ($node['node_no'] ?? ''));
                                    if (empty($tabLabel)) { $tabLabel = $node['heading'] ?? ('Part ' . ($i + 1)); }
                                @endphp
                                <button
                                    type="button"
                                    @click="activeTab = {{ $i }}"
                                    :class="activeTab === {{ $i }}
                                        ? 'border-b-2 border-blue-600 text-blue-700 bg-blue-50/60'
                                        : 'border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                                    class="shrink-0 whitespace-nowrap px-4 py-3 text-sm font-medium transition-colors focus:outline-none"
                                >
                                    {{ $tabLabel }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Tab panels --}}
                        @foreach ($nodes as $i => $node)
                            <div
                                x-show="activeTab === {{ $i }}"
                                x-transition:enter="transition-opacity duration-150"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                class="rounded-b-2xl border border-slate-200/80 bg-white p-5"
                            >
                                {{-- Panel heading --}}
                                @if (! empty($node['heading']))
                                    <h3 class="mb-4 text-base font-semibold text-slate-800">
                                        {{ trim(($node['node_type'] ?? '') . ' ' . ($node['node_no'] ?? '')) }}
                                        <span class="font-normal text-slate-600">— {{ $node['heading'] }}</span>
                                    </h3>
                                @endif

                                @if (! empty($node['text_content']))
                                    <p class="mb-4 whitespace-pre-wrap text-[15px] leading-relaxed text-slate-700">{{ $node['text_content'] }}</p>
                                @endif

                                {{-- Children --}}
                                @if (! empty($node['children']))
                                    <div class="space-y-2">
                                        @foreach ($node['children'] as $child)
                                            @include('laws._node', ['node' => $child])
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
