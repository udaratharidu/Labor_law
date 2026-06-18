@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-base text-slate-900 placeholder:text-slate-400 shadow-sm transition-colors focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-500']) }}>
