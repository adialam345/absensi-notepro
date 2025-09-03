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
        
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        $absensi = $query->latest()->paginate(20);
        $users = User::where('role', 'karyawan')->get();
        
        return view('admin.laporan.absensi', compact('absensi', 'users'));
    }

    // Kelola Cuti & Izin
    public function indexCuti()
    {
        $cuti = IzinCuti::with('user')->latest()->paginate(15);
        return view('admin.cuti.index', compact('cuti'));
    }

    public function approveCuti($id)
    {
        $cuti = IzinCuti::findOrFail($id);
        $cuti->update(['status' => 'disetujui']);
        
        return redirect()->route('admin.cuti.index')->with('success', 'Pengajuan cuti disetujui');
    }

    public function rejectCuti($id)
    {
        $cuti = IzinCuti::findOrFail($id);
        $cuti->update(['status' => 'ditolak']);
        
        return redirect()->route('admin.cuti.index')->with('success', 'Pengajuan cuti ditolak');
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
}
