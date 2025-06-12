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
            ->take(6) // Ambil hanya 6 lomba
            ->get();

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

        return view('landing-page', ['lombas' => $lombasDenganPosterValid], compact('breadcrumb', 'prestasiTerbaru'));
    }
}
