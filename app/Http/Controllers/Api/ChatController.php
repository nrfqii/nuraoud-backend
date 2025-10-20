<?php

namespace App\Http\Controllers\Api;

use App\Events\CustomerMessageSent;
use App\Events\AdminMessageSent;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Order $order)
    {
        // Only allow order owner or admin
        $user = Auth::user();
        if ($order->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = ChatMessage::where('order_id', $order->id)->with('user')->orderBy('created_at')->get();

        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request, Order $order)
    {
        // Only allow order owner or admin
        $user = Auth::user();
        if ($order->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $user = Auth::user();

        $isAdmin = $user->role === 'admin';

        $message = ChatMessage::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'is_admin' => $isAdmin,
            'message' => $request->input('message'),
        ]);

        // TODO: Enable broadcasting when queue system is properly configured
        // For now, skip broadcasting to avoid queue issues
        // if ($isAdmin) {
        //     broadcast(new AdminMessageSent($message))->toOthers();
        // } else {
        //     broadcast(new CustomerMessageSent($message))->toOthers();
        // }

        return response()->json(['message' => $message]);
    }
}
