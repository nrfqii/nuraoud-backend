@extends('admin.layout')

@section('title', 'Edit Voucher')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Edit Voucher</h1>
        <a href="{{ route('admin.vouchers.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Vouchers
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Voucher Information</h3>
        </div>

        <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Voucher Code *</label>
                    <input type="text" name="code" id="code" value="{{ old('code', $voucher->code) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Voucher Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $voucher->name) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $voucher->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Discount Type *</label>
                    <select name="type" id="type"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            required>
                        <option value="">Select Type</option>
                        <option value="percentage" {{ old('type', $voucher->type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('type', $voucher->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount (Rp)</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700">Discount Value *</label>
                    <input type="number" name="value" id="value" value="{{ old('value', $voucher->value) }}" step="0.01" min="0"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="min_order_amount" class="block text-sm font-medium text-gray-700">Minimum Order Amount</label>
                    <input type="number" name="min_order_amount" id="min_order_amount" value="{{ old('min_order_amount', $voucher->min_order_amount) }}" step="0.01" min="0"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('min_order_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_discount" class="block text-sm font-medium text-gray-700">Maximum Discount</label>
                    <input type="number" name="max_discount" id="max_discount" value="{{ old('max_discount', $voucher->max_discount) }}" step="0.01" min="0"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('max_discount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="usage_limit" class="block text-sm font-medium text-gray-700">Usage Limit</label>
                    <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit', $voucher->usage_limit) }}" min="1"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('usage_limit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="starts_at" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at', $voucher->starts_at ? $voucher->starts_at->format('Y-m-d\TH:i') : '') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('starts_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="expires_at" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                    <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at', $voucher->expires_at ? $voucher->expires_at->format('Y-m-d\TH:i') : '') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('expires_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Active
                        </label>
                    </div>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.vouchers.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                    Update Voucher
                </button>
            </div>
        </form>
    </div>
</div>
@endsection