<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LokasiKantor;
use App\Models\Absensi;
use App\Models\IzinCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalKaryawan = User::where('role', 'karyawan')->count();
        $karyawanAktif = User::where('role', 'karyawan')->where('status', 'aktif')->count();
        $totalLokasi = LokasiKantor::count();
        
        $recentActivities = Absensi::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('totalKaryawan', 'karyawanAktif', 'totalLokasi', 'recentActivities'));
    }

    public function statistik()
    {
        // Get current month data
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Attendance statistics for current month
        $absensiStats = Absensi::whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
        
        // Daily attendance trend (last 30 days)
        $dailyTrend = Absensi::where('tanggal', '>=', now()->subDays(30))
            ->selectRaw('DATE(tanggal) as date, COUNT(*) as total, 
                        SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN status = "terlambat" THEN 1 ELSE 0 END) as terlambat,
                        SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Employee performance (top 10)
        $employeePerformance = User::where('role', 'karyawan')
            ->withCount(['absensi as total_absensi' => function($query) use ($currentMonth, $currentYear) {
                $query->whereMonth('tanggal', $currentMonth)
                      ->whereYear('tanggal', $currentYear);
            }])
            ->withCount(['absensi as hadir_count' => function($query) use ($currentMonth, $currentYear) {
                $query->whereMonth('tanggal', $currentMonth)
                      ->whereYear('tanggal', $currentYear)
                      ->where('status', 'hadir');
            }])
            ->withCount(['absensi as terlambat_count' => function($query) use ($currentMonth, $currentYear) {
                $query->whereMonth('tanggal', $currentMonth)
                      ->whereYear('tanggal', $currentYear)
                      ->where('status', 'terlambat');
            }])
            ->having('total_absensi', '>', 0)
            ->get()
            ->map(function($user) {
                $user->attendance_rate = $user->total_absensi > 0 ? 
                    round(($user->hadir_count / $user->total_absensi) * 100, 1) : 0;
                return $user;
            })
            ->sortByDesc('attendance_rate')
            ->take(10);
        
        // Leave statistics
        $leaveStats = IzinCuti::whereMonth('tanggal_mulai', $currentMonth)
            ->whereYear('tanggal_mulai', $currentYear)
            ->selectRaw('tipe, status, COUNT(*) as count')
            ->groupBy('tipe', 'status')
            ->get();
        
        // Department statistics (using jabatan instead of divisi)
        $departmentStats = User::where('role', 'karyawan')
            ->selectRaw('jabatan, COUNT(*) as total_karyawan')
            ->groupBy('jabatan')
            ->get();
        
        // Monthly comparison (current vs previous month)
        $currentMonthData = Absensi::whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
        
        $previousMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        $previousYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
        
        $previousMonthData = Absensi::whereMonth('tanggal', $previousMonth)
            ->whereYear('tanggal', $previousYear)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
        
        return view('admin.statistik', compact(
            'absensiStats',
            'dailyTrend',
            'employeePerformance',
            'leaveStats',
            'departmentStats',
            'currentMonthData',
            'previousMonthData',
            'currentMonth',
            'currentYear'
        ));
    }

    // CRUD Karyawan
    public function indexKaryawan()
    {
        $karyawan = User::where('role', 'karyawan')->paginate(10);
        return view('admin.karyawan.index', compact('karyawan'));
    }

    public function createKaryawan()
    {
        $lokasiKantor = LokasiKantor::all();
        return view('admin.karyawan.create', compact('lokasiKantor'));
    }

    public function storeKaryawan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'jabatan' => 'required|string|max:255',
            'jam_kerja_masuk' => 'required|date_format:H:i',
            'jam_kerja_pulang' => 'required|date_format:H:i|after:jam_kerja_masuk',
            'lokasi_kantor_id' => 'required|exists:lokasi_kantors,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $jamKerja = $request->jam_kerja_masuk . ' - ' . $request->jam_kerja_pulang;

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jabatan' => $request->jabatan,
            'jam_kerja' => $jamKerja,
            'role' => 'karyawan',
            'status' => 'aktif',
            'lokasi_kantor_id' => $request->lokasi_kantor_id,
        ]);

        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function editKaryawan($id)
    {
        $karyawan = User::findOrFail($id);
        $lokasiKantor = LokasiKantor::all();
        
        // Parse jam kerja
        $jamKerja = explode(' - ', $karyawan->jam_kerja);
        $karyawan->jam_masuk = $jamKerja[0] ?? '';
        $karyawan->jam_pulang = $jamKerja[1] ?? '';
        
        return view('admin.karyawan.edit', compact('karyawan', 'lokasiKantor'));
    }

    public function updateKaryawan(Request $request, $id)
    {
        $karyawan = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'jabatan' => 'required|string|max:255',
            'jam_kerja_masuk' => 'required|date_format:H:i',
            'jam_kerja_pulang' => 'required|date_format:H:i|after:jam_kerja_masuk',
            'lokasi_kantor_id' => 'required|exists:lokasi_kantors,id',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $jamKerja = $request->jam_kerja_masuk . ' - ' . $request->jam_kerja_pulang;

        $updateData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'jabatan' => $request->jabatan,
            'jam_kerja' => $jamKerja,
            'lokasi_kantor_id' => $request->lokasi_kantor_id,
        ];

        // Update password only if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $karyawan->update($updateData);

        return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan berhasil diupdate');
    }

    public function destroyKaryawan($id)
    {
        $karyawan = User::findOrFail($id);
        $karyawan->delete();

        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil dihapus');
    }

    // CRUD Lokasi Kantor
    public function indexLokasi()
    {
        $lokasi = LokasiKantor::paginate(10);
        return view('admin.lokasi.index', compact('lokasi'));
    }

    public function createLokasi()
    {
        return view('admin.lokasi.create');
    }

    public function storeLokasi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lokasi' => 'required|string|max:255',
            'alamat' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        LokasiKantor::create($request->all());

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi kantor berhasil ditambahkan');
    }

    public function editLokasi($id)
    {
        $lokasi = LokasiKantor::findOrFail($id);
        return view('admin.lokasi.edit', compact('lokasi'));
    }

    public function updateLokasi(Request $request, $id)
    {
        $lokasi = LokasiKantor::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nama_lokasi' => 'required|string|max:255',
            'alamat' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lokasi->update($request->all());

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi kantor berhasil diupdate');
    }

    public function destroyLokasi($id)
    {
        $lokasi = LokasiKantor::findOrFail($id);
        $lokasi->delete();

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi kantor berhasil dihapus');
    }

    // Laporan Absensi
    public function laporanAbsensi(Request $request)
    {
        $query = Absensi::with('user');
        
        // Filter berdasarkan bulan dan tahun
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('tanggal', $request->month)
                  ->whereYear('tanggal', $request->year);
        } elseif ($request->filled('month')) {
            $query->whereMonth('tanggal', $request->month);
        } elseif ($request->filled('year')) {
            $query->whereYear('tanggal', $request->year);
        }
        
        // Filter berdasarkan karyawan
        if ($request->filled('karyawan')) {
            $query->where('user_id', $request->karyawan);
        }
        
        $absensi = $query->latest('tanggal')->paginate(20);
        $karyawan = User::where('role', 'karyawan')->get();
        
        // Hitung summary untuk absensi
        $summaryQuery = Absensi::query();
        if ($request->filled('month') && $request->filled('year')) {
            $summaryQuery->whereMonth('tanggal', $request->month)
                        ->whereYear('tanggal', $request->year);
        } elseif ($request->filled('month')) {
            $summaryQuery->whereMonth('tanggal', $request->month);
        } elseif ($request->filled('year')) {
            $summaryQuery->whereYear('tanggal', $request->year);
        }
        if ($request->filled('karyawan')) {
            $summaryQuery->where('user_id', $request->karyawan);
        }
        
        // Hitung summary untuk izin/cuti dari tabel IzinCuti
        $izinCutiQuery = IzinCuti::where('status', 'disetujui');
        if ($request->filled('month') && $request->filled('year')) {
            $izinCutiQuery->where(function($query) use ($request) {
                $query->whereMonth('tanggal_mulai', $request->month)
                      ->whereYear('tanggal_mulai', $request->year)
                      ->orWhere(function($q) use ($request) {
                          $q->whereMonth('tanggal_selesai', $request->month)
                            ->whereYear('tanggal_selesai', $request->year);
                      });
            });
        } elseif ($request->filled('month')) {
            $izinCutiQuery->where(function($query) use ($request) {
                $query->whereMonth('tanggal_mulai', $request->month)
                      ->orWhereMonth('tanggal_selesai', $request->month);
            });
        } elseif ($request->filled('year')) {
            $izinCutiQuery->where(function($query) use ($request) {
                $query->whereYear('tanggal_mulai', $request->year)
                      ->orWhereYear('tanggal_selesai', $request->year);
            });
        }
        if ($request->filled('karyawan')) {
            $izinCutiQuery->where('user_id', $request->karyawan);
        }
        
        $summary = [
            'hadir' => (clone $summaryQuery)->where('status', 'hadir')->count(),
            'terlambat' => (clone $summaryQuery)->where('status', 'terlambat')->count(),
            'izin' => (clone $izinCutiQuery)->where('tipe', 'izin')->count(),
            'sakit' => (clone $izinCutiQuery)->where('tipe', 'sakit')->count(),
            'cuti' => (clone $izinCutiQuery)->where('tipe', 'cuti')->count(),
            'dinas_luar' => (clone $summaryQuery)->where('dinas_luar', true)->count(),
        ];
        
        return view('admin.laporan.absensi', compact('absensi', 'karyawan', 'summary'));
    }

    // View Foto Absensi
    public function viewFotoAbsensi($id)
    {
        $absensi = Absensi::with('user')->findOrFail($id);
        
        return view('admin.absensi.foto', compact('absensi'));
    }

    // Kelola Cuti & Izin
    public function indexCuti(Request $request)
    {
        $query = IzinCuti::with('user');
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan tipe
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        
        // Filter berdasarkan karyawan
        if ($request->filled('karyawan')) {
            $query->where('user_id', $request->karyawan);
        }
        
        $cuti = $query->latest()->paginate(15);
        $karyawan = User::where('role', 'karyawan')->get();
        
        // Hitung summary
        $summary = [
            'pending' => IzinCuti::where('status', 'pending')->count(),
            'disetujui' => IzinCuti::where('status', 'disetujui')->count(),
            'ditolak' => IzinCuti::where('status', 'ditolak')->count(),
        ];
        
        return view('admin.cuti.index', compact('cuti', 'karyawan', 'summary'));
    }

    public function approveCuti($id)
    {
        $cuti = IzinCuti::findOrFail($id);
        $cuti->update(['status' => 'disetujui']);
        
        return response()->json([
            'success' => true,
            'message' => 'Pengajuan cuti disetujui'
        ]);
    }

    public function rejectCuti($id)
    {
        $cuti = IzinCuti::findOrFail($id);
        $cuti->update(['status' => 'ditolak']);
        
        return response()->json([
            'success' => true,
            'message' => 'Pengajuan cuti ditolak'
        ]);
    }

    // Export Data
    public function exportKaryawan()
    {
        $karyawan = User::where('role', 'karyawan')->get();
        
        $filename = 'karyawan_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'r+');
        
        // Header CSV
        fputcsv($handle, ['Nama', 'Username', 'Email', 'Jabatan', 'Jam Kerja', 'Status']);
        
        foreach ($karyawan as $k) {
            fputcsv($handle, [
                $k->name,
                $k->username,
                $k->email,
                $k->jabatan,
                $k->jam_kerja,
                $k->status
            ]);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exportAbsensi(Request $request)
    {
        $query = Absensi::with('user');
        
        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }
        
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }
        
        // Filter berdasarkan karyawan
        if ($request->filled('karyawan_id')) {
            $query->where('user_id', $request->karyawan_id);
        }
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $absensi = $query->orderBy('tanggal', 'desc')->get();
        
        $filename = 'absensi_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'r+');
        
        // Header CSV
        fputcsv($handle, [
            'Tanggal', 
            'Nama Karyawan', 
            'Jam Masuk', 
            'Jam Pulang', 
            'Status', 
            'Dinas Luar',
            'Alasan Dinas Luar',
            'Keterangan',
            'Lokasi Masuk',
            'Lokasi Pulang'
        ]);
        
        foreach ($absensi as $a) {
            fputcsv($handle, [
                $a->tanggal,
                $a->user->name ?? 'N/A',
                $a->jam_masuk ?? '-',
                $a->jam_pulang ?? '-',
                ucfirst($a->status),
                $a->dinas_luar ? 'Ya' : 'Tidak',
                $a->alasan_dinas_luar ?? '-',
                $a->keterangan ?? '-',
                $a->lokasi_masuk ?? '-',
                $a->lokasi_pulang ?? '-'
            ]);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exportAbsensiExcel(Request $request)
    {
        // Get attendance data
        $query = Absensi::with('user');
        
        // Apply same filters as the report
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('tanggal', $request->month)
                  ->whereYear('tanggal', $request->year);
        } elseif ($request->filled('month')) {
            $query->whereMonth('tanggal', $request->month);
        } elseif ($request->filled('year')) {
            $query->whereYear('tanggal', $request->year);
        }
        
        if ($request->filled('karyawan')) {
            $query->where('user_id', $request->karyawan);
        }
        
        $absensi = $query->orderBy('tanggal', 'desc')->get();
        
        // Get leave data for the same period
        $izinCutiQuery = IzinCuti::with('user')->where('status', 'disetujui');
        
        if ($request->filled('month') && $request->filled('year')) {
            $izinCutiQuery->where(function($query) use ($request) {
                $query->whereMonth('tanggal_mulai', $request->month)
                      ->whereYear('tanggal_mulai', $request->year)
                      ->orWhere(function($q) use ($request) {
                          $q->whereMonth('tanggal_selesai', $request->month)
                            ->whereYear('tanggal_selesai', $request->year);
                      });
            });
        } elseif ($request->filled('month')) {
            $izinCutiQuery->where(function($query) use ($request) {
                $query->whereMonth('tanggal_mulai', $request->month)
                      ->orWhereMonth('tanggal_selesai', $request->month);
            });
        } elseif ($request->filled('year')) {
            $izinCutiQuery->where(function($query) use ($request) {
                $query->whereYear('tanggal_mulai', $request->year)
                      ->orWhereYear('tanggal_selesai', $request->year);
            });
        }
        
        if ($request->filled('karyawan')) {
            $izinCutiQuery->where('user_id', $request->karyawan);
        }
        
        $izinCuti = $izinCutiQuery->orderBy('tanggal_mulai', 'desc')->get();
        
        // Generate filename
        $filename = 'laporan_absensi_' . date('Y-m-d') . '.csv';
        
        // Create CSV content with professional template
        $csvContent = '';
        
        // Add BOM for UTF-8
        $csvContent .= "\xEF\xBB\xBF";
        
        // Period info
        $period = '';
        if ($request->filled('month') && $request->filled('year')) {
            $period = date('F', mktime(0, 0, 0, $request->month, 1)) . ' ' . $request->year;
        } elseif ($request->filled('month')) {
            $period = date('F', mktime(0, 0, 0, $request->month, 1)) . ' ' . date('Y');
        } elseif ($request->filled('year')) {
            $period = $request->year;
        } else {
            $period = date('F Y');
        }
        
        // Company Header Template
        $companyName = config('app.company_name', 'PT. NAMA PERUSAHAAN');
        $csvContent .= $companyName . "\n";
        $csvContent .= "SISTEM ABSENSI KARYAWAN\n";
        $csvContent .= "==========================================\n\n";
        
        // Report Header
        $csvContent .= "LAPORAN ABSENSI KARYAWAN\n";
        $csvContent .= "Periode: " . $period . "\n";
        $csvContent .= "Tanggal Cetak: " . date('d/m/Y H:i:s') . "\n";
        $csvContent .= "Dibuat Oleh: Admin Sistem\n";
        $csvContent .= "Sistem: Laravel " . app()->version() . "\n\n";
        
        // Summary Section
        $csvContent .= "RINGKASAN DATA\n";
        $csvContent .= "==========================================\n";
        $csvContent .= "Total Data Absensi: " . $absensi->count() . " record\n";
        $csvContent .= "Total Data Cuti/Izin: " . $izinCuti->count() . " record\n";
        $csvContent .= "Total Karyawan: " . $absensi->pluck('user_id')->unique()->count() . " orang\n";
        
        // Status Summary
        $statusSummary = $absensi->groupBy('status')->map->count();
        $csvContent .= "\nRINGKASAN STATUS ABSENSI:\n";
        foreach ($statusSummary as $status => $count) {
            $csvContent .= "- " . ucfirst($status) . ": " . $count . " record\n";
        }
        
        // Leave Type Summary
        if ($izinCuti->count() > 0) {
            $leaveSummary = $izinCuti->groupBy('tipe')->map->count();
            $csvContent .= "\nRINGKASAN TIPE CUTI/IZIN:\n";
            foreach ($leaveSummary as $tipe => $count) {
                $csvContent .= "- " . ucfirst($tipe) . ": " . $count . " record\n";
            }
        }
        
        $csvContent .= "\n";
        
        // Attendance Data Section
        $csvContent .= "DATA ABSENSI KARYAWAN\n";
        $csvContent .= "==========================================\n";
        $csvContent .= "No,Tanggal,Nama Karyawan,Jam Masuk,Jam Pulang,Status,Dinas Luar,Alasan Dinas Luar,Keterangan,Lokasi Masuk,Lokasi Pulang\n";
        
        // Attendance data with numbering
        $no = 1;
        foreach ($absensi as $a) {
            $csvContent .= $no . ',"' . ($a->tanggal ? \Carbon\Carbon::parse($a->tanggal)->format('d/m/Y') : '-') . '",';
            $csvContent .= '"' . ($a->user->name ?? 'N/A') . '",';
            $csvContent .= '"' . ($a->jam_masuk ?? '-') . '",';
            $csvContent .= '"' . ($a->jam_pulang ?? '-') . '",';
            $csvContent .= '"' . ($a->status ? ucfirst($a->status) : '-') . '",';
            $csvContent .= '"' . ($a->dinas_luar ? 'Ya' : 'Tidak') . '",';
            $csvContent .= '"' . ($a->alasan_dinas_luar ?? '-') . '",';
            $csvContent .= '"' . ($a->keterangan ?? '-') . '",';
            $csvContent .= '"' . ($a->lokasi_masuk ?? '-') . '",';
            $csvContent .= '"' . ($a->lokasi_pulang ?? '-') . '"' . "\n";
            $no++;
        }
        
        // Leave Data Section
        $csvContent .= "\nDATA CUTI/IZIN KARYAWAN\n";
        $csvContent .= "==========================================\n";
        $csvContent .= "No,Tanggal Mulai,Tanggal Selesai,Nama Karyawan,Tipe,Status,Keterangan\n";
        
        // Leave data with numbering
        $no = 1;
        foreach ($izinCuti as $ic) {
            $csvContent .= $no . ',"' . ($ic->tanggal_mulai ? \Carbon\Carbon::parse($ic->tanggal_mulai)->format('d/m/Y') : '-') . '",';
            $csvContent .= '"' . ($ic->tanggal_selesai ? \Carbon\Carbon::parse($ic->tanggal_selesai)->format('d/m/Y') : '-') . '",';
            $csvContent .= '"' . ($ic->user->name ?? 'N/A') . '",';
            $csvContent .= '"' . ($ic->tipe ? ucfirst($ic->tipe) : '-') . '",';
            $csvContent .= '"' . ($ic->status ? ucfirst($ic->status) : '-') . '",';
            $csvContent .= '"' . ($ic->keterangan ?? '-') . '"' . "\n";
            $no++;
        }
        
        // Footer
        $csvContent .= "\n==========================================\n";
        $csvContent .= "Laporan ini dibuat secara otomatis oleh sistem absensi\n";
        $csvContent .= "© " . date('Y') . " - " . $companyName . "\n";
        $csvContent .= "==========================================\n";
        
        // Return CSV file
        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');
    }

    public function exportAbsensiPDF(Request $request)
    {
        // Get attendance data
        $query = Absensi::with('user');
        
        // Apply same filters as the report
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('tanggal', $request->month)
                  ->whereYear('tanggal', $request->year);
        } elseif ($request->filled('month')) {
            $query->whereMonth('tanggal', $request->month);
        } elseif ($request->filled('year')) {
            $query->whereYear('tanggal', $request->year);
        }
        
        if ($request->filled('karyawan')) {
            $query->where('user_id', $request->karyawan);
        }
        
        $absensi = $query->orderBy('tanggal', 'desc')->get();
        
        // Get leave data for the same period
        $izinCutiQuery = IzinCuti::with('user')->where('status', 'disetujui');
        
        if ($request->filled('month') && $request->filled('year')) {
            $izinCutiQuery->where(function($query) use ($request) {
                $query->whereMonth('tanggal_mulai', $request->month)
                      ->whereYear('tanggal_mulai', $request->year)
                      ->orWhere(function($q) use ($request) {
                          $q->whereMonth('tanggal_selesai', $request->month)
                            ->whereYear('tanggal_selesai', $request->year);
                      });
            });
        } elseif ($request->filled('month')) {
            $izinCutiQuery->where(function($query) use ($request) {
                $query->whereMonth('tanggal_mulai', $request->month)
                      ->orWhereMonth('tanggal_selesai', $request->month);
            });
        } elseif ($request->filled('year')) {
            $izinCutiQuery->where(function($query) use ($request) {
                $query->whereYear('tanggal_mulai', $request->year)
                      ->orWhereYear('tanggal_selesai', $request->year);
            });
        }
        
        if ($request->filled('karyawan')) {
            $izinCutiQuery->where('user_id', $request->karyawan);
        }
        
        $izinCuti = $izinCutiQuery->orderBy('tanggal_mulai', 'desc')->get();
        
        // Get summary data
        $summaryQuery = Absensi::query();
        if ($request->filled('month') && $request->filled('year')) {
            $summaryQuery->whereMonth('tanggal', $request->month)
                        ->whereYear('tanggal', $request->year);
        } elseif ($request->filled('month')) {
            $summaryQuery->whereMonth('tanggal', $request->month);
        } elseif ($request->filled('year')) {
            $summaryQuery->whereYear('tanggal', $request->year);
        }
        if ($request->filled('karyawan')) {
            $summaryQuery->where('user_id', $request->karyawan);
        }
        
        $izinCutiSummaryQuery = IzinCuti::where('status', 'disetujui');
        if ($request->filled('month') && $request->filled('year')) {
            $izinCutiSummaryQuery->where(function($query) use ($request) {
                $query->whereMonth('tanggal_mulai', $request->month)
                      ->whereYear('tanggal_mulai', $request->year)
                      ->orWhere(function($q) use ($request) {
                          $q->whereMonth('tanggal_selesai', $request->month)
                            ->whereYear('tanggal_selesai', $request->year);
                      });
            });
        } elseif ($request->filled('month')) {
            $izinCutiSummaryQuery->where(function($query) use ($request) {
                $query->whereMonth('tanggal_mulai', $request->month)
                      ->orWhereMonth('tanggal_selesai', $request->month);
            });
        } elseif ($request->filled('year')) {
            $izinCutiSummaryQuery->where(function($query) use ($request) {
                $query->whereYear('tanggal_mulai', $request->year)
                      ->orWhereYear('tanggal_selesai', $request->year);
            });
        }
        if ($request->filled('karyawan')) {
            $izinCutiSummaryQuery->where('user_id', $request->karyawan);
        }
        
        $summary = [
            'hadir' => (clone $summaryQuery)->where('status', 'hadir')->count(),
            'terlambat' => (clone $summaryQuery)->where('status', 'terlambat')->count(),
            'izin' => (clone $izinCutiSummaryQuery)->where('tipe', 'izin')->count(),
            'sakit' => (clone $izinCutiSummaryQuery)->where('tipe', 'sakit')->count(),
            'cuti' => (clone $izinCutiSummaryQuery)->where('tipe', 'cuti')->count(),
            'dinas_luar' => (clone $summaryQuery)->where('dinas_luar', true)->count(),
        ];
        
        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('admin.exports.absensi-pdf', compact('absensi', 'izinCuti', 'summary', 'request'));
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'landscape');
        
        // Set options for better CSS rendering
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'defaultFont' => 'Arial'
        ]);
        
        $filename = 'laporan_absensi_' . date('Y-m-d') . '.pdf';
        
        // Download PDF
        return $pdf->download($filename);
    }
}
