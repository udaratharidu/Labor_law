@php
    $label = trim(($node['node_type'] ?? '').' '.($node['node_no'] ?? ''));
    $hasChildren = ! empty($node['children']);
@endphp

@if ($hasChildren)
    <details {{ ($node['level'] ?? 1) === 1 ? 'open' : '' }} class="group rounded-lg border border-slate-200/80 bg-white">
        <summary class="flex cursor-pointer select-none items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50">
            <svg class="h-3.5 w-3.5 shrink-0 text-slate-400 transition-transform group-open:rotate-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
            <span>{{ $label }}</span>
            @if (! empty($node['heading']))
                <span class="font-normal text-slate-600">— {{ $node['heading'] }}</span>
            @endif
        </summary>

        <div class="space-y-2 border-t border-slate-100 p-3 pl-6">
            @if (! empty($node['text_content']))
                <p class="whitespace-pre-wrap text-[15px] leading-relaxed text-slate-700">{{ $node['text_content'] }}</p>
            @endif

            @foreach ($node['children'] as $child)
                @include('laws._node', ['node' => $child])
            @endforeach
        </div>
    </details>
@else
    <div class="rounded-lg px-3 py-2">
        <p class="text-sm font-semibold text-slate-800">
            {{ $label }}
            @if (! empty($node['heading']))
                <span class="font-normal text-slate-600">— {{ $node['heading'] }}</span>
            @endif
        </p>
        @if (! empty($node['text_content']))
            <p class="mt-1 whitespace-pre-wrap text-[15px] leading-relaxed text-slate-700">{{ $node['text_content'] }}</p>
        @endif
    </div>
@endif
