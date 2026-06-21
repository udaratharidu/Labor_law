<button {{ $attributes->merge(['type' => 'submit', 'class' => 'lx-btn-primary inline-flex w-full items-center justify-center rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-md focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60']) }}>
    {{ $slot }}
</button>
