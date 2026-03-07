<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📊 ダッシュボード
            </h2>
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-3">
                <label class="text-sm text-gray-600">表示月:</label>
                <select name="month" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm text-sm">
                    @foreach ($months as $m)
                        <option value="{{ $m }}" {{ $selectedMonth === $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $m)->format('Y年n月') }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- KPIカード --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {{-- 選択月の売上 --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">{{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('Y年n月') }}の売上</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">
                        ¥{{ number_format($currentMonthSales) }}
                    </div>
                    <div class="text-sm mt-2 {{ $monthOverMonth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $monthOverMonth >= 0 ? '▲' : '▼' }} {{ abs($monthOverMonth) }}% 前月比
                    </div>
                </div>

                {{-- 前月の売上 --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">前月の売上</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">
                        ¥{{ number_format($lastMonthSales) }}
                    </div>
                </div>

                {{-- 目標達成率 --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">目標達成率</div>
                    <div class="text-2xl font-bold mt-1 {{ $achievementRate >= 100 ? 'text-green-600' : 'text-orange-500' }}">
                        {{ $achievementRate }}%
                    </div>
                    <div class="text-sm text-gray-400 mt-2">
                        目標: ¥{{ number_format($currentTarget) }}
                    </div>
                </div>

                {{-- 売上件数 --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">売上件数</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">
                        {{ number_format($currentMonthCount) }}件
                    </div>
                </div>
            </div>

            {{-- 月別売上推移グラフ --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">月別売上推移</h3>
                <canvas id="monthlySalesChart" height="100"></canvas>
            </div>

            {{-- 店舗別 & カテゴリ別 --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- 店舗別売上 --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">店舗別売上（{{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('n月') }}）</h3>
                    <canvas id="storeSalesChart" height="200"></canvas>
                </div>

                {{-- カテゴリ別売上 --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">カテゴリ別売上（{{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('n月') }}）</h3>
                    <canvas id="categorySalesChart" height="200"></canvas>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const monthlySalesData = {!! json_encode($monthlySales->pluck('total')) !!};
        const monthlySalesLabels = {!! json_encode($monthlySales->pluck('month')) !!};
        const selectedMonth = '{{ $selectedMonth }}';

        // 選択月をハイライトするための背景色配列
        const barColors = monthlySalesLabels.map(label =>
            label === selectedMonth ? 'rgba(59, 130, 246, 0.3)' : 'rgba(59, 130, 246, 0.1)'
        );

        new Chart(document.getElementById('monthlySalesChart'), {
            type: 'line',
            data: {
                labels: monthlySalesLabels,
                datasets: [{
                    label: '売上合計',
                    data: monthlySalesData,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: barColors,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: monthlySalesLabels.map(label =>
                        label === selectedMonth ? 'rgb(239, 68, 68)' : 'rgb(59, 130, 246)'
                    ),
                    pointRadius: monthlySalesLabels.map(label =>
                        label === selectedMonth ? 6 : 3
                    ),
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return '¥' + Number(ctx.raw).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return '¥' + (value / 10000).toLocaleString() + '万';
                            }
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('storeSalesChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($storeSales->pluck('name')) !!},
                datasets: [{
                    label: '売上合計',
                    data: {!! json_encode($storeSales->pluck('total')) !!},
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(20, 184, 166, 0.8)',
                    ],
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return '¥' + Number(ctx.raw).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            callback: function(value) {
                                return '¥' + (value / 10000).toLocaleString() + '万';
                            }
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('categorySalesChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categorySales->pluck('name')) !!},
                datasets: [{
                    data: {!! json_encode($categorySales->pluck('total')) !!},
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                    ],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ctx.label + ': ¥' + Number(ctx.raw).toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>