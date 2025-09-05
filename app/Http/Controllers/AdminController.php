<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LokasiKantor;
use App\Models\Absensi;
use App\Models\IzinCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
        
        // Create Excel content
        $filename = 'laporan_absensi_' . date('Y-m-d') . '.xlsx';
        
        // For now, we'll create a CSV with Excel extension (can be improved with PhpSpreadsheet later)
        $handle = fopen('php://temp', 'r+');
        
        // Header
        fputcsv($handle, [
            'TANGGAL', 
            'NAMA KARYAWAN', 
            'JAM MASUK', 
            'JAM PULANG', 
            'STATUS', 
            'DINAS LUAR',
            'ALASAN DINAS LUAR',
            'KETERANGAN',
            'LOKASI MASUK',
            'LOKASI PULANG'
        ]);
        
        // Attendance data
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
        
        // Add empty row separator
        fputcsv($handle, []);
        fputcsv($handle, ['DATA CUTI/IZIN KARYAWAN']);
        fputcsv($handle, []);
        
        // Leave data header
        fputcsv($handle, [
            'TANGGAL MULAI',
            'TANGGAL SELESAI', 
            'NAMA KARYAWAN',
            'TIPE',
            'STATUS',
            'KETERANGAN'
        ]);
        
        // Leave data
        foreach ($izinCuti as $ic) {
            fputcsv($handle, [
                $ic->tanggal_mulai,
                $ic->tanggal_selesai,
                $ic->user->name ?? 'N/A',
                ucfirst($ic->tipe),
                ucfirst($ic->status),
                $ic->keterangan ?? '-'
            ]);
        }
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        return response($content)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
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
        
        // Create HTML content for PDF
        $html = view('admin.exports.absensi-pdf', compact('absensi', 'izinCuti', 'summary', 'request'))->render();
        
        // For now, return HTML (can be improved with DomPDF or similar later)
        $filename = 'laporan_absensi_' . date('Y-m-d') . '.html';
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
