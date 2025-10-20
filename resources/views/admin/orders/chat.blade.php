@extends('admin.layout')

@section('title', 'Order Chat')

@section('content')
    <div class="space-y-6">
        <h1 class="text-2xl font-bold">Chat Pesanan #{{ $order->id }}</h1>
        <div class="bg-white p-6 rounded shadow">
            <div id="chat" class="space-y-4" data-order-id="{{ $order->id }}">
                <div id="messages" class="max-h-96 overflow-y-auto space-y-2 p-2 border rounded bg-gray-50">
                    @foreach($messages as $message)
                        <div class="p-2 rounded {{ $message->is_admin ? 'bg-blue-50 text-blue-900' : 'bg-gray-50' }}">
                            <div class="text-sm font-medium">{{ $message->user?->name ?? ($message->is_admin ? 'Admin' : 'Buyer') }}</div>
                            <div class="text-sm">{{ $message->message }}</div>
                            <div class="text-xs text-gray-500">{{ $message->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('admin.orders.send-chat', $order) }}" class="mt-4 flex gap-2">
                    @csrf
                    <input name="message" id="messageInput" type="text" class="flex-1 border rounded px-3 py-2" placeholder="Tulis pesan..." required />
                    <button id="sendBtn" type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Kirim</button>
                </form>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    const chatEl = document.getElementById('chat');
    const orderId = chatEl.dataset.orderId;
    const messagesEl = document.getElementById('messages');
    const inputEl = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');

    let lastMessageId = 0;
    const renderedIds = new Set();

    async function loadMessages() {
        try {
            const res = await fetch(`/admin/orders/${orderId}/chat/messages`, {
                headers: { 'Accept': 'application/json' }
            });
            if (res.ok) {
                const data = await res.json();
                const messages = data.messages || [];
                renderNewMessages(messages);
            }
        } catch (e) {
            console.error('Failed to load messages', e);
        }
    }

    function renderMessage(m) {
        if (m.id && renderedIds.has(m.id)) return;
        if (m.id) renderedIds.add(m.id);

        const div = document.createElement('div');
        div.className = 'p-2 rounded ' + (m.is_admin ? 'bg-blue-50 text-blue-900' : 'bg-gray-50');
        div.innerHTML = `<div class="text-sm font-medium">${m.user?.name ?? (m.is_admin ? 'Admin' : 'Buyer')}</div><div class="text-sm">${m.message}</div><div class="text-xs text-gray-500">${new Date(m.created_at).toLocaleString()}</div>`;
        messagesEl.appendChild(div);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function renderNewMessages(msgs) {
        let hasNewMessages = false;
        msgs.forEach(m => {
            if (!renderedIds.has(m.id)) {
                renderMessage(m);
                hasNewMessages = true;
            }
        });

        // If no new messages and we have messages, update lastMessageId
        if (!hasNewMessages && msgs.length > 0) {
            lastMessageId = Math.max(lastMessageId, ...msgs.map(m => m.id));
        }
    }

    // Handle form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const text = inputEl.value.trim();
        if (!text) return;

        sendBtn.disabled = true;
        sendBtn.textContent = 'Mengirim...';

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        try {
            const formData = new FormData();
            formData.append('message', text);
            formData.append('_token', token);

            const res = await fetch(`/admin/orders/${orderId}/chat`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();

            if (data.message) {
                // Append new message immediately
                renderMessage(data.message);
            } else {
                // fallback to reload
                await loadMessages();
            }
            inputEl.value = '';
        } catch (e) {
            console.error('Failed to send message', e);
        } finally {
            sendBtn.disabled = false;
            sendBtn.textContent = 'Kirim';
        }
    });

    // Load initial messages
    loadMessages();

    // Poll for new messages every 2 seconds
    setInterval(loadMessages, 2000);

    // Realtime subscription via Echo (if available)
    if (window.Echo) {
        try {
            const channel = window.Echo.private('order.' + orderId);
            channel.listen('CustomerMessageSent', (e) => {
                console.log('New customer message received');
                loadMessages();
            });
            channel.listen('AdminMessageSent', (e) => {
                console.log('New admin message received');
                loadMessages();
            });
        } catch (err) {
            console.warn('Echo subscribe failed, using polling instead', err);
        }
    }
</script>
@endpush
@endsection
