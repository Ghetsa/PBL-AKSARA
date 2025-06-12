<?php
namespace App\Http\Controllers;
use App\Models\DosenModel;
use App\Models\LombaModel;
use App\Models\MahasiswaBimbinganModel;
use App\Models\PrestasiModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DashboardController extends Controller
{
    // Method MOORA (disederhanakan, idealnya di service terpisah)
    private function calculateMooraScores($userId)
    {
        $user = UserModel::with(['keahlian', 'bidang'])->find($userId); // Sesuaikan nama relasi
        if (!$user) {
            return collect();
        }

        $userMinatIds = $user->keahlian->pluck('bidang_id')->toArray();
        $userKeahlianIds = $user->bidang->pluck('bidang_id')->toArray();

        // Ambil lomba yang masih buka atau akan datang
        $lombas = LombaModel::with(['bidangKeahlian.bidang']) // Relasi untuk mendapatkan bidang dari LombaDetailModel
            ->where('status_verifikasi', 'disetujui')
            ->where(function ($query) {
                $query->where('batas_pendaftaran', '>=', Carbon::now()->toDateString())
                    ->orWhereNull('batas_pendaftaran');
            })
            ->get();

        if ($lombas->isEmpty()) {
            return collect();
        }

        $dataMatrix = [];
        foreach ($lombas as $lomba) {
            $row = [];
            $lombaBidangIds = $lomba->bidangKeahlian->pluck('bidang.bidang_id')->filter()->toArray(); // Ambil ID bidang dari relasi

            // Kriteria 1: Kesesuaian Minat (Bobot: 0.3)
            $row['minat'] = count(array_intersect($lombaBidangIds, $userMinatIds)) > 0 ? 1 : 0;

            // Kriteria 2: Kesesuaian Keahlian (Bobot: 0.3)
            $row['keahlian'] = count(array_intersect($lombaBidangIds, $userKeahlianIds)) > 0 ? 1 : 0;

            // Kriteria 3: Tingkat Lomba (Bobot: 0.2)
            $row['tingkat'] = match (strtolower($lomba->tingkat ?? '')) {
                'lokal' => 1,
                'kota' => 2,
                'kabupaten' => 2,
                'provinsi' => 3,
                'nasional' => 4,
                'internasional' => 5,
                default => 0,
            };

            // Kriteria 4: Biaya (Cost, Bobot: 0.1) - normalisasi terbalik atau nilai negatif
            // Skor biaya: 5 (gratis) -> 1 (mahal). Ini contoh sederhana, bisa lebih kompleks.
            if ($lomba->biaya == 0) $row['biaya_score'] = 5;
            elseif ($lomba->biaya <= 50000) $row['biaya_score'] = 4;
            elseif ($lomba->biaya <= 100000) $row['biaya_score'] = 3;
            elseif ($lomba->biaya <= 200000) $row['biaya_score'] = 2;
            else $row['biaya_score'] = 1;

            // Kriteria 5: Sisa Waktu Pendaftaran (Bobot: 0.1)
            if ($lomba->batas_pendaftaran) {
                $sisaHari = Carbon::now()->diffInDays(Carbon::parse($lomba->batas_pendaftaran), false);
                if ($sisaHari < 0) $row['sisa_waktu'] = 0; // Tutup
                elseif ($sisaHari <= 7) $row['sisa_waktu'] = 3; // Mendesak
                elseif ($sisaHari <= 30) $row['sisa_waktu'] = 4; // Cukup
                else $row['sisa_waktu'] = 5; // Lama
            } else {
                $row['sisa_waktu'] = 5; // Tanpa batas waktu
            }

            $dataMatrix[] = ['lomba' => $lomba, 'values' => $row];
        }

        // Normalisasi dan Perhitungan MOORA (Sederhana)
        // Divisor (akar dari jumlah kuadrat)
        $divisors = [];
        $criteriaForMoora = ['minat', 'keahlian', 'tingkat', 'biaya_score', 'sisa_waktu'];
        foreach ($criteriaForMoora as $c) {
            $sumOfSquares = array_sum(array_map(fn($data) => pow($data['values'][$c], 2), $dataMatrix));
            $divisors[$c] = $sumOfSquares > 0 ? sqrt($sumOfSquares) : 1;
        }

        $results = [];
        $weights = ['minat' => 0.3, 'keahlian' => 0.3, 'tingkat' => 0.2, 'biaya_score' => 0.1, 'sisa_waktu' => 0.1];

        foreach ($dataMatrix as $item) {
            $optimasi = 0;
            foreach ($criteriaForMoora as $c) {
                $normalizedValue = $divisors[$c] != 0 ? $item['values'][$c] / $divisors[$c] : 0;
                // Kriteria biaya adalah cost, yang lain benefit (dalam contoh sederhana ini, biaya_score sudah diubah jadi benefit)
                $optimasi += $normalizedValue * $weights[$c];
            }
            $results[] = ['lomba' => $item['lomba'], 'score' => round($optimasi, 4)];
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
        return collect($results)->pluck('lomba'); // Mengembalikan koleksi LombaModel yang sudah diurutkan
    }

    public function mahasiswaDashboard()
    {
        $breadcrumb = (object) ['title' => 'Dashboard Mahasiswa', 'list' => ['Dashboard']];
        $activeMenu = 'dashboard';
        $user = Auth::user();

        // Prestasi semua user (yang sudah disetujui admin) - contoh ambil 5 terbaru
        $prestasiPublik = PrestasiModel::where('status_verifikasi', 'disetujui') // Sesuaikan status
            ->orderBy('tahun', 'desc')
            ->take(5)
            ->get();

        // Prestasi mahasiswa terkait
        $prestasiMahasiswa = PrestasiModel::where('mahasiswa_id', $user->mahasiswa->mahasiswa_id) // Asumsi ada relasi mahasiswa di UserModel
            ->orderBy('tahun', 'desc')
            ->take(5)
            ->get();

        // Lomba yang direkomendasikan (menggunakan MOORA sederhana)
        // Anda mungkin ingin mengambil lebih banyak dan melakukan pagination di view
        $rekomendasiLomba = $this->calculateMooraScores($user->user_id)->take(5);


        // Lomba umum (yang masih buka dan disetujui)
        $lombaUmum = LombaModel::where('status_verifikasi', 'disetujui')
            ->where(function ($query) {
                $query->where('batas_pendaftaran', '>=', Carbon::now()->toDateString())
                    ->orWhereNull('batas_pendaftaran');
            })
            ->orderBy('batas_pendaftaran', 'asc')
            ->take(5)
            ->get();


        return view('dashboard.mahasiswa', compact('breadcrumb', 'activeMenu', 'prestasiPublik', 'prestasiMahasiswa', 'rekomendasiLomba', 'lombaUmum', 'user'));
    }

    public function adminDashboard()
    {
        $breadcrumb = (object) ['title' => 'Dashboard Admin', 'list' => ['Dashboard']];
        $activeMenu = 'dashboard';

        // Statistik untuk Widgets
        $totalPrestasi = PrestasiModel::count();
        $prestasiDisetujui = PrestasiModel::where('status_verifikasi', 'disetujui')->count();
        $prestasiPending = PrestasiModel::where('status_verifikasi', 'pending')->count();
        $totalLomba = LombaModel::count();
        $lombaAktif = LombaModel::where('status_verifikasi', 'disetujui')
            ->where(function ($query) {
                $query->where('batas_pendaftaran', '>=', Carbon::now()->toDateString())
                      ->orWhereNull('batas_pendaftaran');
            })->count();
        $lombaPengajuanPending = LombaModel::where('status_verifikasi', 'pending')->count();
        $totalUser = UserModel::count();
        $totalMahasiswa = UserModel::where('role', 'mahasiswa')->count();
        $totalDosen = UserModel::where('role', 'dosen')->count();

        // Data untuk Grafik
        $lombaByTingkat = LombaModel::select('tingkat', DB::raw('count(*) as total'))
            ->whereNotNull('tingkat')->where('tingkat', '!=', '')->groupBy('tingkat')->pluck('total', 'tingkat');
        $prestasiByTingkat = PrestasiModel::where('status_verifikasi', 'disetujui')
            ->select('tingkat', DB::raw('count(*) as total'))
            ->whereNotNull('tingkat')->where('tingkat', '!=', '')->groupBy('tingkat')->pluck('total', 'tingkat');

        $lombaPerBulan = LombaModel::select(
                DB::raw('YEAR(pembukaan_pendaftaran) as year, MONTH(pembukaan_pendaftaran) as month'),
                DB::raw('COUNT(*) as total')
            )->where('status_verifikasi', 'disetujui')
            ->where('pembukaan_pendaftaran', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')->orderBy('year', 'asc')->orderBy('month', 'asc')->get();

        $labelsBulan = [];
        $dataBulan = [];
        for ($i = 5; $i >= -6; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labelsBulan[$date->format('Y-n')] = $date->format('M Y');
            $dataBulan[$date->format('Y-n')] = 0;
        }
        foreach ($lombaPerBulan as $item) {
            $key = $item->year . '-' . $item->month;
            if (isset($dataBulan[$key])) {
                $dataBulan[$key] = $item->total;
            }
        }

        // Data untuk List Card
        $infoLombaTerbaru = LombaModel::where('status_verifikasi', 'disetujui')
            ->orderBy('created_at', 'desc')->take(6)->get();

        // [PERBAIKAN] Menghapus orderBy('created_at') karena kolom tidak ada di tabel 'prestasi'
        $prestasiKeseluruhanTerbaru = PrestasiModel::where('status_verifikasi', 'disetujui')
            ->with(['mahasiswa.user', 'mahasiswa.prodi'])
            ->orderBy('tahun', 'desc')
            ->take(6)->get();

        return view('dashboard.admin', compact(
            'breadcrumb', 'activeMenu', 'totalPrestasi', 'prestasiDisetujui', 'prestasiPending',
            'totalLomba', 'lombaAktif', 'lombaPengajuanPending', 'totalUser', 'totalMahasiswa', 'totalDosen',
            'lombaByTingkat', 'prestasiByTingkat', 'labelsBulan', 'dataBulan',
            'infoLombaTerbaru', 'prestasiKeseluruhanTerbaru'
        ));
    }

    public function dosenDashboard()
    {
        $breadcrumb = (object) ['title' => 'Dashboard Dosen', 'list' => ['Dashboard']];
        $activeMenu = 'dashboard';
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->route('home')->with('error', 'Data dosen tidak ditemukan.');
        }

        // Data untuk Kartu Statistik
        $jumlahMahasiswaBimbingan = PrestasiModel::where('dosen_id', $dosen->dosen_id)
            ->where('status_verifikasi', 'disetujui')->distinct('mahasiswa_id')->count('mahasiswa_id');
        $jumlahPrestasiKeseluruhan = PrestasiModel::where('status_verifikasi', 'disetujui')->count();
        $jumlahLombaDisetujui = LombaModel::where('status_verifikasi', 'disetujui')->count();

        // Data untuk List Card
        $infoLombaTerbaru = LombaModel::where('status_verifikasi', 'disetujui')
            ->where(function ($query) {
                $query->where('batas_pendaftaran', '>=', Carbon::now()->toDateString())
                      ->orWhereNull('batas_pendaftaran');
            })
            ->orderBy('created_at', 'desc')->take(3)->get();

        // [PERBAIKAN] Menghapus orderBy('created_at') karena kolom tidak ada di tabel 'prestasi'
        $prestasiBimbingan = PrestasiModel::where('dosen_id', $dosen->dosen_id)
            ->where('status_verifikasi', 'disetujui')
            ->with(['mahasiswa.user', 'mahasiswa.prodi'])
            ->orderBy('tahun', 'desc')
            ->take(3)->get();

        // [PERBAIKAN] Menghapus orderBy('created_at') karena kolom tidak ada di tabel 'prestasi'
        $prestasiKeseluruhan = PrestasiModel::where('status_verifikasi', 'disetujui')
            ->with(['mahasiswa.user', 'mahasiswa.prodi'])
            ->orderBy('tahun', 'desc')
            ->take(3)->get();

        return view('dashboard.dosen', compact(
            'breadcrumb', 'activeMenu', 'user', 'dosen', 'jumlahMahasiswaBimbingan',
            'jumlahPrestasiKeseluruhan', 'jumlahLombaDisetujui', 'infoLombaTerbaru',
            'prestasiBimbingan', 'prestasiKeseluruhan'
        ));
    }
}
