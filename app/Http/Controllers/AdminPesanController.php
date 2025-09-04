<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Pesan;
use App\Models\User;

class AdminPesanController extends Controller
{
    public function index()
    {
        $karyawan = User::where('role', 'karyawan')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
        return view('admin.pesan.index', compact('karyawan'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'penerima_id' => 'required|exists:users,id',
                'judul' => 'required|string|max:255',
                'pesan' => 'required|string',
                'tipe' => 'required|in:info,peringatan,pemberitahuan',
                'dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:1024'
            ]);

            $data = [
                'pengirim_id' => Auth::id(),
                'penerima_id' => $request->penerima_id,
                'judul' => $request->judul,
                'pesan' => $request->pesan,
                'tipe' => $request->tipe,
            ];

            // Handle dokumen upload (optimized)
            if ($request->hasFile('dokumen')) {
                $file = $request->file('dokumen');
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                $path = $file->storeAs('public/pesan', $filename);
                $data['dokumen'] = 'pesan/' . $filename;
            }

            // Create pesan with optimized query
            $pesan = Pesan::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dikirim!',
                'data' => [
                    'id' => $pesan->id,
                    'judul' => $pesan->judul
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history()
    {
        $pesanTerikirim = Pesan::with(['penerima'])
            ->where('pengirim_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.pesan.history', compact('pesanTerikirim'));
    }
}