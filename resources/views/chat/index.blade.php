@extends('layouts.main')

@section('content')
    @include('chat.chat')
@endsection

@push('scripts')
<script>
    if (typeof marked !== 'undefined') {
        marked.setOptions({ breaks: true });
    }

    function chatInterface({ initialChats, storeUrl, csrfToken, initialSessionId }) {
        return {
            chats: initialChats ?? [],
            draftMessage: '',
            loading: false,
            errorMessage: '',
            currentSessionId: initialSessionId ?? '',

            init() {
                this.scrollToBottom();
                this.$watch('draftMessage', () => this.autoGrow());
            },

            get isNewChat() {
                return this.chats.length === 0 && !this.loading;
            },

            autoGrow() {
                this.$nextTick(() => {
                    const el = this.$refs.messageInput;
                    if (!el) return;
                    el.style.height = 'auto';
                    el.style.height = `${Math.min(el.scrollHeight, 192)}px`;
                });
            },

            async submitMessage() {
                const message = this.draftMessage.trim();
                if (!message || this.loading) return;

                // Optimistic: show user message immediately
                const idx = this.chats.length;
                this.chats.push({ user_message: message, ai_response: '', typing: false });
                this.draftMessage = '';
                if (this.$refs.messageInput) this.$refs.messageInput.style.height = 'auto';
                this.loading = true;
                this.errorMessage = '';
                this.scrollToBottom();

                const formData = new FormData();
                formData.append('message', message);
                formData.append('session_id', this.currentSessionId);

                try {
                    const res = await fetch(storeUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    });

                    if (!res.ok) throw new Error('Network error');
                    const data = await res.json();

                    if (data.session_id) this.currentSessionId = data.session_id;

                    this.loading = false;

                    // Animate reply character by character
                    this.chats[idx].typing = true;
                    await this.typeText(idx, data.ai_response ?? '');
                    this.chats[idx].typing = false;

                } catch (e) {
                    this.loading = false;
                    this.chats.splice(idx, 1);
                    this.errorMessage = 'Could not get a response. Please try again.';
                }
            },

            async typeText(idx, fullText) {
                if (!fullText) return;
                const len = fullText.length;
                // Aim for ~2 s total animation regardless of response length
                const charsPerFrame = Math.max(1, Math.ceil(len / 120));
                let pos = 0;
                return new Promise(resolve => {
                    const tick = () => {
                        pos = Math.min(pos + charsPerFrame, len);
                        this.chats[idx].ai_response = fullText.slice(0, pos);
                        this.scrollToBottom();
                        if (pos < len) {
                            requestAnimationFrame(tick);
                        } else {
                            resolve();
                        }
                    };
                    requestAnimationFrame(tick);
                });
            },

            renderResponse(chat) {
                if (!chat.ai_response && !chat.typing) return '';
                if (chat.typing) {
                    // Raw escaped text + blinking cursor while animating
                    const escaped = chat.ai_response
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;');
                    return `<span style="white-space:pre-wrap">${escaped}</span><span class="lx-typing-cursor"></span>`;
                }
                // Full markdown render once animation is done
                if (typeof marked !== 'undefined') return marked.parse(chat.ai_response);
                return chat.ai_response
                    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                    .replace(/\n/g, '<br>');
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    if (this.$refs.chatWindow) {
                        this.$refs.chatWindow.scrollTop = this.$refs.chatWindow.scrollHeight;
                    }
                });
            },
        };
    }
</script>
@endpush
