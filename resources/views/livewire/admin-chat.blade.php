<div class="space-y-4">
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Chat dengan Customer</h3>

        <!-- Messages Container -->
        <div class="max-h-96 overflow-y-auto space-y-3 mb-4 p-2 border rounded bg-gray-50" id="messages-container">
            @if(count($messages) === 0)
                <div class="text-center text-gray-500 py-4">
                    Belum ada pesan
                </div>
            @else
                @foreach($messages as $msg)
                    <div class="flex {{ $msg['is_admin'] ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $msg['is_admin'] ? 'bg-blue-500 text-white' : 'bg-white border' }}">
                            <div class="text-xs font-medium mb-1">
                                {{ $msg['user']['name'] ?? ($msg['is_admin'] ? 'Admin' : 'Customer') }}
                            </div>
                            <div class="text-sm">
                                {{ $msg['message'] }}
                            </div>
                            <div class="text-xs opacity-75 mt-1">
                                {{ \Carbon\Carbon::parse($msg['created_at'])->format('H:i') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Message Input -->
        <div class="flex gap-2">
            <input
                type="text"
                wire:model="message"
                wire:keydown.enter="sendMessage"
                placeholder="Ketik pesan..."
                class="flex-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <button
                wire:click="sendMessage"
                wire:loading.attr="disabled"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded disabled:opacity-50"
            >
                <span wire:loading.remove>Kirim</span>
                <span wire:loading>Mengirim...</span>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:loaded', () => {
    // Auto scroll to bottom when component loads
    const container = document.getElementById('messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }

    // Listen for Livewire events
    Livewire.on('messageSent', () => {
        setTimeout(() => {
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }, 100);
    });

    Livewire.on('messageReceived', () => {
        setTimeout(() => {
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }, 100);
    });

    // Poll for new messages every 3 seconds
    setInterval(() => {
        $wire.pollMessages();
    }, 3000);
});
</script>
