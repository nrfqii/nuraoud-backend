@extends('admin.layout')

@section('title', 'Livewire Chat Test')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold">Livewire Chat Test - Order #{{ $order->id }}</h1>

    @livewire('admin-chat', ['orderId' => $order->id])
</div>
@endsection