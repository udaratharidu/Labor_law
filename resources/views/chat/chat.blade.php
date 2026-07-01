{{-- Ludexora Legal Assistant --}}
<div class="absolute inset-0 flex min-h-0 flex-col overflow-hidden bg-white"
     x-data="chatInterface({
         initialChats: @js($chats->map(fn($c)=>['user_message'=>$c->user_message,'ai_response'=>$c->ai_response])->values()),
         storeUrl: '{{ route('chat.store') }}',
         csrfToken: '{{ csrf_token() }}',
         initialSessionId: '{{ $session?->session_id ?? '' }}',
     })">

    {{-- ── Messages ─────────────────────────────────────────────── --}}
    <div class="relative z-0 min-h-0 flex-1 overflow-y-auto overscroll-contain chat-scroll" x-ref="chatWindow">
        <div class="mx-auto min-h-full w-full max-w-2xl px-4 pb-10 pt-8 lg:px-6">

            {{-- Empty / welcome state --}}
            <div x-show="isNewChat" x-transition.opacity.duration.150ms
                 class="flex min-h-[55vh] flex-col items-center justify-center text-center">
                <div class="mb-4 lx-logo-spin" style="animation-duration:8s;">
                    <img src="/images/ludexora-logo.svg" alt="Ludexora" class="h-14 w-14 object-contain"
                         style="filter:drop-shadow(0 0 18px rgba(96,165,250,.65)) drop-shadow(0 0 8px rgba(34,211,238,.3));">
                </div>
                <h2 class="lx-brand text-xl font-semibold text-slate-900">How can I help?</h2>
                <p class="mt-1.5 max-w-xs text-[13px] leading-relaxed text-slate-400">
                    Ask about consumer rights, regulations, or legal terms — I'll explain clearly.
                </p>

                {{-- Suggested prompts --}}
                <div class="mt-6 grid w-full max-w-md gap-2 sm:grid-cols-2">
                    @foreach([
                        ['q'=>'Breach of Implied Warranty for a Specific Purpose',   'i'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                        ['q'=>'Unapproved Price Increases on Essential Goods',        'i'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
                        ['q'=>'Can a seller refuse a refund?',                        'i'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                        ['q'=>'a seller Lying About the Quality of a Product?',       'i'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                    ] as $p)
                    <button type="button"
                            class="flex items-start gap-2.5 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 text-left text-[12px] text-slate-600 transition-all hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                            @click="draftMessage = @js($p['q']); $nextTick(()=>{ autoGrow(); $refs.messageInput.focus(); })">
                        <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-blue-500/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $p['i'] }}"/>
                        </svg>
                        {{ $p['q'] }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Conversation --}}
            <div class="flex flex-col gap-7" x-show="chats.length > 0 || loading">
                <template x-for="(chat, i) in chats" :key="i">
                    <div class="flex flex-col gap-4">
                        {{-- User bubble --}}
                        <div class="flex justify-end">
                            <div class="max-w-[80%] rounded-2xl rounded-tr-sm px-4 py-2.5 text-[14px] leading-relaxed text-white"
                                 style="background:linear-gradient(135deg,#1E40AF,#3B82F6);"
                                 x-text="chat.user_message"></div>
                        </div>
                        {{-- AI response --}}
                        <div class="flex gap-2.5" x-show="chat.ai_response || chat.typing">
                            <div class="mt-1 flex h-7 w-7 shrink-0 overflow-hidden rounded-full"
                                 style="background:#050D1A;">
                                <img src="/images/ludexora-logo.svg" alt="" class="h-7 w-7 object-contain p-0.5">
                            </div>
                            <div class="lx-prose min-w-0 flex-1 rounded-2xl rounded-tl-sm border border-slate-100 bg-slate-50/80 px-4 py-3 text-[14px] leading-relaxed text-slate-800"
                                 x-html="renderResponse(chat)"></div>
                        </div>
                    </div>
                </template>

                {{-- Typing indicator --}}
                <div x-cloak x-show="loading" class="flex gap-2.5">
                    <div class="mt-1 flex h-7 w-7 shrink-0 overflow-hidden rounded-full" style="background:#050D1A;">
                        <img src="/images/ludexora-logo.svg" alt="" class="h-7 w-7 object-contain p-0.5">
                    </div>
                    <div class="rounded-2xl rounded-tl-sm border border-slate-100 bg-slate-50/80 px-4 py-3 text-[14px] text-slate-500">
                        <span class="thinking-dots text-blue-400"><span></span><span></span><span></span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Composer ──────────────────────────────────────────────── --}}
    <div class="relative shrink-0 border-t border-slate-100 bg-white px-4 pb-[max(.875rem,env(safe-area-inset-bottom))] pt-3 lg:px-6">
        <p x-cloak x-show="errorMessage" x-text="errorMessage"
           class="mx-auto mb-2.5 max-w-2xl rounded-xl bg-red-50 px-4 py-2 text-center text-[13px] text-red-700"></p>

        <form class="mx-auto max-w-2xl" @submit.prevent="submitMessage">
            @csrf
            <div class="flex items-end gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 shadow-sm transition-all focus-within:border-blue-300 focus-within:bg-white focus-within:shadow-[0_4px_20px_-6px_rgba(59,130,246,.2)]">
                <textarea
                    name="message" rows="1"
                    class="chat-composer-textarea max-h-40 min-h-[40px] flex-1 resize-none bg-transparent py-2 pr-2 text-[14px] text-slate-900 placeholder:text-slate-400"
                    placeholder="Ask about consumer protection…"
                    required
                    x-model="draftMessage" x-ref="messageInput"
                    @input="autoGrow()"
                    @keydown.enter.prevent="if(!$event.shiftKey) submitMessage();"
                ></textarea>
                <button type="submit"
                        class="mb-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-white transition-all disabled:opacity-35"
                        style="background:linear-gradient(135deg,#1D4ED8,#3B82F6);"
                        :disabled="loading || !draftMessage.trim()"
                        aria-label="Send">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
            <p class="mt-1.5 text-center text-[11px] text-slate-400">
                For information only — not legal advice. Shift+Enter for new line.
            </p>
        </form>
    </div>
</div>
