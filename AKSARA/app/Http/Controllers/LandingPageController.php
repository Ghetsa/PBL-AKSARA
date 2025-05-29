<?php

namespace App\Http\Controllers;

use App\Models\LombaModel; // Pastikan Anda sudah membuat model ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingPageController extends Controller
{
    public function index()
    {
        // Ambil lomba yang sudah disetujui, punya poster, dan urutkan
        $lombas = LombaModel::where('status_verifikasi', 'disetujui')
            ->whereNotNull('poster')
            ->orderBy('batas_pendaftaran', 'asc') // Tampilkan yang deadline-nya paling dekat
            ->take(6) // Ambil misalnya 6 lomba
            ->get();

        // Filter untuk memastikan file poster benar-benar ada di storage
        // Ini penting agar tidak ada broken image di frontend
        $lombasDenganPosterValid = $lombas->filter(function ($lomba) {
            return $lomba->poster && Storage::disk('public')->exists($lomba->poster);
        });

        $breadcrumb = (object) [
            'title' => 'Landing Page',
            'list' => ['User']
        ];

        // Jika Anda punya layout berbeda untuk landing page dan area login,
        // pastikan nama view yang dipanggil benar.
        return view('landing-page', ['lombas' => $lombasDenganPosterValid], compact('breadcrumb'));
        // Jika file blade Anda bernama welcome.blade.php, gunakan 'welcome'
        // return view('welcome', ['lombas' => $lombasDenganPosterValid]);
    }
}
