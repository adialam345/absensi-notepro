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
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $jamKerja = $request->jam_kerja_masuk . ' - ' . $request->jam_kerja_pulang;

        $karyawan->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'jabatan' => $request->jabatan,
            'jam_kerja' => $jamKerja,
            'lokasi_kantor_id' => $request->lokasi_kantor_id,
            'status' => $request->status,
        ]);

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
        
        // Hitung summary
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
        
        $summary = [
            'hadir' => $summaryQuery->where('status', 'hadir')->count(),
            'terlambat' => $summaryQuery->where('status', 'terlambat')->count(),
            'izin' => $summaryQuery->where('status', 'izin')->count(),
            'sakit' => $summaryQuery->where('status', 'sakit')->count(),
        ];
        
        return view('admin.laporan.absensi', compact('absensi', 'karyawan', 'summary'));
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
}
