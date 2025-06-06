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
        $filter = $request->query('filter', 'semua');

        $validNotifications = collect([]); // Buat collection kosong sebagai default

        // ===================================================================
        // PERBAIKAN: Logika Pengambilan Data Berdasarkan Role
        // ===================================================================
        if ($user->role === 'admin' || $user->role === 'mahasiswa') {
            // Untuk admin dan mahasiswa, ambil semua jenis notifikasi
            $lombaNotifs = NotifikasiLombaModel::where('user_id', $user->user_id)->get();
            $prestasiNotifs = NotifikasiPrestasiModel::where('user_id', $user->user_id)->get();
            $keahlianNotifs = NotifikasiKeahlianModel::where('user_id', $user->user_id)->get();
            
            $mergedNotifications = collect([])->merge($lombaNotifs)->merge($prestasiNotifs)->merge($keahlianNotifs);
            $validNotifications = $mergedNotifications->sortByDesc('created_at')->filter(fn ($notif) => !empty($notif) && !empty($notif->id));

        } elseif ($user->role === 'dosen') {
            // KHUSUS UNTUK DOSEN: Hanya ambil notifikasi prestasi
            $validNotifications = NotifikasiPrestasiModel::where('user_id', $user->user_id)
                                    ->orderByDesc('created_at')
                                    ->get();
        }
        
        // Logika filter dan paginasi (tetap sama untuk semua role)
        $unreadCount = $validNotifications->where('status_baca', 'belum_dibaca')->count();
        $filteredNotifications = $validNotifications;
        if ($filter === 'belum_dibaca') {
            $filteredNotifications = $validNotifications->where('status_baca', 'belum_dibaca');
        } elseif ($filter === 'sudah_dibaca') {
            $filteredNotifications = $validNotifications->where('status_baca', 'dibaca');
        }
        $perPage = 10;
        $currentPage = Paginator::resolveCurrentPage('page');
        $currentPageItems = $filteredNotifications->slice(($currentPage - 1) * $perPage, $perPage)->values()->all();
        $allNotifications = new LengthAwarePaginator($currentPageItems, $filteredNotifications->count(), $perPage, $currentPage, [
            'path' => Paginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);
        
        // Logika pemilihan view (sudah benar)
        $viewName = 'notifikasi.mahasiswa.index';
        if ($user->role === 'admin') {
            $viewName = 'notifikasi.admin.index';
        } elseif ($user->role === 'dosen') {
            $viewName = 'notifikasi.dosen.index';
        }
        
        return view($viewName, compact('breadcrumb', 'activeMenu', 'allNotifications', 'filter', 'unreadCount'));
    }

    public function showAndRead($id, $modelAlias)
    {
        $breadcrumb = (object) ['title' => 'Detail Notifikasi', 'list' => ['Notifikasi', 'Detail']];
        $activeMenu = 'notifikasi';
        $user = Auth::user();
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

        if ($notif->user_id !== $user->user_id) {
            abort(403, 'Anda tidak memiliki akses ke notifikasi ini.');
        }

        $notif->update(['status_baca' => 'dibaca']);

        $viewName = 'notifikasi.mahasiswa.show';
        if ($user->role === 'admin') {
            $viewName = 'notifikasi.admin.show';
        } elseif ($user->role === 'dosen') {
            $viewName = 'notifikasi.dosen.show';
        }

        return view($viewName, compact('breadcrumb', 'activeMenu', 'notif'));
    }

    public function markAllAsRead()
    {
        $userId = Auth::id();
        NotifikasiLombaModel::where('user_id', $userId)->update(['status_baca' => 'dibaca']);
        NotifikasiPrestasiModel::where('user_id', $userId)->update(['status_baca' => 'dibaca']);
        NotifikasiKeahlianModel::where('user_id', $userId)->update(['status_baca' => 'dibaca']);

        // Gunakan redirect()->back() agar kembali ke halaman yang benar (admin, dosen, atau mahasiswa).
        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
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

        // Gunakan redirect()->back() agar kembali ke halaman yang benar sesuai role.
        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
    }
}