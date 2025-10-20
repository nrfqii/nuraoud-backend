@extends('admin.layout')

@section('title', 'Chat Management')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Chat Management</h1>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Customer Conversations</h2>
            <p class="text-gray-600 mt-1">Manage chat conversations with customers</p>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($ordersWithChats as $order)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-medium text-sm">
                                            {{ substr($order->user->name, 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-lg font-medium text-gray-900">Order #{{ $order->id }}</h3>
                                        @if(isset($order->unread_by_admin_count) && $order->unread_by_admin_count > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded bg-red-600 text-white">New</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600">Customer: {{ $order->user->name }}</p>
                                    <p class="text-sm text-gray-500">Last message: {{ optional($order->chatMessages->first())->created_at ? $order->chatMessages->first()->created_at->diffForHumans() : '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </p>
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                    @if($order->status === 'waiting') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>

                            <a href="{{ route('admin.orders.chat', $order) }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                View Chat
                            </a>
                        </div>
                    </div>

                    @if($order->chatMessages->first())
                        @php
                            $last = $order->chatMessages->first();
                            $senderName = $last->is_admin ? 'Admin' : $order->user->name;
                        @endphp
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-700">
                                <span class="font-medium">{{ $senderName }}:</span>
                                {{ Str::limit($last->message, 100) }}
                            </p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="w-24 h-24 mx-auto mb-4 text-gray-400">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Chat Conversations</h3>
                    <p class="text-gray-600">There are no active chat conversations with customers at the moment.</p>
                </div>
            @endforelse
        </div>

        @if($ordersWithChats->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $ordersWithChats->links() }}
            </div>
        @endif
    </div>
</div>
@endsection