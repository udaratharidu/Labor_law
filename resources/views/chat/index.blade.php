@extends('layouts.main')

@section('content')
    @include('chat.chat')
@endsection

@push('scripts')
<script>
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

                this.loading = true;
                this.errorMessage = '';
                this.scrollToBottom();

                const formData = new FormData();
                formData.append('message', message);
                formData.append('session_id', this.currentSessionId);

                try {
                    const response = await fetch(storeUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        throw new Error('Unable to get response.');
                    }

                    const data = await response.json();

                    // keep track of the session for subsequent messages
                    if (data.session_id) {
                        this.currentSessionId = data.session_id;
                    }

                    this.chats.push({
                        user_message: data.user_message,
                        ai_response: data.ai_response,
                    });
                    this.draftMessage = '';
                    if (this.$refs.messageInput) {
                        this.$refs.messageInput.style.height = 'auto';
                    }
                } catch (error) {
                    this.errorMessage = 'We could not fetch a legal answer. Please try again.';
                } finally {
                    this.loading = false;
                    this.scrollToBottom();
                }
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
