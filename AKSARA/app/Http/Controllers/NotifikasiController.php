<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\NotifikasiLombaModel;
use App\Models\NotifikasiPrestasiModel;
use App\Models\NotifikasiKeahlianModel;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $breadcrumb = (object) ['title' => 'Notifikasi', 'list' => ['Notifikasi']];
        $activeMenu = 'notifikasi';
        $user = Auth::user();
        $filter = $request->query('filter', 'semua'); // Ambil filter dari URL

        // Ambil semua notifikasi terlebih dahulu
        $lombaNotifs = NotifikasiLombaModel::where('user_id', $user->user_id)->get();
        $prestasiNotifs = NotifikasiPrestasiModel::where('user_id', $user->user_id)->get();
        $keahlianNotifs = NotifikasiKeahlianModel::where('user_id', $user->user_id)->get();

        $mergedNotifications = collect([])->merge($lombaNotifs)->merge($prestasiNotifs)->merge($keahlianNotifs);
        $allNotificationsSorted = $mergedNotifications->sortByDesc('created_at');

        $validNotifications = $allNotificationsSorted->filter(fn ($notif) => !empty($notif) && !empty($notif->id));
        
        // Hitung notifikasi belum dibaca SEBELUM di-filter
        $unreadCount = $validNotifications->where('status_baca', 'belum_dibaca')->count();

        // Terapkan filter status
        $filteredNotifications = $validNotifications;
        if ($filter === 'belum_dibaca') {
            $filteredNotifications = $validNotifications->where('status_baca', 'belum_dibaca');
        } elseif ($filter === 'sudah_dibaca') {
            $filteredNotifications = $validNotifications->where('status_baca', 'dibaca');
        }

        // Buat Paginator dari data yang sudah difilter
        $perPage = 10;
        $currentPage = Paginator::resolveCurrentPage('page');
        $currentPageItems = $filteredNotifications->slice(($currentPage - 1) * $perPage, $perPage)->values()->all();
        $allNotifications = new LengthAwarePaginator($currentPageItems, $filteredNotifications->count(), $perPage, $currentPage, [
            'path' => Paginator::resolveCurrentPath(),
            'query' => $request->query(), // Penting agar filter tetap ada saat pindah halaman
        ]);

        return view('notifikasi.index', compact('breadcrumb', 'activeMenu', 'allNotifications', 'filter', 'unreadCount'));
    }

    public function showAndRead($id, $modelAlias)
    {
        $breadcrumb = (object) ['title' => 'Detail Notifikasi', 'list' => ['Notifikasi', 'Detail']];
        $activeMenu = 'notifikasi';
        $notif = null;

        if ($modelAlias === 'lomba') {
            $notif = NotifikasiLombaModel::findOrFail($id);
        } elseif ($modelAlias === 'prestasi') {
            $notif = NotifikasiPrestasiModel::findOrFail($id);
        } elseif ($modelAlias === 'keahlian') {
            $notif = NotifikasiKeahlianModel::findOrFail($id);
        } else {
            abort(404);
        }

        // Tandai sebagai dibaca
        $notif->update(['status_baca' => 'dibaca']);

        // Tampilkan view detail
        return view('notifikasi.show', compact('breadcrumb', 'activeMenu', 'notif'));
    }

    public function markAllAsRead()
    {
        $userId = Auth::id();
        NotifikasiLombaModel::where('user_id', $userId)->update(['status_baca' => 'dibaca']);
        NotifikasiPrestasiModel::where('user_id', $userId)->update(['status_baca' => 'dibaca']);
        NotifikasiKeahlianModel::where('user_id', $userId)->update(['status_baca' => 'dibaca']);

        return redirect()->route('notifikasi.index')->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    public function destroy($id, $modelAlias)
    {
        $notif = null;
        if ($modelAlias === 'lomba') {
            $notif = NotifikasiLombaModel::findOrFail($id);
        } elseif ($modelAlias === 'prestasi') {
            $notif = NotifikasiPrestasiModel::findOrFail($id);
        } elseif ($modelAlias === 'keahlian') {
            $notif = NotifikasiKeahlianModel::findOrFail($id);
        } else {
            abort(404);
        }
        
        $notif->delete();

        return redirect()->route('notifikasi.index')->with('success', 'Notifikasi berhasil dihapus.');
    }
}