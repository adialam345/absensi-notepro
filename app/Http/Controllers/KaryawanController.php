<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Absensi;
use App\Models\IzinCuti;
use App\Models\LokasiKantor;
use App\Helpers\LocationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class KaryawanController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // Get today's attendance
        $todayAbsensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();
        
        // Get current month attendance summary
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $monthlyAbsensi = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->get();
        
        $hadir = $monthlyAbsensi->where('status', 'hadir')->count();
        $izin = $monthlyAbsensi->where('status', 'izin')->count();
        $sakit = $monthlyAbsensi->where('status', 'sakit')->count();
        $terlambat = $monthlyAbsensi->where('status', 'terlambat')->count();
        
        // Get last week attendance
        $lastWeek = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [Carbon::now()->subWeek(), Carbon::now()])
            ->orderBy('tanggal', 'desc')
            ->get();
        
        // Parse work hours
        $jamKerja = explode(' - ', $user->jam_kerja ?? '08:00 - 17:00');
        $jamMasuk = $jamKerja[0] ?? '08:00';
        $jamPulang = $jamKerja[1] ?? '17:00';
        
        return view('karyawan.dashboard', compact(
            'todayAbsensi',
            'hadir',
            'izin', 
            'sakit',
            'terlambat',
            'lastWeek',
            'jamMasuk',
            'jamPulang'
        ));
    }

    public function absenMasuk(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // Check if already attended today
        $existingAbsensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();
            
        if ($existingAbsensi) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen hari ini'
            ]);
        }
        
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|string|starts_with:data:image/'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ]);
        }
        
        // Check location
        $lokasiKantor = LokasiKantor::find($user->lokasi_kantor_id);
        if (!$lokasiKantor) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi kantor tidak ditemukan'
            ]);
        }
        
        // Log coordinates for debugging
        \Log::info('Absen Masuk Debug', [
            'user_id' => $user->id,
            'user_coords' => [$request->latitude, $request->longitude],
            'office_coords' => [$lokasiKantor->latitude, $lokasiKantor->longitude],
            'office_radius' => $lokasiKantor->radius,
            'request_data' => $request->all()
        ]);
        
        // Calculate distance
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $lokasiKantor->latitude,
            $lokasiKantor->longitude
        );
        
        // Log distance calculation
        \Log::info('Distance Calculation', [
            'calculated_distance' => $distance,
            'office_radius' => $lokasiKantor->radius,
            'within_radius' => $distance <= $lokasiKantor->radius
        ]);
        
        if ($distance > $lokasiKantor->radius) {
                    return response()->json([
            'success' => false,
            'message' => 'Anda berada di luar area kantor. Jarak: ' . round($distance, 2) . 'm, Radius: ' . $lokasiKantor->radius . 'm',
            'data' => [
                'calculated_distance' => round($distance, 2),
                'office_radius' => $lokasiKantor->radius,
                'within_radius' => false,
                'user_coords' => [$request->latitude, $request->longitude],
                'office_coords' => [$lokasiKantor->latitude, $lokasiKantor->longitude],
                'status' => 'Luar radius'
            ]
        ]);
        }
        
        // Check if late
        $jamSekarang = Carbon::now();
        $jamKerja = explode(' - ', $user->jam_kerja ?? '08:00 - 17:00');
        $jamMasuk = Carbon::createFromFormat('H:i', $jamKerja[0]);
        
        $status = 'hadir';
        if ($jamSekarang->gt($jamMasuk)) {
            $status = 'terlambat';
        }
        
        // Handle base64 photo data
        $fotoData = $request->foto;
        $fotoPath = null;
        
        if (strpos($fotoData, 'data:image/') === 0) {
            // Extract base64 data
            $base64Data = explode(',', $fotoData)[1];
            $imageData = base64_decode($base64Data);
            
            // Generate unique filename
            $filename = 'absensi_' . $user->id . '_' . time() . '.jpg';
            $fotoPath = 'absensi/' . $filename;
            
            // Save to storage
            \Storage::disk('public')->put($fotoPath, $imageData);
        }
        
        // Create attendance record
        $absensi = Absensi::create([
            'user_id' => $user->id,
            'tanggal' => $today,
            'jam_masuk' => $jamSekarang->format('H:i:s'),
            'status' => $status,
            'foto_masuk' => $fotoPath,
            'lokasi_masuk' => $request->latitude . ',' . $request->longitude,
            'keterangan' => $request->keterangan
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Absen masuk berhasil',
            'data' => [
                'absensi' => $absensi,
                'calculated_distance' => round($distance, 2),
                'office_radius' => $lokasiKantor->radius,
                'within_radius' => true,
                'user_coords' => [$request->latitude, $request->longitude],
                'office_coords' => [$lokasiKantor->latitude, $lokasiKantor->longitude],
                'status' => 'Dalam radius'
            ]
        ]);
    }
    
    public function absenPulang(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // Check if attendance exists
        $absensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();
            
        if (!$absensi) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum absen masuk hari ini'
            ]);
        }
        
        if ($absensi->jam_pulang) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen pulang hari ini'
            ]);
        }
        
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|string|starts_with:data:image/'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ]);
        }
        
        // Handle base64 photo data
        $fotoData = $request->foto;
        $fotoPulangPath = null;
        
        if (strpos($fotoData, 'data:image/') === 0) {
            // Extract base64 data
            $base64Data = explode(',', $fotoData)[1];
            $imageData = base64_decode($base64Data);
            
            // Generate unique filename
            $filename = 'absensi_pulang_' . $user->id . '_' . time() . '.jpg';
            $fotoPulangPath = 'absensi/' . $filename;
            
            // Save to storage
            \Storage::disk('public')->put($fotoPulangPath, $imageData);
        }
        
        // Update attendance record
        $absensi->update([
            'jam_pulang' => Carbon::now()->format('H:i:s'),
            'foto_pulang' => $fotoPulangPath,
            'lokasi_pulang' => $request->latitude . ',' . $request->longitude,
            'keterangan' => $request->keterangan
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Absen pulang berhasil',
            'data' => [
                'absensi' => $absensi,
                'status' => 'Absen pulang berhasil'
            ]
        ]);
    }
    
    public function izinCuti(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'jenis' => 'required|in:izin,cuti,sakit',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:500',
            'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ]);
        }
        
        // Check if dates overlap with existing requests
        $overlapping = IzinCuti::where('user_id', $user->id)
            ->where('status', '!=', 'ditolak')
            ->where(function($query) use ($request) {
                $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_selesai])
                    ->orWhereBetween('tanggal_selesai', [$request->tanggal_mulai, $request->tanggal_selesai])
                    ->orWhere(function($q) use ($request) {
                        $q->where('tanggal_mulai', '<=', $request->tanggal_mulai)
                            ->where('tanggal_selesai', '>=', $request->tanggal_selesai);
                    });
            })
            ->exists();
            
        if ($overlapping) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal yang dipilih bertabrakan dengan pengajuan sebelumnya'
            ]);
        }
        
        $data = [
            'user_id' => $user->id,
            'tipe' => $request->jenis,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'keterangan' => $request->alasan,
            'status' => 'pending'
        ];
        
        // Handle document upload if provided
        if ($request->hasFile('dokumen')) {
            $data['dokumen'] = $request->file('dokumen')->store('dokumen_cuti', 'public');
        }
        
        $izinCuti = IzinCuti::create($data);
        
        return response()->json([
            'success' => true,
            'message' => 'Pengajuan ' . $request->jenis . ' berhasil dikirim',
            'data' => $izinCuti
        ]);
    }
    
    public function historyIzinCuti()
    {
        $user = Auth::user();
        $history = IzinCuti::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }
    
    public function historyAbsensi(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        
        $absensi = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);
            
        return view('karyawan.history', compact('absensi', 'month', 'year'));
    }
    
    public function profile()
    {
        $user = Auth::user();
        $lokasiKantor = LokasiKantor::find($user->lokasi_kantor_id);
        
        return view('karyawan.profile', compact('user', 'lokasiKantor'));
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $data = [
            'name' => $request->name,
            'email' => $request->email
        ];
        
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }
        
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('profile', 'public');
        }
        
        $user->update($data);
        
        return redirect()->route('karyawan.profile')->with('success', 'Profil berhasil diupdate');
    }

    /**
     * Get current attendance data for real-time updates
     */
    public function getCurrentAttendance()
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // Get today's attendance
        $todayAbsensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();
        
        return response()->json([
            'success' => true,
            'data' => [
                'jam_masuk' => $todayAbsensi ? $todayAbsensi->jam_masuk : null,
                'jam_pulang' => $todayAbsensi ? $todayAbsensi->jam_pulang : null,
                'status' => $todayAbsensi ? $todayAbsensi->status : null,
                'has_attendance' => $todayAbsensi ? true : false
            ]
        ]);
    }

    /**
     * Calculate distance between two points using Haversine formula
     * Returns distance in meters
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        return LocationHelper::calculateDistance($lat1, $lon1, $lat2, $lon2);
    }
}
