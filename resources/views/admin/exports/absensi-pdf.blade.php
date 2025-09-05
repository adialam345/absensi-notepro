<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ff040c;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #ff040c;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .summary h3 {
            color: #ff040c;
            margin-top: 0;
        }
        .summary-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: separate;
            border-spacing: 8px;
        }
        .summary-item {
            background: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 16.66%;
        }
        .summary-item .number {
            font-size: 20px;
            font-weight: bold;
            color: #ff040c;
        }
        .summary-item .label {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }
        .section {
            margin-bottom: 40px;
        }
        .section h3 {
            color: #ff040c;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        th {
            background-color: #ff040c;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-hadir { background-color: #d4edda; color: #155724; }
        .status-terlambat { background-color: #fff3cd; color: #856404; }
        .status-izin { background-color: #cce5ff; color: #004085; }
        .status-sakit { background-color: #f8d7da; color: #721c24; }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        
        /* Print/PDF specific styles */
        @media print {
            body { margin: 0; }
            .summary-table {
                width: 100% !important;
                border-collapse: separate !important;
                border-spacing: 8px !important;
                page-break-inside: avoid;
            }
            .summary-item {
                width: 16.66% !important;
                page-break-inside: avoid;
            }
            table { page-break-inside: avoid; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN ABSENSI KARYAWAN</h1>
        <p>Periode: 
            @if($request->filled('month') && $request->filled('year'))
                {{ date('F', mktime(0, 0, 0, $request->month, 1)) }} {{ $request->year }}
            @elseif($request->filled('month'))
                {{ date('F', mktime(0, 0, 0, $request->month, 1)) }} {{ date('Y') }}
            @elseif($request->filled('year'))
                {{ $request->year }}
            @else
                {{ date('F Y') }}
            @endif
        </p>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <h3>Ringkasan Data</h3>
        <table class="summary-table">
            <tr>
                <td class="summary-item">
                    <div class="number">{{ $summary['hadir'] }}</div>
                    <div class="label">Total Hadir</div>
                </td>
                <td class="summary-item">
                    <div class="number">{{ $summary['terlambat'] }}</div>
                    <div class="label">Terlambat</div>
                </td>
                <td class="summary-item">
                    <div class="number">{{ $summary['izin'] }}</div>
                    <div class="label">Izin</div>
                </td>
                <td class="summary-item">
                    <div class="number">{{ $summary['sakit'] }}</div>
                    <div class="label">Sakit</div>
                </td>
                <td class="summary-item">
                    <div class="number">{{ $summary['cuti'] }}</div>
                    <div class="label">Cuti</div>
                </td>
                <td class="summary-item">
                    <div class="number">{{ $summary['dinas_luar'] }}</div>
                    <div class="label">Dinas Luar</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Data Absensi Karyawan</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Karyawan</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status</th>
                    <th>Dinas Luar</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensi as $a)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($a->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $a->user->name }}</td>
                        <td>{{ $a->jam_masuk ?? '-' }}</td>
                        <td>{{ $a->jam_pulang ?? '-' }}</td>
                        <td>
                            <span class="status-badge status-{{ $a->status }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                        <td>{{ $a->dinas_luar ? 'Ya' : 'Tidak' }}</td>
                        <td>{{ $a->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">Tidak ada data absensi</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Data Cuti/Izin Karyawan</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Nama Karyawan</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($izinCuti as $ic)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($ic->tanggal_mulai)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($ic->tanggal_selesai)->format('d/m/Y') }}</td>
                        <td>{{ $ic->user->name }}</td>
                        <td>
                            <span class="status-badge status-{{ $ic->tipe }}">
                                {{ ucfirst($ic->tipe) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $ic->status }}">
                                {{ ucfirst($ic->status) }}
                            </span>
                        </td>
                        <td>{{ $ic->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Tidak ada data cuti/izin</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem absensi</p>
        <p>© {{ date('Y') }} - Sistem Absensi Karyawan</p>
    </div>
</body>
</html>
