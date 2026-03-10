<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🏪 店舗一覧
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('stores.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    📥 Excel
                </a>
                <a href="{{ route('stores.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    📥 CSV
                </a>
                <a href="{{ route('stores.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + 新規登録
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            {{-- 検索フォーム --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('stores.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">エリア</label>
                        <select name="area_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">すべて</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">店舗名</label>
                        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="キーワード" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white py-2 px-4 rounded text-sm">
                            検索
                        </button>
                        <a href="{{ route('stores.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded text-sm">
                            リセット
                        </a>
                    </div>
                </form>
            </div>

            {{-- 店舗テーブル --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">コード</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">店舗名</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">エリア</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">住所</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">電話番号</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">売上件数</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">状態</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($stores as $store)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ $store->code }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $store->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $store->area->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $store->address ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $store->phone ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($store->sales_count) }}件</td>
                                <td class="px-6 py-4 text-sm text-center">
                                    @if ($store->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">営業中</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">閉鎖</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <a href="{{ route('stores.edit', $store) }}" class="text-blue-600 hover:text-blue-800 mr-3">編集</a>
                                    <form method="POST" action="{{ route('stores.destroy', $store) }}" class="inline" onsubmit="return confirm('削除しますか？関連する売上データも削除されます。')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">削除</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">データがありません</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="px-6 py-4 border-t">
                    {{ $stores->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>