<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Absensi - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            overflow: hidden;
        }
        .chart-container canvas {
            max-height: 300px !important;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <x-admin-navbar title="Statistik Absensi" subtitle="Analisis Data Kehadiran Karyawan" />

    <!-- Content -->
    <div class="p-4">
        <!-- Period Info -->
        <div class="bg-white rounded-lg p-4 mb-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Statistik Bulan {{ date('F', mktime(0, 0, 0, $currentMonth, 1)) }} {{ $currentYear }}</h2>
                    <p class="text-sm text-gray-600">Analisis data kehadiran dan performa karyawan</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Periode</div>
                    <div class="font-semibold text-[#ff040c]">{{ date('F Y', mktime(0, 0, 0, $currentMonth, 1)) }}</div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Hadir</p>
                        <p class="text-2xl font-bold text-green-600">{{ $absensiStats['hadir'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Terlambat</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $absensiStats['terlambat'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Sakit</p>
                        <p class="text-2xl font-bold text-red-600">{{ $absensiStats['sakit'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-thermometer-half text-red-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Izin</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $absensiStats['izin'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-clock text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Attendance Trend Chart -->
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Trend Kehadiran 30 Hari Terakhir</h3>
                <div class="chart-container">
                    <canvas id="attendanceTrendChart"></canvas>
                </div>
            </div>
            
            <!-- Status Distribution Chart -->
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Status Absensi</h3>
                <div class="chart-container">
                    <canvas id="statusDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Employee Performance & Department Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Top Performers -->
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Top 10 Performa Karyawan</h3>
                <div class="space-y-3">
                    @forelse($employeePerformance as $index => $employee)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-[#ff040c] text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $employee->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $employee->jabatan ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-[#ff040c]">{{ $employee->attendance_rate }}%</p>
                                <p class="text-xs text-gray-500">{{ $employee->hadir_count }}/{{ $employee->total_absensi }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Tidak ada data performa</p>
                    @endforelse
                </div>
            </div>
            
            <!-- Department Statistics -->
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik Jabatan</h3>
                <div class="space-y-3">
                    @forelse($departmentStats as $dept)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800">{{ $dept->jabatan ?? 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-[#ff040c]">{{ $dept->total_karyawan }}</p>
                                <p class="text-xs text-gray-500">karyawan</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Tidak ada data jabatan</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Leave Statistics -->
        <div class="bg-white rounded-lg p-6 shadow-sm mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik Cuti & Izin</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $leaveTypes = ['cuti', 'izin', 'sakit'];
                    $leaveStatuses = ['disetujui', 'ditolak', 'pending'];
                @endphp
                
                @foreach($leaveTypes as $type)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-3 capitalize">{{ $type }}</h4>
                        <div class="space-y-2">
                            @foreach($leaveStatuses as $status)
                                @php
                                    $count = $leaveStats->where('tipe', $type)->where('status', $status)->sum('count');
                                @endphp
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 capitalize">{{ $status }}</span>
                                    <span class="font-semibold text-[#ff040c]">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Monthly Comparison -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Perbandingan Bulan Ini vs Bulan Lalu</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $statuses = ['hadir', 'terlambat', 'sakit', 'izin'];
                    $colors = ['green', 'yellow', 'red', 'blue'];
                @endphp
                
                @foreach($statuses as $index => $status)
                    @php
                        $current = $currentMonthData[$status] ?? 0;
                        $previous = $previousMonthData[$status] ?? 0;
                        $change = $previous > 0 ? round((($current - $previous) / $previous) * 100, 1) : 0;
                        $color = $colors[$index];
                    @endphp
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600 capitalize mb-2">{{ $status }}</p>
                        <p class="text-2xl font-bold text-{{ $color }}-600">{{ $current }}</p>
                        <p class="text-xs {{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $change >= 0 ? '+' : '' }}{{ $change }}%
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        // Attendance Trend Chart
        const trendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
        const trendData = @json($dailyTrend);
        
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendData.map(item => new Date(item.date).toLocaleDateString('id-ID')),
                datasets: [
                    {
                        label: 'Hadir',
                        data: trendData.map(item => item.hadir),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Terlambat',
                        data: trendData.map(item => item.terlambat),
                        borderColor: '#F59E0B',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Sakit',
                        data: trendData.map(item => item.sakit),
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Izin',
                        data: trendData.map(item => item.izin),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Tanggal'
                        },
                        ticks: {
                            maxTicksLimit: 10,
                            callback: function(value, index, values) {
                                const date = new Date(this.getLabelForValue(value));
                                return date.toLocaleDateString('id-ID', { 
                                    day: '2-digit', 
                                    month: '2-digit' 
                                });
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        display: true,
                        title: {
                            display: true,
                            text: 'Jumlah'
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 3,
                        hoverRadius: 6
                    }
                }
            }
        });

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusDistributionChart').getContext('2d');
        const statusData = @json($absensiStats);
        
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusData).map(status => status.charAt(0).toUpperCase() + status.slice(1)),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: [
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                        '#3B82F6'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
