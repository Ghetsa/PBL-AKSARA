<?php

namespace App\Http\Controllers;

use App\Models\SliderKriteriaModel;
use App\Models\UserModel;
use App\Models\LombaModel;
use App\Models\BidangModel;
use App\Models\LombaDetailModel;
use App\Models\LombaHadiahModel;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // Untuk logging
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LombaController extends Controller
{


    /**
     * Method AJAX untuk mengambil detail perhitungan MOORA satu lomba.
     * Akan dipanggil ketika tombol "btn-detail-hitungan" ditekan.
     */
    public function showMooraDetails(Request $request, $id) // WAJIB ada $id
    {
        $userId = Auth::id();

        // Ambil bobot kustom dari request, jika tidak ada, gunakan array kosong
        $customWeights = $request->input('weights', []);

        // Panggil fungsi utama calculateMooraScores untuk mendapatkan semua hasil.
        // Kemudian filter untuk lomba spesifik ini.
        $allMooraResults = $this->calculateMooraScores($userId, $customWeights);

        // Filter hasil untuk lomba_id yang spesifik
        $results = array_filter($allMooraResults, function ($result) use ($id) {
            return $result['lomba']->lomba_id == $id;
        });

        // Pastikan array di-re-index setelah filtering
        $results = array_values($results);

        // Jika tidak ada hasil untuk lomba spesifik atau jika user tidak login
        if (empty($results)) {
            // Mengembalikan HTML untuk pesan kosong/error di modal
            return '<div class="modal-header bg-warning text-white"><h5 class="modal-title">Detail Perhitungan MOORA</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p class="text-warning">Detail perhitungan tidak ditemukan untuk lomba ini atau data tidak tersedia.</p></div>';
        }

        // Ekstrak data global dari hasil lomba yang pertama (yang seharusnya hanya ada satu sekarang)
        $globalData = [
            'weights' => $results[0]['weights'],
            'divisors' => $results[0]['divisors'],
            'criteria' => array_keys($results[0]['original_values']),
            'benefit_criteria' => ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan'],
            'cost_criteria' => ['biaya'],
        ];

        // Siapkan data untuk view
        $breadcrumb = (object) ['title' => 'Detail Perhitungan MOORA', 'list' => ['Info Lomba', 'Detail Perhitungan']];
        $activeMenu = 'info_lomba';

        // Mengembalikan view Blade yang berisi detail perhitungan untuk satu lomba
        return view('lomba.mahasiswa.moora_details', compact('breadcrumb', 'activeMenu', 'results', 'globalData'));
    }

    /**
     * Menampilkan detail perhitungan MOORA untuk SEMUA LOMBA di halaman baru.
     * Dipanggil oleh tombol "Lihat Detail Lengkap".
     */
    public function showAllMooraDetails(Request $request)
    {
        $userId = Auth::id();

        // Ambil bobot kustom dari request, jika tidak ada, gunakan array kosong
        $customWeights = $request->input('weights', []);

        // Panggil fungsi utama yang sudah ada untuk mendapatkan hasil perhitungan
        $results = $this->calculateMooraScores($userId, $customWeights);

        // Jika tidak ada hasil (misal, tidak ada lomba), tampilkan pesan
        if (empty($results)) {
            return view('lomba.mahasiswa.moora_details_empty')
                ->with('breadcrumb', (object) ['title' => 'Detail Perhitungan MOORA', 'list' => ['Info Lomba', 'Detail Perhitungan']])
                ->with('activeMenu', 'info_lomba');
        }

        // Ekstrak data global dari hasil pertama (karena sama untuk semua)
        $globalData = [
            'weights' => $results[0]['weights'],
            'divisors' => $results[0]['divisors'],
            'criteria' => array_keys($results[0]['original_values']),
            'benefit_criteria' => ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan'],
            'cost_criteria' => ['biaya'],
        ];

        // Siapkan data untuk view
        $breadcrumb = (object) ['title' => 'Detail Perhitungan MOORA', 'list' => ['Info Lomba', 'Detail Perhitungan']];
        $activeMenu = 'info_lomba';

        return view('lomba.mahasiswa.moora_details', compact('breadcrumb', 'activeMenu', 'results', 'globalData'));
    }

    // Fungsi ini akan dipanggil oleh DataTables di view lomba/index.blade.php
    public function getList(Request $request)
    {
        // 1. Query dasar untuk mengambil lomba yang sudah disetujui beserta relasinya.
        $query = LombaModel::query()
            ->with([
                'bidangKeahlian.bidang', // Untuk menampilkan bidang lomba
                'daftarHadiah'           // Untuk kriteria hadiah
            ])
            ->where('status_verifikasi', 'disetujui'); // Hanya lomba yang disetujui

        $mooraScoresMap = [];
        $isRekomendasiMode = $request->rekomendasi === '1';

        // 2. Cek apakah pengguna meminta mode rekomendasi.
        if ($isRekomendasiMode) {
            $userId = Auth::id();
            if ($userId) {
                // Ambil bobot dari request yang dikirim oleh JavaScript
                $customWeightsInput = $request->input('weights', []);
                $customWeights = [];
                $criteriaKeys = ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan', 'biaya'];
                
                // Validasi dan siapkan bobot untuk kalkulasi
                foreach ($criteriaKeys as $key) {
                    if (isset($customWeightsInput[$key]) && is_numeric($customWeightsInput[$key])) {
                        $customWeights[$key] = (float) $customWeightsInput[$key];
                    }
                }
                
                // 3. Simpan atau perbarui preferensi bobot pengguna ke database.
                //    Ini berjalan setiap kali pengguna menekan "Terapkan & Lihat Rekomendasi".
                if (!empty($customWeights)) {
                    // Ubah format dari desimal (0.25) ke persentase (25) untuk disimpan
                    $weightsToSave = [];
                    foreach ($customWeights as $key => $value) {
                        $weightsToSave[$key] = round($value * 100);
                    }

                    try {
                        // Gunakan updateOrCreate: jika user_id sudah ada, update; jika belum, buat baru.
                        SliderKriteriaModel::updateOrCreate(
                            ['user_id' => $userId], // Kunci pencarian
                            $weightsToSave          // Data yang akan disimpan/diupdate
                        );
                    } catch (\Exception $e) {
                        // Jika gagal, catat error tapi jangan hentikan proses.
                        Log::error('Gagal menyimpan bobot slider untuk user_id: ' . $userId . ' - ' . $e->getMessage());
                    }
                }

                // 4. Hitung skor MOORA untuk semua lomba berdasarkan bobot pengguna.
                $mooraResults = $this->calculateMooraScores($userId, $customWeights);

                // Siapkan ID lomba yang sudah terurut untuk query
                $orderedLombaIds = [];
                foreach ($mooraResults as $result) {
                    $mooraScoresMap[$result['lomba']->lomba_id] = $result['score'];
                    $orderedLombaIds[] = $result['lomba']->lomba_id;
                }

                // Jika tidak ada hasil, kembalikan data kosong.
                if (empty($orderedLombaIds)) {
                    return DataTables::of(collect())->addIndexColumn()->rawColumns(['status_display', 'aksi', 'biaya_display'])->make(true);
                }

                // 5. Terapkan urutan kustom ke query utama.
                $query->whereIn('lomba_id', $orderedLombaIds)
                      ->orderByRaw("FIELD(lomba_id, " . implode(',', array_map('intval', $orderedLombaIds)) . ")");

            } else {
                // Jika user tidak login dalam mode rekomendasi, kembalikan data kosong.
                return DataTables::of(collect())->addIndexColumn()->rawColumns(['status_display', 'aksi', 'biaya_display'])->make(true);
            }
        } else {
            // Ini adalah mode standar (tanpa rekomendasi), lakukan filter biasa.
            if ($request->filled('tingkat_lomba_filter')) {
                $query->where('tingkat', $request->tingkat_lomba_filter);
            }
            if ($request->filled('kategori_lomba_filter')) {
                $query->where('kategori', $request->kategori_lomba_filter);
            }

            if ($request->filled('filter_status')) {
                $status = strtolower($request->filter_status);
                $today = Carbon::now('Asia/Jakarta')->startOfDay();

                if ($status == 'buka') {
                    $query->where(function ($q) use ($today) {
                        $q->whereNull('pembukaan_pendaftaran')->orWhereDate('pembukaan_pendaftaran', '<=', $today);
                    })->where(function ($q) use ($today) {
                        $q->whereNull('batas_pendaftaran')->orWhereDate('batas_pendaftaran', '>=', $today);
                    });
                } elseif ($status == 'tutup') {
                    $query->whereNotNull('batas_pendaftaran')->whereDate('batas_pendaftaran', '<', $today);
                } elseif ($status == 'segera hadir') {
                    $query->whereNotNull('pembukaan_pendaftaran')->whereDate('pembukaan_pendaftaran', '>', $today);
                }
            }
            $query->orderBy('batas_pendaftaran', 'asc');
        }

        // 6. Buat dan kembalikan response DataTables.
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('bidang_display', function ($lomba) {
                if ($lomba->bidangKeahlian->isNotEmpty()) {
                    return $lomba->bidangKeahlian->map(fn($detail) => $detail->bidang->bidang_nama ?? '')->filter()->implode(', ');
                }
                return '-';
            })
            ->editColumn('batas_pendaftaran', function ($lomba) {
                return $lomba->batas_pendaftaran ? Carbon::parse($lomba->batas_pendaftaran)->isoFormat('D MMMM YYYY') : 'N/A';
            })
            ->addColumn('biaya_display', function ($lomba) {
                return $lomba->biaya > 0 ? 'Rp ' . number_format($lomba->biaya, 0, ',', '.') : '<span class="badge bg-light-success text-success px-2 py-1">Gratis</span>';
            })
            ->addColumn('status_display', function ($lomba) {
                $statusDisplay = $lomba->status_pendaftaran_display;
                $badgeClass = match (strtolower($statusDisplay)) {
                    'buka' => 'success',
                    'tutup' => 'danger',
                    'segera hadir' => 'warning',
                    default => 'secondary'
                };
                return '<span class="badge bg-light-' . $badgeClass . ' text-' . $badgeClass . ' px-2 py-1">' . e(ucfirst($statusDisplay)) . '</span>';
            })
            ->addColumn('moora_score', function ($lomba) use ($isRekomendasiMode, $mooraScoresMap) {
                return $isRekomendasiMode && isset($mooraScoresMap[$lomba->lomba_id])
                    ? number_format($mooraScoresMap[$lomba->lomba_id], 4)
                    : '-';
            })
            ->addColumn('aksi', function ($lomba) {
                $btnDetail = '<button onclick="modalActionLomba(\'' . route('lomba.publik.show_ajax', $lomba->lomba_id) . '\', \'Detail Lomba\', \'modalDetailLombaPublik\')" class="btn btn-sm btn-outline-primary me-2" title="Lihat Detail"><i class="fas fa-eye me-1"></i></button>';
                $btnHitung = '<button class="btn btn-sm btn-outline-secondary btn-detail-hitungan" data-lomba-id="' . $lomba->lomba_id . '" title="Lihat Perhitungan"><i class="fas fa-calculator me-1"></i></button>';
                return '<div class="btn-group">' . $btnDetail . $btnHitung . '</div>';
            })
            ->rawColumns(['status_display', 'aksi', 'biaya_display'])
            ->make(true);
    }

    /**
     * [FUNGSI BARU] Menghitung skor hadiah berdasarkan deskripsi teks.
     */
    private function calculatePrizeScore(\Illuminate\Support\Collection $daftarHadiah): int
    {
        if ($daftarHadiah->isEmpty()) {
            return 0;
        }

        $totalScore = 0;

        foreach ($daftarHadiah as $itemHadiah) {
            $prizeString = strtolower($itemHadiah->hadiah);
            $currentPrizeScore = 0;

            // 1. Skoring berdasarkan kata kunci dengan skala satuan
            if (str_contains($prizeString, 'beasiswa')) $currentPrizeScore += 5;
            if (str_contains($prizeString, 'magang') || str_contains($prizeString, 'internship') || str_contains($prizeString, 'proyek')) $currentPrizeScore += 4;
            if (str_contains($prizeString, 'medali') || str_contains($prizeString, 'piala') || str_contains($prizeString, 'trophy')) $currentPrizeScore += 3;
            if (str_contains($prizeString, 'sertifikat')) $currentPrizeScore += 2;

            // 2. Ekstraksi dan skoring berdasarkan nilai uang dengan skala satuan
            preg_match('/(\d[\d.,]*)\s*(juta|jt|ribu|rb|k)/i', $prizeString, $matches);

            $moneyValue = 0;
            if (!empty($matches)) {
                $number = (float) str_replace([',', '.'], '', $matches[1]);
                $unit = strtolower($matches[2] ?? '');
                if (in_array($unit, ['juta', 'jt'])) $moneyValue = $number * 1000000;
                elseif (in_array($unit, ['ribu', 'rb', 'k'])) $moneyValue = $number * 1000;
            } else {
                preg_match('/(rp\s*|idr\s*)?(\d[\d.,]*)/i', $prizeString, $plainMatches);
                if (!empty($plainMatches[2])) {
                    $moneyValue = (float) str_replace(['.', ','], '', $plainMatches[2]);
                }
            }

            // Tambahkan poin berdasarkan tingkatan nominal uang dengan skala satuan
            if ($moneyValue > 0) {
                $currentPrizeScore += 1; // Poin dasar karena ada hadiah uang
                if ($moneyValue > 10000000) $currentPrizeScore += 8;
                elseif ($moneyValue > 5000000) $currentPrizeScore += 7;
                elseif ($moneyValue > 1000000) $currentPrizeScore += 6;
                elseif ($moneyValue >= 500000) $currentPrizeScore += 5;
                elseif ($moneyValue >= 100000) $currentPrizeScore += 4;
            } elseif (str_contains($prizeString, 'uang')) {
                $currentPrizeScore += 2; // Skor 2, sama seperti sertifikat
            }

            $totalScore += $currentPrizeScore;
        }

        return (int) round($totalScore); // Mengembalikan sebagai integer
    }

    // private function calculatePrizeScore(Collection $daftarHadiah): int
    // {
    //     if ($daftarHadiah->isEmpty()) {
    //         return 0;
    //     }

    //     $totalScore = 0;

    //     foreach ($daftarHadiah as $itemHadiah) {
    //         $prizeString = strtolower($itemHadiah->hadiah);
    //         $currentPrizeScore = 0;

    //         // 1. Skoring berdasarkan kata kunci
    //         if (str_contains($prizeString, 'beasiswa')) $currentPrizeScore += 50;
    //         if (str_contains($prizeString, 'magang') || str_contains($prizeString, 'internship')) $currentPrizeScore += 30;
    //         if (str_contains($prizeString, 'pengalaman') || str_contains($prizeString, 'proyek')) $currentPrizeScore += 25;
    //         if (str_contains($prizeString, 'medali') || str_contains($prizeString, 'piala') || str_contains($prizeString, 'trophy')) $currentPrizeScore += 15;
    //         if (str_contains($prizeString, 'sertifikat')) $currentPrizeScore += 5;

    //         // 2. Ekstraksi dan skoring berdasarkan nilai uang
    //         preg_match('/(\d[\d.,]*)\s*(juta|jt|ribu|rb|k)/i', $prizeString, $matches);

    //         $moneyValue = 0;
    //         if (!empty($matches)) {
    //             $number = (float) str_replace([',', '.'], '', $matches[1]);
    //             $unit = strtolower($matches[2] ?? '');
    //             if (in_array($unit, ['juta', 'jt'])) $moneyValue = $number * 1000000;
    //             elseif (in_array($unit, ['ribu', 'rb', 'k'])) $moneyValue = $number * 1000;
    //         } else {
    //             preg_match('/(rp\s*|idr\s*)?(\d[\d.,]*)/i', $prizeString, $plainMatches);
    //             if (!empty($plainMatches[2])) {
    //                 $moneyValue = (float) str_replace(['.', ','], '', $plainMatches[2]);
    //             }
    //         }

    //         // Tambahkan poin berdasarkan tingkatan nominal uang
    //         if ($moneyValue > 0) {
    //             $currentPrizeScore += 5; // Poin dasar karena ada hadiah uang
    //             if ($moneyValue > 10000000) $currentPrizeScore += 80;
    //             elseif ($moneyValue > 5000000) $currentPrizeScore += 60;
    //             elseif ($moneyValue > 1000000) $currentPrizeScore += 40;
    //             elseif ($moneyValue >= 500000) $currentPrizeScore += 20;
    //             elseif ($moneyValue >= 100000) $currentPrizeScore += 10;
    //         }

    //         $totalScore += $currentPrizeScore;
    //     }

    //     // 3. Tambahkan bonus kecil berdasarkan jumlah item hadiah
    //     $totalScore += ($daftarHadiah->count() * 2);

    //     return $totalScore;
    // }

    private function calculateMooraScores($userId, $customWeights = [])
    {
        $user = UserModel::with(['minat', 'keahlian'])->find($userId);
        if (!$user) {
            return [];
        }

        $userMinatIds = $user->minat->pluck('bidang_id')->toArray();
        $userKeahlianIds = $user->keahlian->pluck('bidang_id')->toArray();

        $lombas = LombaModel::with(['bidangKeahlian.bidang', 'daftarHadiah'])
            ->where('status_verifikasi', 'disetujui')
            ->where(function ($query) {
                $query->where('batas_pendaftaran', '>=', Carbon::now()->toDateString())
                    ->orWhereNull('batas_pendaftaran');
            })
            ->get();

        if ($lombas->isEmpty()) {
            return [];
        }

        $dataMatrix = [];
        foreach ($lombas as $lomba) {
            $row = [];
            $lombaBidangIds = $lomba->bidangKeahlian->pluck('bidang.bidang_id')->filter()->toArray();

            $row['minat'] = count(array_intersect($lombaBidangIds, $userMinatIds)) > 0 ? 1 : 0; // Skor biner
            $row['keahlian'] = count(array_intersect($lombaBidangIds, $userKeahlianIds)) > 0 ? 1 : 0; // Skor biner
            $row['tingkat'] = match (strtolower($lomba->tingkat ?? '')) {
                'lokal' => 1,
                'kota' => 2,
                'kabupaten' => 2,
                'provinsi' => 3,
                'nasional' => 4,
                'internasional' => 5,
                default => 0,
            };
            // Kriteria Hadiah: sederhananya, jumlah jenis hadiah. Bisa lebih kompleks.
            // $row['hadiah'] = $lomba->daftarHadiah->count() > 0 ? ($lomba->daftarHadiah->count() <= 5 ? $lomba->daftarHadiah->count() : 5) : 0; // Max skor 5
            $row['hadiah'] = $this->calculatePrizeScore($lomba->daftarHadiah);

            if ($lomba->batas_pendaftaran) {
                $sisaHari = Carbon::now()->diffInDays(Carbon::parse($lomba->batas_pendaftaran), false);
                if ($sisaHari < 0)
                    $row['penutupan'] = 0;      // Sudah tutup
                elseif ($sisaHari == 0)
                    $row['penutupan'] = 1; // Tutup hari ini
                elseif ($sisaHari <= 7)
                    $row['penutupan'] = 2;  // <= 1 minggu
                elseif ($sisaHari <= 14)
                    $row['penutupan'] = 3; // <= 2 minggu
                elseif ($sisaHari <= 30)
                    $row['penutupan'] = 4; // <= 1 bulan
                else
                    $row['penutupan'] = 5;                        // > 1 bulan
            } else {
                $row['penutupan'] = 5; // Tanpa batas, dianggap paling fleksibel
            }
            // Kriteria Biaya (Cost) - nilai asli
            $row['biaya'] = (float) ($lomba->biaya ?? 0);

            $dataMatrix[] = ['lomba' => $lomba, 'values' => $row];
        }
        return $this->processMooraNormalization($dataMatrix, $customWeights);
    }

    private function processMooraNormalization($dataMatrix, $customWeights = [])
    {
        $criteria = ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan', 'biaya'];
        $defaultWeights = [
            'minat' => 0.25,
            'keahlian' => 0.25,
            'tingkat' => 0.15,
            'hadiah' => 0.10,
            'penutupan' => 0.15,
            'biaya' => 0.10
        ];

        $weights = $defaultWeights;
        if (!empty($customWeights) && count(array_intersect_key($customWeights, $defaultWeights)) === count($defaultWeights)) {
            $totalCustomWeight = array_sum($customWeights);
            // Cek apakah total bobot mendekati 1.0 (karena dari JS sudah dinormalisasi)
            if (abs($totalCustomWeight - 1.0) < 0.01) {
                $weights = $customWeights;
            } else if ($totalCustomWeight > 0) { // Fallback jika JS tidak menormalisasi
                $normalizedCustomWeights = [];
                foreach ($customWeights as $key => $val) {
                    $normalizedCustomWeights[$key] = $val / $totalCustomWeight;
                }
                $weights = $normalizedCustomWeights;
            }
        }

        $benefitCriteria = ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan'];
        $costCriteria = ['biaya'];

        if (empty($dataMatrix)) {
            return [];
        }

        // Hitung pembagi untuk normalisasi
        $divisors = [];
        foreach ($criteria as $c) {
            $sumOfSquares = array_sum(array_map(fn($data) => pow($data['values'][$c], 2), $dataMatrix));
            $divisors[$c] = $sumOfSquares > 0 ? sqrt($sumOfSquares) : 1;
        }

        $results = [];
        foreach ($dataMatrix as $item) {
            $original = $item['values'];
            $normalized = [];

            foreach ($criteria as $c) {
                $normalized[$c] = $divisors[$c] != 0 ? $original[$c] / $divisors[$c] : 0;
            }

            $score = 0;
            foreach ($benefitCriteria as $c) {
                $score += ($normalized[$c] * $weights[$c]);
            }
            foreach ($costCriteria as $c) {
                $score -= ($normalized[$c] * $weights[$c]);
            }

            $results[] = [
                'lomba' => $item['lomba'],
                'score' => round($score, 4),
                'original_values' => $original,
                'normalized_values' => $normalized,
                'weights' => $weights,
                'divisors' => $divisors
            ];
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
        return $results;
    }

    // Method untuk menampilkan halaman utama daftar lomba mahasiswa
    public function indexLomba()
    {
        $userRole = Auth::user()->role;
        $breadcrumb = (object) ['title' => 'Informasi & Rekomendasi Lomba', 'list' => ['Info Lomba', 'Rekomendasi Lomba']];
        $activeMenu = 'info_lomba';

        $kriteriaUntukBobot = [
            'minat'     => 'Kesesuaian Minat',
            'keahlian'  => 'Kesesuaian Keahlian',
            'tingkat'   => 'Tingkat Lomba',
            'hadiah'    => 'Potensi Hadiah',
            'penutupan' => 'Sisa Waktu Pendaftaran',
            'biaya'     => 'Biaya Pendaftaran (Rendah Lebih Baik)',
        ];

        $defaultBobotView = [
            'minat'     => 25, 'keahlian'  => 25, 'tingkat'   => 15,
            'hadiah'    => 10, 'penutupan' => 15, 'biaya'     => 10
        ];
        
        // [PERBAIKAN] Logika disederhanakan, langsung menggunakan Auth::id()
        $userId = Auth::id();
        $bobotTersimpan = SliderKriteriaModel::where('user_id', $userId)->first();
        
        $bobotView = [];
        if ($bobotTersimpan) {
            foreach (array_keys($defaultBobotView) as $key) {
                $bobotView[$key] = $bobotTersimpan->$key ?? $defaultBobotView[$key];
            }
        } else {
            $bobotView = $defaultBobotView;
        }

        return view('lomba.mahasiswa.index', compact('breadcrumb', 'activeMenu', 'userRole', 'kriteriaUntukBobot', 'bobotView', 'defaultBobotView'));
    }

    // Sisa dari kode controller Anda (tidak perlu diubah)
    // ...
    public function getMooraCalculationDetailJson(Request $request)
    {
        $lombaId = $request->input('lomba_id');
        $weights = $request->input('weights', []);

        $lomba = LombaModel::find($lombaId);
        if (!$lomba) {
            return response()->json(['error' => 'Lomba tidak ditemukan.'], 404);
        }

        $mooraDetail = $this->calculateMooraScores(Auth::id(), $weights);
        if (!$mooraDetail) {
            return response()->json(['error' => 'Perhitungan tidak ditemukan.'], 404);
        }

        return response()->json([
            'lomba' => [
                'nama_lomba' => $lomba->nama_lomba,
                'score' => $mooraDetail['score'],
                'weights' => $mooraDetail['weights'],
                'original_values' => $mooraDetail['original_values'],
                'divisors' => $mooraDetail['divisors'],
                'normalized_values' => $mooraDetail['normalized_values']
            ]
        ]);
    }

    private function getMooraDetailByLombaId($lombaId, $weights)
    {
        $lomba = LombaModel::with('bidangKeahlian')->find($lombaId);
        if (!$lomba) {
            return null;
        }
        return $this->calculateMooraScores(Auth::id(), $weights);
    }



    public function create()
    {
        return view('lomba.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lomba' => 'required|string|max:50',
            'pembukaan_pendaftaran' => 'required|date',
            'kategori' => 'required|in:akademik,non-akademik,lainnya',
            'penyelenggara' => 'required|string|max:50',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'link_pendaftaran' => 'nullable|string|max:150',
            'batas_pendaftaran' => 'required|date',
            'status_verifikasi' => 'required|in:pending,disetujui,ditolak',
            'diinput_oleh' => 'required|integer'
        ]);

        LombaModel::create($validated);

        return redirect()->route('lomba.index')->with('success', 'Lomba berhasil ditambahkan');
    }

    public function show($id)
    {
        $lomba = LombaModel::findOrFail($id);

        return view('lomba.admin.show', compact('lomba'));
    }

    // Menampilkan detail lomba (AJAX) untuk semua user, hanya lomba yang disetujui
    public function showAjax($id)
    {
        $lomba = LombaModel::where('status_verifikasi', 'disetujui')->findOrFail($id);
        return view('lomba.show_ajax', compact('lomba'));
    }

    public function edit($id)
    {
        $data = LombaModel::findOrFail($id);
        return view('lomba.admin.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_lomba' => 'required|string|max:50',
            'pembukaan_pendaftaran' => 'required|date',
            'kategori' => 'required|in:akademik,non-akademik,lainnya',
            'penyelenggara' => 'required|string|max:50',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'link_pendaftaran' => 'nullable|string|max:150',
            'batas_pendaftaran' => 'required|date',
            'status_verifikasi' => 'required|in:pending,disetujui,ditolak',
            'diinput_oleh' => 'required|integer'
        ]);

        $data = LombaModel::findOrFail($id);
        $data->update($validated);

        return redirect()->route('lomba.admin.index')->with('success', 'Lomba berhasil diupdate');
    }

    public function indexLombaPublik()
    {
        $userRole = Auth::user()->role;
        $breadcrumb = (object) ['title' => 'Informasi Lomba Terkini', 'list' => ['Info Lomba', 'Daftar Lomba']];
        $activeMenu = 'info_lomba_publik'; // Sesuaikan nama activeMenu
        return view('lomba.publik.index', compact('breadcrumb', 'activeMenu', 'userRole'));
    }

    /**
     * Menyediakan data untuk DataTables daftar lomba yang sudah disetujui.
     */
    public function listLombaPublik(Request $request)
    {
        if ($request->ajax()) {
            $data = LombaModel::where('status_verifikasi', 'disetujui')
                ->orderBy('batas_pendaftaran', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama_lomba', function ($row) {
                    $html = '<span class="fw-semibold">' . e($row->nama_lomba) . '</span>';
                    if ($row->poster && Storage::disk('public')->exists($row->poster)) {
                        $html .= '<br><a href="' . asset('storage/' . $row->poster) . '" target="_blank" class="badge bg-light-info text-info mt-1"><i class="fas fa-image me-1"></i>Lihat Poster</a>';
                    }
                    return $html;
                })
                ->addColumn('periode_pendaftaran', function ($row) {
                    $mulai = $row->pembukaan_pendaftaran ? Carbon::parse($row->pembukaan_pendaftaran)->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY') : 'N/A';
                    $selesai = $row->batas_pendaftaran ? Carbon::parse($row->batas_pendaftaran)->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY') : 'N/A';
                    return $mulai . ' - ' . $selesai;
                })
                ->addColumn('biaya_formatted', fn($row) => $row->biaya > 0 ? 'Rp ' . number_format($row->biaya, 0, ',', '.') : '<span class="badge bg-light-success text-success px-2 py-1">Gratis</span>')
                ->addColumn('aksi', function ($row) {
                    return '<button onclick="modalActionLomba(\'' . route('lomba.publik.show_ajax', $row->lomba_id) . '\', \'Detail Lomba\', \'modalDetailLombaPublik\')" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i>Detail</button>';
                })
                ->rawColumns(['nama_lomba', 'biaya_formatted', 'aksi'])
                ->make(true);
        }
        return abort(403);
    }

    public function showAjaxLombaPublik($id)
    {
        $lomba = LombaModel::where('status_verifikasi', 'disetujui')
            ->with([
                'inputBy',
                'bidangKeahlian.bidang',
                'daftarHadiah' // [PERBAIKAN] Tambahkan eager loading untuk relasi hadiah
            ])
            ->findOrFail($id);

        // File view ini akan digunakan untuk mengisi modal di dashboard
        return view('lomba.publik.show_ajax', compact('lomba'));
    }

    // =======================================================================
    // METHOD UNTUK MAHASISWA & DOSEN (PENGAJUAN LOMBA & HISTORI)
    // =======================================================================

    public function indexLombaDosen()
    {
        $userRole = Auth::user()->role;
        $breadcrumb = (object) ['title' => 'Informasi Lomba Terkini', 'list' => ['Info Lomba', 'Daftar Lomba']];
        $activeMenu = 'info_lomba_publik'; // Sesuaikan nama activeMenu
        return view('lomba.dosen.index', compact('breadcrumb', 'activeMenu', 'userRole'));
    }

    /**
     * Menyediakan data untuk DataTables daftar lomba yang sudah disetujui.
     */
    public function listLombaDosen(Request $request)
    {
        if ($request->ajax()) {
            $data = LombaModel::where('status_verifikasi', 'disetujui')
                ->orderBy('batas_pendaftaran', 'asc');

            if ($request->filled('tingkat_lomba_filter_crud')) {
                $data->where('tingkat', $request->tingkat_lomba_filter_crud);
            }

            if ($request->filled('kategori_lomba_filter_crud')) {
                $data->where('kategori', $request->kategori_lomba_filter_crud);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama_lomba', function ($row) {
                    $html = '<span class="fw-semibold">' . e($row->nama_lomba) . '</span>';
                    if ($row->poster && Storage::disk('public')->exists($row->poster)) {
                        $html .= '<br><a href="' . asset('storage/' . $row->poster) . '" target="_blank" class="badge bg-light-info text-info mt-1"><i class="fas fa-image me-1"></i>Lihat Poster</a>';
                    }
                    return $html;
                })
                ->addColumn('periode_pendaftaran', function ($row) {
                    $mulai = $row->pembukaan_pendaftaran ? Carbon::parse($row->pembukaan_pendaftaran)->setTimezone('Asia/Jakarta')->isoFormat('D MMMM YYYY') : 'N/A';
                    $selesai = $row->batas_pendaftaran ? Carbon::parse($row->batas_pendaftaran)->setTimezone('Asia/Jakarta')->isoFormat('D MMMM YYYY') : 'N/A';
                    return $mulai . ' - ' . $selesai;
                })
                ->addColumn('biaya_formatted', fn($row) => $row->biaya > 0 ? 'Rp ' . number_format($row->biaya, 0, ',', '.') : '<span class="badge bg-light-success text-success px-2 py-1">Gratis</span>')
                ->addColumn('aksi', function ($row) {
                    return '<button onclick="modalActionLomba(\'' . route('lomba.publik.show_ajax', $row->lomba_id) . '\', \'Detail Lomba\', \'modalDetailLomba\')" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i></button>';
                })
                ->rawColumns(['nama_lomba', 'biaya_formatted', 'aksi'])
                ->make(true);
        }
        return abort(403);
    }

    /**
     * Menampilkan halaman histori pengajuan lomba untuk user yang login.
     */
    public function historiPengajuanLombaMhs()
    {
        $breadcrumb = (object) [
            'title' => 'Histori Pengajuan Info Lomba',
            'list' => ['Info Lomba', 'Histori Pengajuan']
        ];
        $activeMenu = 'histori_lomba_user';
        return view('lomba.mahasiswa.histori_pengajuan_lomba', compact('breadcrumb', 'activeMenu'));
    }

    /**
     * Menyediakan data untuk DataTables histori pengajuan lomba user.
     */
    public function listHistoriPengajuanLombaMhs(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $data = LombaModel::where('diinput_oleh', $user->user_id)
                ->orderBy('created_at', 'desc');

            if ($request->filled('status_verifikasi_filter')) {
                $data->where('status_verifikasi', $request->status_verifikasi_filter);
            }
            if ($request->filled('tingkat_lomba_filter')) {
                $data->where('tingkat', $request->tingkat_lomba_filter);
            }
            if ($request->filled('kategori_lomba_filter')) {
                $data->where('kategori', $request->kategori_lomba_filter);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama_lomba', fn($row) => e($row->nama_lomba))
                ->addColumn('bidang_lomba', function ($lomba) {
                    // Pastikan relasi ada sebelum diakses untuk menghindari error
                    if ($lomba->relationLoaded('detailBidang') && $lomba->detailBidang) {
                        return $lomba->detailBidang->map(function ($detail) {
                            return ucfirst($detail->kategori) . ' ' . ($detail->bidang->bidang_nama ?? 'N/A');
                        })->implode(', ');
                    }
                    return '-';
                })
                ->editColumn('batas_pendaftaran', fn($row) => $row->batas_pendaftaran ? Carbon::parse($row->batas_pendaftaran)->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY') : '-')
                ->editColumn('created_at', fn($row) => $row->created_at ? Carbon::parse($row->created_at)->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY') : '-')
                ->editColumn('status_verifikasi', fn($row) => $row->status_verifikasi_badge)
                ->addColumn('aksi', function ($row) {
                    $btnEdit = '';
                    // [PERBAIKAN] Ganti 'lomba.mhs.show_form' menjadi 'lomba.mhs.show'
                    $btnDetail = '<button onclick="modalActionLomba(\'' . route('lomba.mhs.show', $row->lomba_id) . '\', \'Detail Lomba\', \'modalFormLombaUser\')" class="btn btn-sm btn-outline-info me-1" title="Detail"><i class="fas fa-eye"></i></button>';

                    if ($row->status_verifikasi == 'ditolak' || $row->status_verifikasi == 'pending') {
                        $btnEdit = '<button onclick="modalActionLomba(\'' . route('lomba.mhs.edit_form', $row->lomba_id) . '\', \'Edit Pengajuan\', \'modalFormLombaUser\')" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button>';
                    }
                    return '<div class="btn-group">' . $btnDetail . $btnEdit . '</div>';
                })
                ->rawColumns(['status_verifikasi', 'aksi'])
                ->make(true);
        }
        return abort(403);
    }
    /**
     * Menampilkan form AJAX untuk mahasiswa/dosen mengajukan lomba.
     */
    public function createPengajuanLombaMhs()
    {
        $bidangList = BidangModel::orderBy('bidang_nama')->get();
        return view('lomba.mahasiswa.create_lomba', compact('bidangList'));
    }

    /**
     * Menyimpan pengajuan lomba dari mahasiswa/dosen.
     */

    // public function storeLombaMhs(Request $request)
    // {
    //     $user = Auth::user();

    //     $validator = Validator::make($request->all(), [
    //         'nama_lomba' => 'required|string|max:255',
    //         'pembukaan_pendaftaran' => 'required|date',
    //         'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
    //         'kategori' => 'required|in:individu,kelompok',
    //         'penyelenggara' => 'required|string|max:255',
    //         'tingkat' => 'required|in:lokal,nasional,internasional',
    //         'bidang_keahlian' => 'required|array|min:1',
    //         'bidang_keahlian.*' => 'exists:bidang,bidang_id',
    //         'biaya' => 'nullable|integer|min:0',
    //         'link_pendaftaran' => 'nullable|url|max:255',
    //         'link_penyelenggara' => 'nullable|url|max:255',
    //         'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validasi gagal.',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     $posterPath = null;
    //     if ($request->hasFile('poster')) {
    //         $posterPath = $request->file('poster')->store('lomba_poster', 'public');
    //     }

    //     // Simpan data utama lomba
    //     $lomba = LombaModel::create([
    //         'nama_lomba' => $request->nama_lomba,
    //         'pembukaan_pendaftaran' => $request->pembukaan_pendaftaran,
    //         'batas_pendaftaran' => $request->batas_pendaftaran,
    //         'kategori' => $request->kategori,
    //         'penyelenggara' => $request->penyelenggara,
    //         'tingkat' => $request->tingkat,
    //         'biaya' => $request->biaya ?? 0,
    //         'link_pendaftaran' => $request->link_pendaftaran,
    //         'link_penyelenggara' => $request->link_penyelenggara,
    //         'status_verifikasi' => 'pending',
    //         'diinput_oleh' => $user->user_id,
    //         'poster' => $posterPath,
    //     ]);

    //     // Simpan bidang ke lomba_detail
    //     foreach ($request->bidang_keahlian as $bidangId) {
    //         LombaDetailModel::create([
    //             'lomba_id' => $lomba->lomba_id,
    //             'bidang_id' => $bidangId
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Pengajuan info lomba berhasil dikirim dan akan diverifikasi oleh Admin.'
    //     ]);
    // }

    public function storeLombaMhs(Request $request) // Atau storePengajuanLombaUmum
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'nama_lomba' => 'required|string|max:50',
            'pembukaan_pendaftaran' => 'required|date',
            'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran|after_or_equal:today',
            'kategori' => 'required|in:individu,kelompok',
            'penyelenggara' => 'required|string|max:50',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|array|min:1',
            'bidang_keahlian.*' => 'exists:bidang,bidang_id', // Validasi setiap item dalam array
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:150',
            'link_penyelenggara' => 'nullable|url|max:150',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048', // Tambahkan pdf jika diizinkan
            'hadiah' => 'nullable|array',
            'hadiah.*' => 'nullable|string|max:40',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal. Periksa kembali data yang Anda masukkan.',
                'errors' => $validator->errors()
            ], 422);
        }

        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('lomba_poster', 'public');
        }

        $lomba = LombaModel::create([
            'nama_lomba' => $request->nama_lomba,
            'pembukaan_pendaftaran' => $request->pembukaan_pendaftaran,
            'batas_pendaftaran' => $request->batas_pendaftaran,
            'kategori' => $request->kategori,
            'penyelenggara' => $request->penyelenggara,
            'tingkat' => $request->tingkat,
            'biaya' => $request->biaya ?? 0,
            'link_pendaftaran' => $request->link_pendaftaran,
            'link_penyelenggara' => $request->link_penyelenggara,
            'status_verifikasi' => 'pending', // Pengajuan dari user defaultnya pending
            'diinput_oleh' => $user->user_id,
            'poster' => $posterPath,
        ]);

        // Simpan bidang keahlian
        if ($request->has('bidang_keahlian') && is_array($request->bidang_keahlian)) {
            foreach ($request->bidang_keahlian as $bidangId) {
                LombaDetailModel::create([
                    'lomba_id' => $lomba->lomba_id,
                    'bidang_id' => $bidangId
                ]);
            }
        }

        // Simpan hadiah
        if ($request->has('hadiah') && is_array($request->hadiah)) {
            foreach ($request->hadiah as $itemHadiah) {
                if (!empty(trim($itemHadiah))) {
                    LombaHadiahModel::create([
                        'lomba_id' => $lomba->lomba_id,
                        'hadiah' => $itemHadiah
                    ]);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Pengajuan info lomba berhasil dikirim dan akan diverifikasi oleh Admin.'
        ]);
    }

    // public function editLombaMhs($id)
    // {
    //     $lomba = LombaModel::with('bidangKeahlian')->findOrFail($id);
    //     $bidangList = BidangModel::all();

    //     return view('lomba.mahasiswa.edit', [
    //         'lomba' => $lomba,
    //         'bidangList' => $bidangList,
    //     ]);
    // }

    public function editLombaMhs($id) // Atau editPengajuanLombaUmum
    {
        $user = Auth::user();
        $lomba = LombaModel::where('lomba_id', $id)
            // Pastikan hanya pemilik atau admin yang bisa edit (kebijakan bisa beda)
            // ->where('diinput_oleh', $user->user_id) 
            ->with(['bidangKeahlian.bidang', 'daftarHadiah'])
            ->firstOrFail();

        // Hanya boleh edit jika statusnya 'pending' atau 'ditolak'
        if (!in_array($lomba->status_verifikasi, ['pending', 'ditolak'])) {
            // return redirect()->route('lomba.mhs.histori.index')->with('error', 'Pengajuan lomba ini tidak dapat diedit lagi.');
            // Jika ini adalah modal AJAX, kembalikan response error
            return response()->json(['status' => false, 'message' => 'Pengajuan lomba ini tidak dapat diedit lagi karena sudah diproses.'], 403);
        }

        $bidangList = BidangModel::orderBy('bidang_nama')->get();
        // Jika view Anda adalah 'lomba.edit' yang diunggah
        return view('lomba.mahasiswa.edit', compact('lomba', 'bidangList'));
    }
    public function updateLombaMhs(Request $request, $id) // Atau updatePengajuanLombaUmum
    {
        $user = Auth::user();
        $lomba = LombaModel::where('lomba_id', $id)
            // ->where('diinput_oleh', $user->user_id) // Pastikan user adalah pemilik
            ->firstOrFail();

        if (!in_array($lomba->status_verifikasi, ['pending', 'ditolak'])) {
            return response()->json(['status' => false, 'message' => 'Pengajuan lomba ini tidak dapat diupdate lagi karena sudah diproses.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama_lomba' => 'required|string|max:50',
            'pembukaan_pendaftaran' => 'required|date',
            'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
            'kategori' => 'required|in:individu,kelompok',
            'penyelenggara' => 'required|string|max:50',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|array|min:1',
            'bidang_keahlian.*' => 'exists:bidang,bidang_id',
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:150',
            'link_penyelenggara' => 'nullable|url|max:150',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048',
            'hadiah' => 'nullable|array',
            'hadiah.*' => 'nullable|string|max:40',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal. Periksa kembali data yang Anda masukkan.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('poster')) {
            if ($lomba->poster && Storage::disk('public')->exists($lomba->poster)) {
                Storage::disk('public')->delete($lomba->poster);
            }
            $validatedData['poster'] = $request->file('poster')->store('lomba_poster', 'public');
        } else {
            // Jika tidak ada file poster baru, jangan sertakan 'poster' dalam update data utama
            // agar nilai poster lama tidak terhapus jika tidak ada file baru.
            // Namun, jika ingin menghapus poster, perlu logika terpisah (misal checkbox "hapus poster")
            unset($validatedData['poster']);
        }

        // Set status kembali ke pending jika ada perubahan, agar admin mereview ulang
        $validatedData['status_verifikasi'] = 'pending';
        $validatedData['catatan_verifikasi'] = null; // Hapus catatan lama jika ada

        $lomba->update(collect($validatedData)->except(['bidang_keahlian', 'hadiah'])->toArray());

        // Update bidang keahlian
        if ($request->has('bidang_keahlian') && is_array($request->bidang_keahlian)) {
            $lomba->bidangKeahlian()->delete(); // Hapus yang lama
            foreach ($request->bidang_keahlian as $bidangId) {
                LombaDetailModel::create([
                    'lomba_id' => $lomba->lomba_id,
                    'bidang_id' => $bidangId
                ]);
            }
        }

        // Update hadiah
        $lomba->daftarHadiah()->delete(); // Hapus hadiah lama
        if ($request->has('hadiah') && is_array($request->hadiah)) {
            foreach ($request->hadiah as $itemHadiah) {
                if (!empty(trim($itemHadiah))) {
                    LombaHadiahModel::create([
                        'lomba_id' => $lomba->lomba_id,
                        'hadiah' => $itemHadiah
                    ]);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Pengajuan info lomba berhasil diperbarui dan akan diverifikasi ulang oleh Admin.'
        ]);
    }

    // public function showLombaMhs($id)
    // {
    //     $lomba = LombaModel::with([
    //         'inputBy',
    //         'bidangKeahlian.bidang'
    //     ])
    //         ->findOrFail($id);
    //     return view('lomba.mahasiswa.show', compact('lomba'));
    // }

    public function showLombaMhs($id)
    {
        $user = Auth::user();
        $lomba = LombaModel::where('lomba_id', $id)
            // ->where('diinput_oleh', $user->user_id) // Baris ini bisa diaktifkan jika hanya pemilik yang boleh lihat
            ->with([
                'inputBy',
                'bidangKeahlian.bidang',
                'daftarHadiah' // [PERBAIKAN] Tambahkan eager loading untuk relasi hadiah
            ])
            ->firstOrFail();

        // File view ini untuk halaman detail pengajuan milik mahasiswa
        return view('lomba.mahasiswa.show', compact('lomba'));
    }
    /**
     * Menampilkan halaman histori pengajuan lomba untuk user yang login.
     */
    public function historiPengajuanLombaDsn()
    {
        $breadcrumb = (object) [
            'title' => 'Histori Pengajuan Info Lomba',
            'list' => ['Info Lomba', 'Histori Pengajuan']
        ];
        $activeMenu = 'histori_lomba_user';
        return view('lomba.dosen.histori_pengajuan_lomba', compact('breadcrumb', 'activeMenu'));
    }

    /**
     * Menyediakan data untuk DataTables histori pengajuan lomba user.
     */
    public function listHistoriPengajuanLombaDsn(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $data = LombaModel::where('diinput_oleh', $user->user_id)
                ->orderBy('created_at', 'desc');

            if ($request->filled('status_verifikasi_filter')) {
                $data->where('status_verifikasi', $request->status_verifikasi_filter);
            }
            if ($request->filled('tingkat_lomba_filter')) { // Jika filter ini tetap ada di view verifikasi
                $data->where('tingkat', $request->tingkat_lomba_filter);
            }
            if ($request->filled('kategori_lomba_filter')) {
                $data->where('kategori', $request->kategori_lomba_filter);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama_lomba', fn($row) => e($row->nama_lomba))
                ->editColumn('batas_pendaftaran', fn($row) => $row->batas_pendaftaran ? Carbon::parse($row->batas_pendaftaran)->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY') : '-')
                ->editColumn('created_at', fn($row) => $row->created_at ? Carbon::parse($row->created_at)->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY, HH:mm') : '-')
                ->editColumn('status_verifikasi', fn($row) => $row->status_verifikasi_badge)
                ->addColumn('aksi', function ($row) {
                    $btnEdit = '';
                    // [PERBAIKAN KONSISTENSI] Ganti 'lomba.dosen.show_form' menjadi 'lomba.dosen.show'
                    $btnDetail = '<button onclick="modalActionLomba(\'' . route('lomba.dosen.show', $row->lomba_id) . '\', \'Detail Lomba\', \'modalFormLombaUser\')" class="btn btn-sm btn-outline-info me-1" title="Detail"><i class="fas fa-eye"></i></button>';

                    if ($row->status_verifikasi == 'ditolak' || $row->status_verifikasi == 'pending') {
                        $btnEdit = '<button onclick="modalActionLomba(\'' . route('lomba.dosen.edit_form', $row->lomba_id) . '\', \'Edit Pengajuan\', \'modalFormLombaUser\')" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button>';
                    }
                    return '<div class="btn-group">' . $btnDetail . $btnEdit . '</div>';
                })
                ->rawColumns(['status_verifikasi', 'aksi'])
                ->make(true);
        }
        return abort(403);
    }

    public function createPengajuanLombaDsn()
    {
        $bidangList = BidangModel::orderBy('bidang_nama')->get();
        return view('lomba.dosen.create_lomba', compact('bidangList'));
    }

    public function storeLombaDsn(Request $request) // Atau storePengajuanLombaUmum
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'nama_lomba' => 'required|string|max:50',
            'pembukaan_pendaftaran' => 'required|date',
            'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
            'kategori' => 'required|in:individu,kelompok',
            'penyelenggara' => 'required|string|max:50',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|array|min:1',
            'bidang_keahlian.*' => 'exists:bidang,bidang_id', // Validasi setiap item dalam array
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:150',
            'link_penyelenggara' => 'nullable|url|max:150',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048', // Tambahkan pdf jika diizinkan
            'hadiah' => 'nullable|array',
            'hadiah.*' => 'nullable|string|max:40',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal. Periksa kembali data yang Anda masukkan.',
                'errors' => $validator->errors()
            ], 422);
        }

        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('lomba_poster', 'public');
        }

        $lomba = LombaModel::create([
            'nama_lomba' => $request->nama_lomba,
            'pembukaan_pendaftaran' => $request->pembukaan_pendaftaran,
            'batas_pendaftaran' => $request->batas_pendaftaran,
            'kategori' => $request->kategori,
            'penyelenggara' => $request->penyelenggara,
            'tingkat' => $request->tingkat,
            'biaya' => $request->biaya ?? 0,
            'link_pendaftaran' => $request->link_pendaftaran,
            'link_penyelenggara' => $request->link_penyelenggara,
            'status_verifikasi' => 'pending', // Pengajuan dari user defaultnya pending
            'diinput_oleh' => $user->user_id,
            'poster' => $posterPath,
        ]);

        // Simpan bidang keahlian
        if ($request->has('bidang_keahlian') && is_array($request->bidang_keahlian)) {
            foreach ($request->bidang_keahlian as $bidangId) {
                LombaDetailModel::create([
                    'lomba_id' => $lomba->lomba_id,
                    'bidang_id' => $bidangId
                ]);
            }
        }

        // Simpan hadiah
        if ($request->has('hadiah') && is_array($request->hadiah)) {
            foreach ($request->hadiah as $itemHadiah) {
                if (!empty(trim($itemHadiah))) {
                    LombaHadiahModel::create([
                        'lomba_id' => $lomba->lomba_id,
                        'hadiah' => $itemHadiah
                    ]);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Pengajuan info lomba berhasil dikirim dan akan diverifikasi oleh Admin.'
        ]);
    }

    public function editLombaDsn($id) // Atau editPengajuanLombaUmum
    {
        $user = Auth::user();
        $lomba = LombaModel::where('lomba_id', $id)
            // Pastikan hanya pemilik atau admin yang bisa edit (kebijakan bisa beda)
            // ->where('diinput_oleh', $user->user_id) 
            ->with(['bidangKeahlian.bidang', 'daftarHadiah'])
            ->firstOrFail();

        // Hanya boleh edit jika statusnya 'pending' atau 'ditolak'
        if (!in_array($lomba->status_verifikasi, ['pending', 'ditolak'])) {
            // return redirect()->route('lomba.mhs.histori.index')->with('error', 'Pengajuan lomba ini tidak dapat diedit lagi.');
            // Jika ini adalah modal AJAX, kembalikan response error
            return response()->json(['status' => false, 'message' => 'Pengajuan lomba ini tidak dapat diedit lagi karena sudah diproses.'], 403);
        }

        $bidangList = BidangModel::orderBy('bidang_nama')->get();
        // Jika view Anda adalah 'lomba.edit' yang diunggah
        return view('lomba.dosen.edit', compact('lomba', 'bidangList'));
    }

    public function updateLombaDsn(Request $request, $id) // Atau updatePengajuanLombaUmum
    {
        $user = Auth::user();
        $lomba = LombaModel::where('lomba_id', $id)
            // ->where('diinput_oleh', $user->user_id) // Pastikan user adalah pemilik
            ->firstOrFail();

        if (!in_array($lomba->status_verifikasi, ['pending', 'ditolak'])) {
            return response()->json(['status' => false, 'message' => 'Pengajuan lomba ini tidak dapat diupdate lagi karena sudah diproses.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama_lomba' => 'required|string|max:50',
            'pembukaan_pendaftaran' => 'required|date',
            'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
            'kategori' => 'required|in:individu,kelompok',
            'penyelenggara' => 'required|string|max:50',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|array|min:1',
            'bidang_keahlian.*' => 'exists:bidang,bidang_id',
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:150',
            'link_penyelenggara' => 'nullable|url|max:50',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048',
            'hadiah' => 'nullable|array',
            'hadiah.*' => 'nullable|string|max:40',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal. Periksa kembali data yang Anda masukkan.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('poster')) {
            if ($lomba->poster && Storage::disk('public')->exists($lomba->poster)) {
                Storage::disk('public')->delete($lomba->poster);
            }
            $validatedData['poster'] = $request->file('poster')->store('lomba_poster', 'public');
        } else {
            // Jika tidak ada file poster baru, jangan sertakan 'poster' dalam update data utama
            // agar nilai poster lama tidak terhapus jika tidak ada file baru.
            // Namun, jika ingin menghapus poster, perlu logika terpisah (misal checkbox "hapus poster")
            unset($validatedData['poster']);
        }

        // Set status kembali ke pending jika ada perubahan, agar admin mereview ulang
        $validatedData['status_verifikasi'] = 'pending';
        $validatedData['catatan_verifikasi'] = null; // Hapus catatan lama jika ada

        $lomba->update(collect($validatedData)->except(['bidang_keahlian', 'hadiah'])->toArray());

        // Update bidang keahlian
        if ($request->has('bidang_keahlian') && is_array($request->bidang_keahlian)) {
            $lomba->bidangKeahlian()->delete(); // Hapus yang lama
            foreach ($request->bidang_keahlian as $bidangId) {
                LombaDetailModel::create([
                    'lomba_id' => $lomba->lomba_id,
                    'bidang_id' => $bidangId
                ]);
            }
        }

        // Update hadiah
        $lomba->daftarHadiah()->delete(); // Hapus hadiah lama
        if ($request->has('hadiah') && is_array($request->hadiah)) {
            foreach ($request->hadiah as $itemHadiah) {
                if (!empty(trim($itemHadiah))) {
                    LombaHadiahModel::create([
                        'lomba_id' => $lomba->lomba_id,
                        'hadiah' => $itemHadiah
                    ]);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Pengajuan info lomba berhasil diperbarui dan akan diverifikasi ulang oleh Admin.'
        ]);
    }

    public function showLombaDsn($id) // Atau showPengajuanLombaUmum
    {
        $user = Auth::user();
        $lomba = LombaModel::where('lomba_id', $id)
            // ->where('diinput_oleh', $user->user_id) // Opsional, jika hanya pemilik yang boleh lihat detail pengajuannya
            ->with(['inputBy', 'bidangKeahlian.bidang', 'daftarHadiah'])
            ->firstOrFail();

        // Jika view Anda adalah 'lomba.show' yang diunggah
        return view('lomba.dosen.show', compact('lomba'));
    }

    /**
     * Menampilkan form AJAX untuk mahasiswa/dosen mengajukan lomba.
     */
    public function createPengajuanLomba()
    {
        $bidangList = BidangModel::orderBy('bidang_nama')->get();
        return view('lomba.create_lomba', compact('bidangList'));
    }

    /**
     * Menyimpan pengajuan lomba dari mahasiswa/dosen.
     */

    public function storeLomba(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'nama_lomba' => 'required|string|max:50',
            'pembukaan_pendaftaran' => 'required|date',
            'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
            'kategori' => 'required|in:individu,kelompok',
            'penyelenggara' => 'required|string|max:50',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|array|min:1',
            'bidang_keahlian.*' => 'exists:bidang,bidang_id',
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:150',
            'link_penyelenggara' => 'nullable|url|max:150',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('lomba_poster', 'public');
        }

        // Simpan data utama lomba
        $lomba = LombaModel::create([
            'nama_lomba' => $request->nama_lomba,
            'pembukaan_pendaftaran' => $request->pembukaan_pendaftaran,
            'batas_pendaftaran' => $request->batas_pendaftaran,
            'kategori' => $request->kategori,
            'penyelenggara' => $request->penyelenggara,
            'tingkat' => $request->tingkat,
            'biaya' => $request->biaya ?? 0,
            'link_pendaftaran' => $request->link_pendaftaran,
            'link_penyelenggara' => $request->link_penyelenggara,
            'status_verifikasi' => 'pending',
            'diinput_oleh' => $user->user_id,
            'poster' => $posterPath,
        ]);

        // Simpan bidang ke lomba_detail
        foreach ($request->bidang_keahlian as $bidangId) {
            LombaDetailModel::create([
                'lomba_id' => $lomba->lomba_id,
                'bidang_id' => $bidangId
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Pengajuan info lomba berhasil dikirim dan akan diverifikasi oleh Admin.'
        ]);
    }

    // =======================================================================
    // METHOD UNTUK ADMIN: VERIFIKASI LOMBA
    // =======================================================================
    public function adminIndexVerifikasiLomba()
    {
        $breadcrumb = (object) ['title' => 'Daftar Pengajuan Info Lomba', 'list' => ['Lomba', 'Verifikasi Lomba']];
        $activeMenu = 'admin_verifikasi_lomba';
        return view('lomba.admin.verifikasi.index', compact('breadcrumb', 'activeMenu'));
    }

    public function adminListVerifikasiLomba(Request $request) // DataTables untuk halaman verifikasi
    {
        if ($request->ajax()) {
            $data = LombaModel::with([
                'inputBy' => function ($query) {
                    // Hanya ambil field yang dibutuhkan dari user untuk mengurangi data
                    $query->select('user_id', 'nama', 'role');
                }
            ])
                ->orderBy('created_at', 'desc');

            if ($request->filled('status_verifikasi_filter')) {
                $data->where('status_verifikasi', $request->status_verifikasi_filter);
            }
            if ($request->filled('tingkat_lomba_filter')) { // Jika filter ini tetap ada di view verifikasi
                $data->where('tingkat', $request->tingkat_lomba_filter);
            }
            if ($request->filled('kategori_lomba_filter')) {
                $data->where('kategori', $request->kategori_lomba_filter);
            }


            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_lomba_display', function ($row) { // Kolom gabungan Nama & Poster
                    $html = '<span class="fw-semibold">' . e($row->nama_lomba) . '</span>';
                    if ($row->poster && Storage::disk('public')->exists($row->poster)) {
                        // Thumbnail kecil atau hanya link
                        $html .= '<br><a href="' . asset('storage/' . $row->poster) . '" target="_blank" data-bs-toggle="tooltip" title="Lihat Poster" class="text-info small"><i class="fas fa-image"></i> Poster</a>';
                    }
                    return $html;
                })
                ->addColumn('diajukan_oleh', function ($row) {
                    if ($row->inputBy) {
                        return e($row->inputBy->nama) . '<br><small class="text-muted">(' . e(ucfirst($row->inputBy->role)) . ')</small>';
                    }
                    return '<span class="text-muted fst-italic">N/A</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? Carbon::parse($row->created_at)->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY, HH:mm') : '-';
                })
                ->editColumn('status_verifikasi', fn($row) => $row->status_verifikasi_badge)
                ->addColumn('aksi', function ($row) {
                    // Tombol hanya untuk membuka modal verifikasi
                    $btnVerifikasi = '<button onclick="modalActionLombaAdmin(\'' . route('admin.lomba.verifikasi.form_ajax', $row->lomba_id) . '\', \'Verifikasi Lomba\', \'modalVerifikasiLombaAdmin\')" class="btn btn-sm btn-primary" title="Proses Verifikasi"><i class="fas fa-clipboard-check me-1"></i>Verifikasi</button>';
                    return $btnVerifikasi;
                })
                ->rawColumns(['nama_lomba_display', 'diajukan_oleh', 'status_verifikasi', 'aksi'])
                ->make(true);
        }
        return abort(403);
    }

    // public function adminShowVerifyFormAjax($id)
    // {
    //     $lomba = LombaModel::with('inputBy')->findOrFail($id);
    //     return view('lomba.admin.verifikasi.verifikasi_lomba', compact('lomba'));
    // }

    public function adminShowVerifyFormAjax($id)
    {
        $lomba = LombaModel::with([
            'inputBy',
            'bidangKeahlian.bidang', // Eager load bidang
            'daftarHadiah'         // Eager load hadiah
        ])
            ->findOrFail($id);
        return view('lomba.admin.verifikasi.verifikasi_lomba', compact('lomba'));
    }

    public function adminProcessVerificationAjax(Request $request, $id)
    {
        $lomba = LombaModel::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'status_verifikasi' => 'required|in:disetujui,ditolak,pending',
            'catatan_verifikasi' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        if ($request->status_verifikasi == 'ditolak' && empty(trim($request->catatan_verifikasi ?? ''))) {
            return response()->json([
                'status' => false,
                'message' => 'Catatan verifikasi wajib diisi jika status ditolak.',
                'errors' => ['catatan_verifikasi' => ['Catatan verifikasi wajib diisi jika status ditolak.']]
            ], 422);
        }

        $lomba->status_verifikasi = $request->status_verifikasi;
        $lomba->catatan_verifikasi = $request->catatan_verifikasi; // Pastikan ada kolom ini di tabel
        $lomba->save();
        return response()->json(['status' => true, 'message' => 'Status verifikasi lomba berhasil diperbarui.']);
    }


    // =======================================================================
    // METHOD UNTUK ADMIN: MANAJEMEN/CRUD LOMBA (Admin Input Langsung)
    // =======================================================================
    public function adminIndexCrudLomba()
    {
        $breadcrumb = (object) ['title' => 'Manajemen Data Lomba', 'list' => ['Lomba', 'Data Lomba']];
        $activeMenu = 'admin_crud_lomba';
        return view('lomba.admin.crud.index', compact('breadcrumb', 'activeMenu'));
    }

    public function adminListCrudLomba(Request $request)
    {
        if ($request->ajax()) {
            $data = LombaModel::with('inputBy')->orderBy('created_at', 'desc');

            // Filter status: default ke 'disetujui' jika tidak ada filter dari frontend
            $statusFilter = $request->filled('status_verifikasi_filter_crud') ? $request->status_verifikasi_filter_crud : 'disetujui';
            if (!empty($statusFilter)) {
                $data->where('status_verifikasi', $statusFilter);
            }

            if ($request->filled('tingkat_lomba_filter_crud')) {
                $data->where('tingkat', $request->tingkat_lomba_filter_crud);
            }

            if ($request->filled('kategori_lomba_filter_crud')) {
                $data->where('kategori', $request->kategori_lomba_filter_crud);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama_lomba', function ($row) {
                    $html = '<span class="fw-semibold">' . e($row->nama_lomba) . '</span>';
                    if ($row->poster && Storage::disk('public')->exists($row->poster)) {
                        $html .= '<br><a href="' . asset('storage/' . $row->poster) . '" target="_blank" class="badge bg-light-info text-info mt-1"><i class="fas fa-image me-1"></i>Poster</a>';
                    }
                    return $html;
                })
                ->addColumn('inputBy_nama', fn($row) => $row->inputBy->nama ?? '<span class="text-muted fst-italic">N/A</span>')
                ->editColumn('status_verifikasi', fn($row) => $row->status_verifikasi_badge) // Menggunakan accessor
                ->editColumn('batas_pendaftaran', fn($row) => $row->batas_pendaftaran ? Carbon::parse($row->batas_pendaftaran)->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY') : '-')
                ->addColumn('aksi', function ($row) {
                    // Tombol Detail (menggunakan modal yang sama dengan publik/user)
                    $btnDetail = '<button onclick="modalActionLombaAdminCrud(\'' . route('lomba.publik.show_ajax', $row->lomba_id) . '\', \'Detail Lomba\', \'modalDetailLombaAdminCrud\')" class="btn btn-sm btn-outline-info me-1" title="Detail"><i class="fas fa-eye"></i></button>';

                    $btnEdit = '<button onclick="modalActionLombaAdminCrud(\'' . route('admin.lomba.crud.edit_form_ajax', $row->lomba_id) . '\', \'Edit Lomba\', \'modalFormLombaAdminCrud\')" class="btn btn-sm btn-outline-warning me-1" title="Edit"><i class="fas fa-edit"></i></button>';

                    $btnDelete = '<button onclick="modalActionLombaAdminCrud(\'' . route('admin.lomba.crud.confirm_delete_ajax', $row->lomba_id) . '\', \'Konfirmasi Hapus Lomba\', \'modalConfirmDeleteLombaAdminCrud\')" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>';

                    return '<div class="btn-group">' . $btnDetail . $btnEdit . $btnDelete . '</div>';
                })
                ->rawColumns(['nama_lomba', 'status_verifikasi', 'aksi', 'inputBy_nama'])
                ->make(true);
        }
        return abort(403);
    }

    // Menampilkan form TAMBAH lomba (AJAX) untuk admin
    public function adminCreateLombaFormAjax()
    {
        $bidangList = BidangModel::orderBy('bidang_nama')->get(); // Jika bidang_keahlian adalah multi-select
        return view('lomba.admin.crud.create_lomba', compact('bidangList'));
    }


    // Tambah hadiah
    public function adminStoreLombaAjax(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'nama_lomba' => 'required|string|max:50',
            'pembukaan_pendaftaran' => 'required|date',
            'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
            'kategori' => 'required|in:individu,kelompok',
            'penyelenggara' => 'required|string|max:50',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|array|min:1',
            'bidang_keahlian.*' => 'exists:bidang,bidang_id',
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:150',
            'link_penyelenggara' => 'nullable|url|max:150',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'hadiah' => 'nullable|array', // Tambahkan validasi untuk hadiah
            'hadiah.*' => 'nullable|string|max:40', // Setiap item hadiah adalah string
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('lomba_poster', 'public');
        }

        // Simpan data utama lomba
        $lomba = LombaModel::create([
            'nama_lomba' => $request->nama_lomba,
            'pembukaan_pendaftaran' => $request->pembukaan_pendaftaran,
            'batas_pendaftaran' => $request->batas_pendaftaran,
            'kategori' => $request->kategori,
            'penyelenggara' => $request->penyelenggara,
            'tingkat' => $request->tingkat,
            'biaya' => $request->biaya ?? 0,
            'link_pendaftaran' => $request->link_pendaftaran,
            'link_penyelenggara' => $request->link_penyelenggara,
            'status_verifikasi' => 'disetujui', // Lomba dari admin langsung disetujui
            'diinput_oleh' => $user->user_id,
            'poster' => $posterPath,
        ]);

        // Simpan bidang keahlian ke tabel lomba_detail
        if ($request->has('bidang_keahlian') && is_array($request->bidang_keahlian)) {
            foreach ($request->bidang_keahlian as $bidangId) {
                LombaDetailModel::create([
                    'lomba_id' => $lomba->lomba_id,
                    'bidang_id' => $bidangId
                    // Anda mungkin perlu menambahkan 'kategori' di sini jika LombaDetailModel membutuhkannya
                    // Misalnya: 'kategori' => 'keahlian'
                ]);
            }
        }

        // Simpan hadiah ke tabel lomba_hadiah
        if ($request->has('hadiah') && is_array($request->hadiah)) {
            foreach ($request->hadiah as $itemHadiah) {
                if (!empty(trim($itemHadiah))) { // Hanya simpan jika tidak kosong
                    LombaHadiahModel::create([
                        'lomba_id' => $lomba->lomba_id,
                        'hadiah' => $itemHadiah
                    ]);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Info lomba baru berhasil ditambahkan.'
        ]);
    }

    // Menampilkan form EDIT lomba (AJAX) untuk admin
    public function adminEditLombaFormAjax($id)
    {
        // Eager load relasi bidangKeahlian dan daftarHadiah
        $lomba = LombaModel::with(['bidangKeahlian.bidang', 'daftarHadiah'])->findOrFail($id);
        $bidangList = BidangModel::orderBy('bidang_nama')->get(); // Ambil semua bidang untuk pilihan

        // Variabel untuk menentukan apakah form ini dibuka oleh admin (untuk field status verifikasi)
        $isAdmin = (Auth::user()->role === 'admin');


        return view('lomba.admin.crud.edit_lomba', compact('lomba', 'bidangList', 'isAdmin'));
    }

    // Mengupdate lomba oleh admin (AJAX)
    public function adminUpdateLombaAjax(Request $request, $id)
    {
        $lomba = LombaModel::findOrFail($id);

        $rules = [
            'nama_lomba' => 'required|string|max:50',
            'pembukaan_pendaftaran' => 'required|date',
            'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
            'kategori' => 'required|in:individu,kelompok',
            'penyelenggara' => 'required|string|max:50',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|array|min:1',
            'bidang_keahlian.*' => 'exists:bidang,bidang_id',
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:150',
            'link_penyelenggara' => 'nullable|url|max:150',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'hadiah' => 'nullable|array',
            'hadiah.*' => 'nullable|string|max:40',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated(); // Ambil data yang sudah divalidasi

        if ($request->hasFile('poster')) {
            // Hapus poster lama jika ada
            if ($lomba->poster && Storage::disk('public')->exists($lomba->poster)) {
                Storage::disk('public')->delete($lomba->poster);
            }
            $validatedData['poster'] = $request->file('poster')->store('lomba_poster', 'public');
        }

        // Update data utama lomba
        $lomba->update(collect($validatedData)->except(['bidang_keahlian', 'hadiah'])->toArray());

        // Update bidang keahlian
        if ($request->has('bidang_keahlian') && is_array($request->bidang_keahlian)) {
            $lomba->bidangKeahlian()->delete(); // Hapus yang lama
            foreach ($request->bidang_keahlian as $bidangId) {
                LombaDetailModel::create([
                    'lomba_id' => $lomba->lomba_id,
                    'bidang_id' => $bidangId
                    // 'kategori' => 'keahlian' // Jika perlu
                ]);
            }
        }

        // Update hadiah
        $lomba->daftarHadiah()->delete(); // Hapus hadiah lama
        if ($request->has('hadiah') && is_array($request->hadiah)) {
            foreach ($request->hadiah as $itemHadiah) {
                if (!empty(trim($itemHadiah))) {
                    LombaHadiahModel::create([
                        'lomba_id' => $lomba->lomba_id,
                        'hadiah' => $itemHadiah
                    ]);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Info lomba berhasil diperbarui.'
        ]);
    }


    // Menampilkan konfirmasi hapus (AJAX)
    public function adminConfirmDeleteLombaAjax($id)
    {
        $lomba = LombaModel::find($id);
        if (!$lomba) {
            // Bisa juga return view error jika diperlukan
            return response()->json(['message' => 'Data lomba tidak ditemukan.'], 404);
        }
        return view('lomba.admin.crud.confirm_ajax', compact('lomba'));
    }

    // Menghapus lomba oleh admin (AJAX)
    public function adminDestroyLombaAjax($id)
    {
        $lomba = LombaModel::find($id);
        if (!$lomba) {
            return response()->json(['status' => false, 'message' => 'Data lomba tidak ditemukan.'], 404);
        }

        try {
            if ($lomba->poster && Storage::disk('public')->exists($lomba->poster)) {
                Storage::disk('public')->delete($lomba->poster);
            }
            $lomba->delete();
            return response()->json(['status' => true, 'message' => 'Info lomba berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Gagal hapus lomba: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal menghapus lomba. Terjadi kesalahan server.'], 500);
        }
    }

    public function export_excel()
    {
        // ambil data user yang akan di export
        // Pastikan untuk eager load relasi yang dibutuhkan
        $lomba = LombaModel::select('lomba_id', 'nama_lomba', 'penyelenggara', 'tingkat', 'biaya', 'pembukaan_pendaftaran', 'batas_pendaftaran')
            ->where('status_verifikasi', 'disetujui')
            ->orderBy('nama_lomba')
            ->get();

        // Load library excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

        // Set header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Lomba'); // Ditambahkan/disesuaikan
        $sheet->setCellValue('C1', 'Penyelenggara');   // Kolom baru
        $sheet->setCellValue('D1', 'Tingkat');
        $sheet->setCellValue('E1', 'Biaya');
        $sheet->setCellValue('F1', 'Pembukaan Pendaftaran');
        $sheet->setCellValue('G1', 'Batas Pendaftaran');

        // Set style bold untuk header
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $no = 1; // nomor data dimulai dari 1
        $baris = 2; // baris data dimulai dari baris ke-2
        foreach ($lomba as $lomba) { // Menggunakan $user sebagai iterator
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $lomba->nama_lomba);
            $sheet->setCellValue('C' . $baris, $lomba->penyelenggara);
            $sheet->setCellValue('D' . $baris, $lomba->tingkat);
            $sheet->setCellValue('E' . $baris, $lomba->biaya);
            $sheet->setCellValue('F' . $baris, $lomba->pembukaan_pendaftaran->isoFormat('D MMMM YYYY'));
            $sheet->setCellValue('G' . $baris, $lomba->batas_pendaftaran->isoFormat('D MMMM YYYY'));

            $baris++;
            $no++;
        }

        // Set auto size untuk semua kolom yang digunakan
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Lomba'); // set title sheet

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Lomba ' . date('Y-m-d H_i_s') . '.xlsx'; // Menggunakan H_i_s agar nama file lebih unik

        // Menyiapkan header untuk file Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1'); // Sesuai contoh Anda
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $lomba = LombaModel::select('lomba_id', 'nama_lomba', 'penyelenggara', 'tingkat', 'biaya', 'pembukaan_pendaftaran', 'batas_pendaftaran')
            ->where('status_verifikasi', 'disetujui')
            ->orderBy('nama_lomba')
            ->get();

        // use Barryvdh\DomPDF\Facade\Pdf PDF
        $pdf = Pdf::loadView('lomba.export_pdf', ['lomba' => $lomba]);
        $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi 
        $pdf->setOption('isRemoteEnabled', true); // set true jika ada gambar dari URL
        $pdf->render();

        return $pdf->stream('Data User ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
