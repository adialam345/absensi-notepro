<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Pesan;

class KaryawanPesanController extends Controller
{
    public function index()
    {
        $pesanMasuk = Pesan::with(['pengirim'])
            ->where('penerima_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('karyawan.pesan.index', compact('pesanMasuk'));
    }

    public function show($id)
    {
        $pesan = Pesan::with(['pengirim'])
            ->where('id', $id)
            ->where('penerima_id', Auth::id())
            ->firstOrFail();

        // Mark as read
        if (!$pesan->dibaca) {
            $pesan->update([
                'dibaca' => true,
                'dibaca_at' => now()
            ]);
        }

        return view('karyawan.pesan.show', compact('pesan'));
    }

    public function markAsRead($id)
    {
        $pesan = Pesan::where('id', $id)
            ->where('penerima_id', Auth::id())
            ->firstOrFail();

        $pesan->update([
            'dibaca' => true,
            'dibaca_at' => now()
        ]);

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $unreadCount = Pesan::where('penerima_id', Auth::id())
            ->where('dibaca', false)
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }
}