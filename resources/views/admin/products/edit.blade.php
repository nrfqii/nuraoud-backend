@extends('admin.layout')

@section('title', 'Edit Product')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Edit Product</h1>
        <a href="{{ route('admin.products') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Products
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price (Rp)</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-500 @enderror"
                           required>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" id="category"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('category') border-red-500 @enderror"
                            required>
                        <option value="">Select Category</option>
                        <option value="Oud" {{ old('category', $product->category) == 'Oud' ? 'selected' : '' }}>Oud</option>
                        <option value="Perfume" {{ old('category', $product->category) == 'Perfume' ? 'selected' : '' }}>Perfume</option>
                        <option value="Attar" {{ old('category', $product->category) == 'Attar' ? 'selected' : '' }}>Attar</option>
                        <option value="Oil" {{ old('category', $product->category) == 'Oil' ? 'selected' : '' }}>Oil</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Brand -->
                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand', $product->brand) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('brand') border-red-500 @enderror"
                           required>
                    @error('brand')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Scent -->
                <div>
                    <label for="scent" class="block text-sm font-medium text-gray-700 mb-2">Scent</label>
                    <input type="text" name="scent" id="scent" value="{{ old('scent', $product->scent) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('scent') border-red-500 @enderror"
                           required>
                    @error('scent')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Volume -->
                <div>
                    <label for="volume" class="block text-sm font-medium text-gray-700 mb-2">Volume</label>
                    <input type="text" name="volume" id="volume" value="{{ old('volume', $product->volume) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('volume') border-red-500 @enderror"
                           placeholder="e.g., 30ml, 50ml" required>
                    @error('volume')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('stock') border-red-500 @enderror"
                           required>
                    @error('stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Image -->
                @if($product->image)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-20 w-20 rounded-lg object-cover border">
                        <div class="text-sm text-gray-600">
                            <p class="font-medium">Current image</p>
                            <p>Upload a new image below to replace this one</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Image -->
                <div class="md:col-span-2">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">{{ $product->image ? 'Replace Image (Optional)' : 'Product Image' }}</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md" id="image-upload-area">
                        <div class="space-y-1 text-center" id="upload-placeholder">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="image" name="image" type="file" accept="image/*" class="sr-only" onchange="handleImageSelect(event)">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                        </div>

                        <!-- Image Preview -->
                        <div class="hidden space-y-2" id="image-preview">
                            <div class="relative">
                                <img id="preview-img" src="" alt="Preview" class="max-h-48 max-w-full mx-auto rounded-lg shadow-md">
                                <button type="button" onclick="removeImage()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-sm text-gray-600" id="file-name"></p>
                        </div>
                    </div>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                              required>{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bestseller -->
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" name="bestseller" id="bestseller" value="1" {{ old('bestseller', $product->bestseller) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="bestseller" class="ml-2 block text-sm text-gray-900">
                            Mark as bestseller
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.products') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function handleImageSelect(event) {
    const file = event.target.files[0];
    if (file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Please select a valid image file (PNG, JPG, GIF)');
            removeImage();
            return;
        }

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            removeImage();
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('file-name').textContent = file.name;
            document.getElementById('upload-placeholder').classList.add('hidden');
            document.getElementById('image-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    document.getElementById('image').value = '';
    document.getElementById('preview-img').src = '';
    document.getElementById('file-name').textContent = '';
    document.getElementById('upload-placeholder').classList.remove('hidden');
    document.getElementById('image-preview').classList.add('hidden');
}

// Drag and drop functionality
const uploadArea = document.getElementById('image-upload-area');

uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('border-blue-500', 'bg-blue-50');
});

uploadArea.addEventListener('dragleave', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('border-blue-500', 'bg-blue-50');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('border-blue-500', 'bg-blue-50');

    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const file = files[0];
        // Create a fake event to reuse the handleImageSelect function
        const fakeEvent = {
            target: {
                files: [file]
            }
        };
        handleImageSelect(fakeEvent);
    }
});
</script>
@endsection
