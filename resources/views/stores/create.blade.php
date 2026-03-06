<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📝 売上登録
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('sales.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">店舗</label>
                        <select name="store_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">選択してください</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('store_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">商品</label>
                        <select name="product_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">選択してください</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}（¥{{ number_format($product->price) }}）
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">数量</label>
                        <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('quantity') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">売上日</label>
                        <input type="date" name="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('sale_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('sales.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            戻る
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            登録する
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>