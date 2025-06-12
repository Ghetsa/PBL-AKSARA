<?php

namespace App\Http\Controllers;

use App\Models\LombaModel; // Pastikan Anda sudah membuat model ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\PrestasiModel;

class LandingPageController extends Controller
{
    public function index()
    {
        // Ambil lomba yang sudah disetujui, punya poster, dan urutkan
        $lombas = LombaModel::where('status_verifikasi', 'disetujui')
            ->whereNotNull('poster')
            ->orderBy('created_at', 'desc') // Tampilkan yang terbaru
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

        // Ambil data prestasi terbaru yang sudah disetujui/valid
        $prestasiTerbaru = PrestasiModel::where('status_verifikasi', 'disetujui') // Sesuaikan dengan status verifikasi prestasi Anda
            ->orderBy('tahun', 'desc') // Atau field tanggal yang relevan
            ->get();

        // Jika Anda punya layout berbeda untuk landing page dan area login,
        // pastikan nama view yang dipanggil benar.
        return view('landing-page', ['lombas' => $lombasDenganPosterValid], compact('breadcrumb', 'prestasiTerbaru'));
        // Jika file blade Anda bernama welcome.blade.php, gunakan 'welcome'
        // return view('welcome', ['lombas' => $lombasDenganPosterValid]);
    }
}
