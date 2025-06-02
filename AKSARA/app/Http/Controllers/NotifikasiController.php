<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotifikasiModel;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = NotifikasiModel::query();

        if ($user->role == 'admin') {
            // Admin menerima semua notifikasi dari dosen/mahasiswa
            $query->whereHas('user', function ($q) {
                $q->whereIn('role', ['dosen', 'mahasiswa']);
            });
        } elseif ($user->role == 'dosen') {
            // Dosen: notifikasi dari prestasi yang mencantumkan dia sebagai pembimbing
            $query->whereHas('prestasi', function ($q) use ($user) {
                $q->where('dosen_pembimbing', $user->user_id);
            });
        } elseif ($user->role == 'mahasiswa') {
            // Mahasiswa: notifikasi dari prestasi/keahlian/lomba miliknya yang diverifikasi/ditolak
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->user_id);
            });
        }

        $notifikasi = $query->latest()->get();

        return view('notifikasi.index', compact('notifikasi'));
    }

    public function show($id)
    {
        $notifikasi = NotifikasiModel::findOrFail($id);
        return response()->json($notifikasi);
    }

    public function markAsRead($id)
    {
        $notifikasi = NotifikasiModel::findOrFail($id);
        $notifikasi->status_baca = 'dibaca';
        $notifikasi->save();

        return response()->json(['success' => true, 'message' => 'Notifikasi ditandai sebagai dibaca']);
    }

    public function markAllAsRead()
    {
        NotifikasiModel::where('user_id', Auth::id())->update(['status_baca' => 'dibaca']);

        return response()->json(['success' => true, 'message' => 'Semua notifikasi ditandai dibaca']);
    }

    public function destroy($id)
    {
        $notifikasi = NotifikasiModel::findOrFail($id);
        $notifikasi->delete();

        return response()->json(['success' => true, 'message' => 'Notifikasi berhasil dihapus']);
    }
}
