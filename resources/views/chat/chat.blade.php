{{-- Modern AI assistant layout (Gemini / Claude style). Included from chat/index. --}}
<div
    class="absolute inset-0 flex min-h-0 flex-col overflow-hidden bg-slate-50"
    x-data="chatInterface({
        initialChats: @js($chats->map(fn ($chat) => ['message' => $chat->message, 'response' => $chat->response])->values()),
        storeUrl: '{{ route('chat.store') }}',
        csrfToken: '{{ csrf_token() }}',
    })"
>
    <header class="flex shrink-0 items-center justify-end border-b border-slate-200/80 bg-slate-50/90 px-4 py-3 backdrop-blur-sm lg:px-6">
        <div class="flex items-center">
            @auth
                <form method="POST" action="{{ route('auth.logout') }}" class="flex items-center">
                    @csrf
                    <button type="submit" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">
                        Log out
                    </button>
                </form>
            @else
                <a
                    href="{{ route('auth.email') }}"
                    class="inline-flex items-center rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                >
                    Log in
                </a>
            @endauth
        </div>
    </header>

    <div class="relative z-0 min-h-0 flex-1 overflow-y-auto overscroll-contain chat-scroll" x-ref="chatWindow">
        <div class="mx-auto min-h-full w-full max-w-3xl px-4 pb-8 pt-10 lg:px-6">
            <div
                x-show="isNewChat"
                x-transition.opacity.duration.200ms
                class="flex min-h-[40vh] flex-col items-center justify-center px-2 py-12 text-center"
            >
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 text-white shadow-md">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">How can I help?</h1>
                <p class="mt-2 max-w-md text-[15px] leading-relaxed text-slate-500">
                    @auth
                        Ask about consumer protection. Informational only — not legal advice.
                    @else
                        Ask a question, or log in to keep chat history.
                    @endauth
                </p>
            </div>

            <div class="flex flex-col gap-10" x-show="chats.length > 0 || loading">
                <template x-for="(chat, index) in chats" :key="index">
                    <div class="flex flex-col gap-6">
                        <div class="flex justify-end">
                            <div
                                class="max-w-[min(90%,28rem)] rounded-[1.25rem] bg-slate-200/90 px-4 py-3 text-[15px] leading-relaxed text-slate-900 shadow-sm"
                                x-text="chat.message"
                            ></div>
                        </div>
                        <div class="flex gap-3">
                            <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm ring-2 ring-white">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1 pt-0.5 text-[15px] leading-relaxed text-slate-800">
                                <span class="sr-only">Assistant</span>
                                <span class="whitespace-pre-wrap break-words" x-text="chat.response"></span>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-cloak x-show="loading" class="flex gap-3">
                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm ring-2 ring-white">
                        <svg class="h-4 w-4 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1 text-[15px] text-slate-700">
                        <span class="font-medium text-slate-800">Thinking</span>
                        <span class="thinking-dots ml-1 text-blue-600"><span></span><span></span><span></span></span>
                        <p class="mt-1 text-sm text-slate-500">Searching sources…</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="relative z-20 shrink-0">
        <div
            class="pointer-events-none absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-white via-white/95 to-transparent"
            aria-hidden="true"
        ></div>
        <div class="relative border-t border-slate-200/60 bg-gradient-to-b from-white/80 to-white px-4 pb-[max(1rem,env(safe-area-inset-bottom,0px))] pt-1 lg:px-6 lg:pb-[max(1.25rem,env(safe-area-inset-bottom,0px))]">
            <p x-cloak x-show="errorMessage" x-text="errorMessage" class="mx-auto mb-3 max-w-3xl rounded-xl bg-red-50 px-4 py-2 text-center text-sm text-red-700 ring-1 ring-red-100"></p>

            <form method="POST" action="{{ route('chat.store') }}" class="relative mx-auto max-w-3xl" @submit.prevent="submitMessage">
                @csrf
                <div class="relative rounded-[28px] border border-slate-200/90 bg-white py-2 pl-4 pr-2 shadow-[0_8px_30px_-12px_rgba(15,23,42,0.12)] transition-shadow focus-within:border-slate-300 focus-within:shadow-[0_12px_40px_-16px_rgba(15,23,42,0.18)]">
                    <textarea
                        name="message"
                        rows="1"
                        class="chat-composer-textarea max-h-48 min-h-[48px] w-full resize-none bg-transparent py-3 pl-1 pr-14 text-[15px] leading-snug text-slate-900 placeholder:text-slate-400"
                        placeholder="Message Legal Assistant…"
                        required
                        x-model="draftMessage"
                        x-ref="messageInput"
                        @input="autoGrow()"
                        @keydown.enter.prevent="if (!$event.shiftKey) { submitMessage(); }"
                    ></textarea>
                    <button
                        type="submit"
                        class="absolute bottom-2.5 right-2.5 flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-white shadow-md transition hover:bg-blue-700 disabled:pointer-events-none disabled:opacity-35"
                        :disabled="loading || !draftMessage.trim()"
                        aria-label="Send message"
                    >
                        <svg class="h-5 w-5 -translate-y-px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l-6-6m6 6l6-6" />
                        </svg>
                    </button>
                </div>
                <p class="mt-2 text-center text-xs text-slate-500">
                    Consumer protection guidance only — verify critical matters with a qualified attorney.
                </p>
            </form>
        </div>
    </div>
</div>
