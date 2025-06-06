<?php

namespace App\Http\Controllers;

use App\Models\NotifikasiLombaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiLombaController extends Controller
{
    public function index(Request $request)
{
    $user = Auth::user();

    // Hitung jumlah untuk setiap kategori
    $totalAll    = $user->notifikasiLomba()->count();
    $totalUnread = $user->notifikasiLomba()->where('status_baca', 'belum_dibaca')->count();
    $totalRead   = $user->notifikasiLomba()->where('status_baca', 'dibaca')->count();

    $filter = $request->get('filter', 'all');

    // Bangun query dasar
    $query = $user->notifikasiLomba()
                  ->orderByDesc('created_at');

    // Terapkan filter
    if ($filter === 'unread') {
        $query->where('status_baca', 'belum_dibaca');
    } elseif ($filter === 'read') {
        $query->where('status_baca', 'dibaca');
    }

    // Paginate dan sertakan query string
    $notifikasi = $query->paginate(5)->withQueryString();

    return view('notifikasi.index', compact('activeMenu', 'allNotifications', 'totalAll', 'totalUnread', 'totalRead', 'filter'));

}


    public function show($id)
    {
        $notif = NotifikasiLombaModel::findOrFail($id);
        $notif->update(['status_baca' => 'dibaca']);
        return view('notifikasi.lomba.show', compact('notif'));
    }

    public function markAsRead($id)
    {
        $notif = NotifikasiLombaModel::findOrFail($id);
        $notif->update(['status_baca' => 'dibaca']);
        return redirect()->back()->with('success', 'Notifikasi telah ditandai sebagai dibaca.');
    }

    public function destroy($id)
    {
        $notif = NotifikasiLombaModel::findOrFail($id);
        $notif->delete();
        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
    }
}
