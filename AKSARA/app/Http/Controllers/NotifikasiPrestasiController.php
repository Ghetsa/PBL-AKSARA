<?php

namespace App\Http\Controllers;

use App\Models\NotifikasiPrestasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiPrestasiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Hitung jumlah untuk setiap kategori
        $totalAll    = $user->notifikasiPrestasi()->count();
        $totalUnread = $user->notifikasiPrestasi()->where('status_baca', 'belum_dibaca')->count();
        $totalRead   = $user->notifikasiPrestasi()->where('status_baca', 'dibaca')->count();

        $filter = $request->get('filter', 'all');

        // Bangun query dasar
        $query = $user->notifikasiPrestasi()
                      ->orderByDesc('created_at');

        // Terapkan filter
        if ($filter === 'unread') {
            $query->where('status_baca', 'belum_dibaca');
        } elseif ($filter === 'read') {
            $query->where('status_baca', 'dibaca');
        }

        // Paginate dan sertakan query string
        $notifikasi = $query->paginate(5)->withQueryString();

        return view('notifikasi.prestasi.index', compact(
            'notifikasi', 'totalAll', 'totalUnread', 'totalRead', 'filter'
        ));
    }

    public function show($id)
    {
        $notif = NotifikasiPrestasiModel::findOrFail($id);
        $notif->update(['status_baca' => 'dibaca']);
        return view('notifikasi.prestasi.show', compact('notif'));
    }

    public function markAsRead($id)
    {
        $notif = NotifikasiPrestasiModel::findOrFail($id);
        $notif->update(['status_baca' => 'dibaca']);
        return redirect()->back()->with('success', 'Notifikasi telah ditandai sebagai dibaca.');
    }

    public function destroy($id)
    {
        $notif = NotifikasiPrestasiModel::findOrFail($id);
        $notif->delete();
        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
    }
}
