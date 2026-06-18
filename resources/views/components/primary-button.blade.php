<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex w-full items-center justify-center rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 active:bg-blue-900 disabled:cursor-not-allowed disabled:opacity-60']) }}>
    {{ $slot }}
</button>
