@extends('layouts.main')

@section('content')
<div class="flex min-h-0 flex-1 flex-col overflow-y-auto">

    {{-- ── Hero ──────────────────────────────────────────────────────── --}}
    <section class="relative shrink-0 overflow-hidden"
             style="background:linear-gradient(150deg,#050D1A 0%,#0B1F40 55%,#0D2A5A 100%);">
        {{-- Single subtle glow, not competing orbs --}}
        <div class="pointer-events-none absolute right-0 top-0 h-full w-1/2 opacity-20"
             style="background:radial-gradient(ellipse at 80% 40%, #3B82F6 0%, transparent 65%);"></div>

        <div class="relative px-6 py-10 lg:px-10 lg:py-14">
            <h1 class="lx-brand max-w-2xl text-[28px] font-semibold leading-snug text-white lg:text-[38px]">
                Understand your consumer rights.<br>
                <span style="background:linear-gradient(90deg,#60A5FA,#34D399);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;color:transparent;">
                    Get clear legal guidance instantly.
                </span>
            </h1>
            <p class="mt-3 text-[15px] leading-relaxed text-blue-100/55"
               x-data="typewriter()" x-init="start()">
                <span x-text="display"></span><span
                    class="inline-block w-[2px] h-[1em] align-middle ml-[1px] rounded-sm"
                    style="background:rgba(96,165,250,.8);"
                    :style="cursorVisible ? 'opacity:1' : 'opacity:0'"
                ></span>
            </p>

            @push('scripts')
            <script>
            function typewriter() {
                return {
                    phrases: [
                        'Ask the AI assistant, browse acts and regulations, or explore legal topics — all in one place.',
                        'Get instant answers to consumer protection questions.',
                        'Browse hundreds of acts and regulatory frameworks.',
                        'Understand your legal rights in plain language.',
                    ],
                    display: '',
                    cursorVisible: true,
                    phraseIndex: 0,
                    charIndex: 0,
                    deleting: false,
                    typeSpeed: 38,
                    deleteSpeed: 18,
                    pauseAfterType: 1800,
                    pauseAfterDelete: 120,
                    _timer: null,
                    _cursorTimer: null,
                    start() {
                        this._cursorTimer = setInterval(() => {
                            this.cursorVisible = !this.cursorVisible;
                        }, 530);
                        this._tick();
                    },
                    _tick() {
                        const phrase = this.phrases[this.phraseIndex];
                        if (!this.deleting) {
                            // Typing forward
                            if (this.charIndex < phrase.length) {
                                this.display = phrase.slice(0, ++this.charIndex);
                                this._timer = setTimeout(() => this._tick(), this.typeSpeed);
                            } else {
                                // Finished typing — pause then start deleting
                                this._timer = setTimeout(() => {
                                    this.deleting = true;
                                    this._tick();
                                }, this.pauseAfterType);
                            }
                        } else {
                            // Deleting backward (fast)
                            if (this.charIndex > 0) {
                                this.display = phrase.slice(0, --this.charIndex);
                                this._timer = setTimeout(() => this._tick(), this.deleteSpeed);
                            } else {
                                // Finished deleting — next phrase, instant start
                                this.deleting = false;
                                this.phraseIndex = (this.phraseIndex + 1) % this.phrases.length;
                                this._timer = setTimeout(() => this._tick(), this.pauseAfterDelete);
                            }
                        }
                    },
                };
            }
            </script>
            @endpush
        </div>
    </section>

    {{-- ── Features ───────────────────────────────────────────────────── --}}
    <section class="flex-1 px-6 py-8 lg:px-10">
        <p class="mb-5 text-xs font-semibold uppercase tracking-widest text-slate-400">What you can do</p>

        <div class="grid gap-4 sm:grid-cols-2">

            {{-- AI Assistant --}}
            <a href="{{ route('chat.new') }}"
               class="lx-feature-card group flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm no-underline hover:border-blue-200">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl"
                     style="background:linear-gradient(135deg,#1D4ED8,#3B82F6);">
                    <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-900 group-hover:text-blue-700 transition-colors">AI Legal Assistant</h3>
                <p class="mt-1.5 flex-1 text-[13px] leading-relaxed text-slate-500">
                    Ask any consumer protection question in plain language and get a clear, informed answer instantly.
                </p>
                <span class="mt-4 flex items-center gap-1 text-[13px] font-semibold text-blue-600">
                    Start a conversation
                    <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>

            {{-- Law Browser --}}
            <a href="{{ route('laws.index') }}"
               class="lx-feature-card group flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm no-underline hover:border-cyan-200">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl"
                     style="background:linear-gradient(135deg,#0E7490,#06B6D4);">
                    <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-900 group-hover:text-cyan-700 transition-colors">Law & Regulation Browser</h3>
                <p class="mt-1.5 flex-1 text-[13px] leading-relaxed text-slate-500">
                    Browse the full text of consumer protection acts, regulations, and gazette notices by status or topic.
                </p>
                <span class="mt-4 flex items-center gap-1 text-[13px] font-semibold text-cyan-600">
                    Browse laws
                    <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>

        </div>

        {{-- Disclaimer --}}
        <p class="mt-8 text-[12px] text-slate-400">
            Ludexora provides legal information for educational purposes only — not a substitute for advice from a qualified attorney.
        </p>
    </section>
</div>
@endsection
