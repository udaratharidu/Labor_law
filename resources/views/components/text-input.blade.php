@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'lx-input w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition-all disabled:cursor-not-allowed disabled:opacity-60']) }}>
