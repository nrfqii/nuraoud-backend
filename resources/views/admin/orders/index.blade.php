@extends('admin.layout')

@section('title', 'Orders Management')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Orders Management</h1>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">All Orders</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50" data-order-id="{{ $order->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">#{{ $order->id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-2">
                                @foreach($order->orderItems as $item)
                                    <div class="flex items-center space-x-3 py-2">
                                        @if($item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}"
                                                 alt="{{ $item->product->name }}"
                                                 class="w-10 h-10 object-cover rounded border">
                                        @else
                                            <div class="w-10 h-10 bg-gray-200 rounded border flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                            <div class="text-sm text-gray-500">Qty: {{ $item->quantity }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($order->status)
                                @case('waiting')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Menunggu Pembayaran
                                    </span>
                                    @break
                                @case('processing')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Dikemas
                                    </span>
                                    @break
                                @case('shipped')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Dikirim
                                    </span>
                                    @break
                                @case('delivered')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Selesai
                                    </span>
                                    @break
                                @case('cancelled')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Dibatalkan
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.orders.chat', $order) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                                    Reply
                                </a>
                                
                                <button onclick="showOrderDetails('{{ $order->id }}')"
                                        class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                @if($order->status !== 'delivered')
                                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()"
                                                class="text-xs border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500">
                                            <option value="waiting" {{ $order->status == 'waiting' ? 'selected' : '' }}>Waiting</option>
                                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-500 italic">Status final</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Order Details Modal -->
<div id="orderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Order Details</h3>
            <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="orderDetailsContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
function showOrderDetails(orderId) {
    // For now, just show a placeholder. In a real app, you'd fetch order details via AJAX
    const modal = document.getElementById('orderModal');
    const content = document.getElementById('orderDetailsContent');

    content.innerHTML = '<div class="text-center py-8">' +
        '<p class="text-gray-600">Order details for #' + orderId + '</p>' +
        '<p class="text-sm text-gray-500 mt-2">This feature would load detailed order information including items, shipping address, etc.</p>' +
        '</div>';

    modal.classList.remove('hidden');
}

function closeOrderModal() {
    document.getElementById('orderModal').classList.add('hidden');
}

// Real-time order status updates
function refreshOrderStatuses() {
    fetch('/admin/orders/status-updates', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.orders) {
            data.orders.forEach(order => {
                updateOrderStatus(order.id, order.status);
            });
        }
    })
    .catch(error => {
        console.log('Status update check failed, will retry...');
    });
}

function updateOrderStatus(orderId, newStatus) {
    const statusCell = document.querySelector(`tr[data-order-id="${orderId}"] td:nth-child(5)`);
    if (statusCell) {
        let statusClass = '';
        let statusText = '';

        switch(newStatus) {
            case 'waiting':
                statusClass = 'bg-yellow-100 text-yellow-800';
                statusText = 'Menunggu Pembayaran';
                break;
            case 'processing':
                statusClass = 'bg-blue-100 text-blue-800';
                statusText = 'Dikemas';
                break;
            case 'shipped':
                statusClass = 'bg-purple-100 text-purple-800';
                statusText = 'Dikirim';
                break;
            case 'delivered':
                statusClass = 'bg-green-100 text-green-800';
                statusText = 'Selesai';
                break;
            case 'cancelled':
                statusClass = 'bg-red-100 text-red-800';
                statusText = 'Dibatalkan';
                break;
        }

        statusCell.innerHTML = `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">${statusText}</span>`;
    }
}

// Check for status updates every 10 seconds
document.addEventListener('DOMContentLoaded', function() {
    setInterval(refreshOrderStatuses, 10000);
});
</script>
@endsection
