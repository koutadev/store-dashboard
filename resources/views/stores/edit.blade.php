<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ✏️ 店舗編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('stores.update', $store) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">エリア</label>
                        <select name="area_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">選択してください</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}" {{ old('area_id', $store->area_id) == $area->id ? 'selected' : '' }}>
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('area_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">店舗名</label>
                        <input type="text" name="name" value="{{ old('name', $store->name) }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">店舗コード</label>
                        <input type="text" name="code" value="{{ old('code', $store->code) }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">住所</label>
                        <input type="text" name="address" value="{{ old('address', $store->address) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">電話番号</label>
                        <input type="text" name="phone" value="{{ old('phone', $store->phone) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $store->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm">
                            <span class="ml-2 text-sm text-gray-700">営業中</span>
                        </label>
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('stores.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            戻る
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            更新する
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>