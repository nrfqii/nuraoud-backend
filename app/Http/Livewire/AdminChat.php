<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ChatMessage;
use App\Models\Order;
use App\Events\AdminMessageSent;
use Illuminate\Support\Facades\Auth;

class AdminChat extends Component
{
    public $orderId;
    public $message = '';
    public $messages = [];

    protected $listeners = [
        'messageReceived' => 'handleMessageReceived',
    ];

    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = ChatMessage::where('order_id', $this->orderId)
            ->with('user')
            ->orderBy('created_at')
            ->get()
            ->toArray();
    }

    public function sendMessage()
    {
        $this->validate([
            'message' => 'required|string|max:2000',
        ]);

        $user = Auth::user();

        $msg = ChatMessage::create([
            'order_id' => $this->orderId,
            'user_id' => $user->id,
            'is_admin' => true,
            'message' => $this->message,
        ]);

        // broadcast to private channel
        broadcast(new AdminMessageSent($msg))->toOthers();

        $this->message = '';
        $this->loadMessages();

        // Emit event to scroll to bottom
        $this->dispatch('messageSent');
    }

    public function handleMessageReceived()
    {
        $this->loadMessages();
        $this->dispatch('messageReceived');
    }

    public function pollMessages()
    {
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.admin-chat');
    }
}
