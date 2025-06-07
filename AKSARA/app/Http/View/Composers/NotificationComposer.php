<?php

// PASTIKAN NAMESPACE INI SAMA PERSIS DENGAN STRUKTUR FOLDER ANDA
namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\NotifikasiLombaModel;
use App\Models\NotifikasiPrestasiModel;
use App\Models\NotifikasiKeahlianModel;

// PASTIKAN NAMA CLASS INI SAMA DENGAN NAMA FILE
class NotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $recentNotifications = collect([]);
            $unreadNotificationCount = 0;

            $validNotifications = collect([]);

            if ($user->role === 'admin' || $user->role === 'mahasiswa') {
                $lombaNotifs = NotifikasiLombaModel::where('user_id', $user->user_id)->get();
                $prestasiNotifs = NotifikasiPrestasiModel::where('user_id', $user->user_id)->get();
                $keahlianNotifs = NotifikasiKeahlianModel::where('user_id', $user->user_id)->get();
                
                $merged = collect([])->merge($lombaNotifs)->merge($prestasiNotifs)->merge($keahlianNotifs);
                $validNotifications = $merged->sortByDesc('created_at')->filter(fn ($notif) => !empty($notif) && !empty($notif->id));
            
            } elseif ($user->role === 'dosen') {
                $validNotifications = NotifikasiPrestasiModel::where('user_id', $user->user_id)
                                        ->orderByDesc('created_at')
                                        ->get();
            }

            // Ambil 5 notifikasi terbaru untuk ditampilkan di dropdown
            $recentNotifications = $validNotifications->take(5);
            // Hitung semua notifikasi yang belum dibaca untuk badge
            $unreadNotificationCount = $validNotifications->where('status_baca', 'belum_dibaca')->count();

            $view->with('recentNotifications', $recentNotifications);
            $view->with('unreadNotificationCount', $unreadNotificationCount);
        }
    }
}