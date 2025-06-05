<?php

namespace App\Http\Controllers;

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
    //     //=================================================
    //     //|        METHOD UNTUK MAHASISWA DAN DOSEN       |
    //     //=================================================
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Lomba',
            'list' => ['Dashboard', 'Lomba']
        ];

        $activeMenu = 'lomba';

        return view('lomba.mahasiswa.index', compact('breadcrumb', 'activeMenu'));
    }

    // public function indexLomba()
    // {
    //     $userRole = Auth::user()->role;
    //     $breadcrumb = (object) ['title' => 'Informasi Lomba', 'list' => ['Lomba']];
    //     $activeMenu = 'info_lomba';

    //     // view yang sama untuk daftar lomba yang disetujui.
    //     return view('lomba.mahasiswa.index', compact('breadcrumb', 'activeMenu', 'userRole'));
    // }


    //     private function calculateMooraScores($userId)
    //     {
    //         $user = UserModel::with(['minat', 'keahlian'])->find($userId);
    //         if (!$user) {
    //             return [];
    //         }

    //         $userMinatIds = $user->minat->pluck('bidang_id')->toArray();
    //         $userKeahlianIds = $user->keahlian->pluck('bidang_id')->toArray();

    //         $lombas = LombaModel::with(['detailBidang'])->where(function ($query) {
    //             $query->where('batas_pendaftaran', '>=', Carbon::now()->toDateString())
    //                 ->orWhereNull('batas_pendaftaran');
    //         })->get();

    //         if ($lombas->isEmpty()) {
    //             return [];
    //         }

    //         $dataMatrix = [];

    //         foreach ($lombas as $lomba) {
    //             $row = [];

    //             $lombaMinatBidangIds = $lomba->detailBidang
    //                 ->where('kategori', 'minat')
    //                 ->pluck('bidang_id')
    //                 ->toArray();

    //             $lombaKeahlianBidangIds = $lomba->detailBidang
    //                 ->where('kategori', 'keahlian')
    //                 ->pluck('bidang_id')
    //                 ->toArray();

    //             // Skor 1 jika ada minimal 1 bidang minat sama antara lomba dan user
    //             $row['minat'] = count(array_intersect($lombaMinatBidangIds, $userMinatIds)) > 0 ? 1 : 0;

    //             // Skor 1 jika ada minimal 1 bidang keahlian sama antara lomba dan user
    //             $row['keahlian'] = count(array_intersect($lombaKeahlianBidangIds, $userKeahlianIds)) > 0 ? 1 : 0;

    //             $row['tingkat'] = match (strtolower($lomba->tingkat ?? '')) {
    //                 'lokal' => 1,
    //                 'kota' => 2,
    //                 'kabupaten' => 2,
    //                 'provinsi' => 3,
    //                 'nasional' => 4,
    //                 'internasional' => 5,
    //                 default => 0,
    //             };

    //             $row['hadiah'] = $lomba->daftarHadiah->count();

    //             if ($lomba->batas_pendaftaran) {
    //                 $selisihHari = Carbon::now()->diffInDays(Carbon::parse($lomba->batas_pendaftaran), false);
    //                 $row['penutupan'] = match (true) {
    //                     $selisihHari < 0 => 0,
    //                     $selisihHari == 0 => 1,
    //                     $selisihHari <= 7 => 2,
    //                     $selisihHari <= 14 => 3,
    //                     $selisihHari <= 30 => 4,
    //                     default => 5,
    //                 };
    //             } else {
    //                 $row['penutupan'] = 5;
    //             }

    //             $row['biaya'] = (float) ($lomba->biaya ?? 0);

    //             $dataMatrix[] = [
    //                 'lomba' => $lomba,
    //                 'values' => $row,
    //             ];
    //         }

    //         return $this->processMooraNormalization($dataMatrix);
    //     }


    //     private function processMooraNormalization($dataMatrix)
    // {
    //     $criteria = ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan', 'biaya'];
    //     $weights = ['minat' => 0.25, 'keahlian' => 0.25, 'tingkat' => 0.15, 'hadiah' => 0.15, 'penutupan' => 0.10, 'biaya' => 0.10];
    //     $benefitCriteria = ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan'];
    //     $costCriteria = ['biaya'];

    //     if (empty($dataMatrix)) {
    //         return [];
    //     }

    //     // Hitung pembagi normalisasi
    //     $divisors = [];
    //     foreach ($criteria as $c) {
    //         $sumOfSquares = array_sum(array_map(fn($data) => pow($data['values'][$c], 2), $dataMatrix));
    //         $divisors[$c] = $sumOfSquares > 0 ? sqrt($sumOfSquares) : 1;
    //     }

    //     $results = [];
    //     foreach ($dataMatrix as $item) {
    //         $normalizedValues = [];
    //         $weightedValues = [];
    //         $totalBenefitScore = 0;
    //         $totalCostScore = 0;

    //         foreach ($criteria as $c) {
    //             $normalized = $divisors[$c] != 0 ? $item['values'][$c] / $divisors[$c] : 0;
    //             $weighted = $normalized * $weights[$c];
    //             $normalizedValues[$c] = $normalized;
    //             $weightedValues[$c] = $weighted;

    //             if (in_array($c, $benefitCriteria)) {
    //                 $totalBenefitScore += $weighted;
    //             } else {
    //                 $totalCostScore += $weighted;
    //             }
    //         }

    //         $mooraScore = $totalBenefitScore - $totalCostScore;

    //         $results[] = [
    //             'lomba' => $item['lomba'],
    //             'asli' => $item['values'],                     // Matriks keputusan awal
    //             'normalisasi' => $normalizedValues,           // Hasil normalisasi
    //             'terbobot' => $weightedValues,                // Dikalikan bobot
    //             'benefit' => round($totalBenefitScore, 4),    // Total benefit
    //             'cost' => round($totalCostScore, 4),          // Total cost
    //             'score' => round($mooraScore, 4),             // Skor akhir
    //         ];
    //     }

    //     // Urutkan berdasarkan skor MOORA
    //     usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
    //     return $results;
    // }

    //     public function getList(Request $request)
    //     {
    //         $query = LombaModel::query()->with(['bidangTerkait', 'daftarHadiah']);

    //         $mooraScoresMap = [];
    //         $isRekomendasiMode = $request->rekomendasi == 1;

    //         if ($isRekomendasiMode) {
    //             $userId = Auth::id();
    //             if ($userId) {
    //                 $mooraResults = $this->calculateMooraScores($userId);
    //                 $orderedLombaIds = [];
    //                 foreach ($mooraResults as $result) {
    //                     $mooraScoresMap[$result['lomba']->lomba_id] = $result['score'];
    //                     $orderedLombaIds[] = $result['lomba']->lomba_id;
    //                 }

    //                 if (empty($orderedLombaIds)) {
    //                     return DataTables::of(collect())->make(true);
    //                 }

    //                 $query->whereIn('lomba_id', $orderedLombaIds)
    //                     ->orderByRaw("FIELD(lomba_id, " . implode(',', $orderedLombaIds) . ")");
    //             } else {
    //                 return DataTables::of(collect())->make(true);
    //             }
    //         } else {
    //             if ($request->filled('search_nama')) {
    //                 $query->where('nama_lomba', 'like', '%' . $request->search_nama . '%');
    //             }

    //             if ($request->filled('filter_status')) {
    //                 $status = strtolower($request->filter_status);
    //                 $today = Carbon::now('Asia/Jakarta');

    //                 if ($status == 'buka') {
    //                     $query->where(function ($q) use ($today) {
    //                         $q->whereNull('pembukaan_pendaftaran')
    //                             ->orWhere('pembukaan_pendaftaran', '<=', $today);
    //                     })->where(function ($q) use ($today) {
    //                         $q->whereNull('batas_pendaftaran')
    //                             ->orWhere('batas_pendaftaran', '>=', $today);
    //                     });
    //                 } elseif ($status == 'tutup') {
    //                     $query->whereNotNull('batas_pendaftaran')
    //                         ->where('batas_pendaftaran', '<', $today);
    //                 } elseif ($status == 'segera hadir') {
    //                     $query->whereNotNull('pembukaan_pendaftaran')
    //                         ->where('pembukaan_pendaftaran', '>', $today);
    //                 }
    //             }
    //         }

    //         return DataTables::of($query)
    //             ->addIndexColumn()
    //             ->addColumn('kategori', function ($lomba) {
    //                 return $lomba->detailBidang->map(function ($detail) {
    //                     return ucfirst($detail->kategori) . ': ' . ($detail->bidang->bidang_nama ?? 'N/A');
    //                 })->implode(', ');
    //             })
    //             ->addColumn('pembukaan_pendaftaran', function ($lomba) {
    //                 return $lomba->pembukaan_pendaftaran
    //                     ? Carbon::parse($lomba->pembukaan_pendaftaran)->setTimezone('Asia/Jakarta')->isoFormat('D MMMM YYYY')
    //                     : 'N/A';
    //             })
    //             ->addColumn('batas_pendaftaran', function ($lomba) {
    //                 return $lomba->batas_pendaftaran
    //                     ? Carbon::parse($lomba->batas_pendaftaran)->setTimezone('Asia/Jakarta')->isoFormat('D MMMM YYYY')
    //                     : 'N/A';
    //             })
    //             ->addColumn('moora_score', function ($lomba) use ($isRekomendasiMode, $mooraScoresMap) {
    //                 return $isRekomendasiMode && isset($mooraScoresMap[$lomba->lomba_id])
    //                     ? number_format($mooraScoresMap[$lomba->lomba_id], 4)
    //                     : '-';
    //             })
    //             ->addColumn('status', function ($lomba) {
    //                 $statusDisplay = $lomba->status_pendaftaran_display;
    //                 $badgeClass = match (strtolower($statusDisplay)) {
    //                     'buka' => 'success',
    //                     'tutup' => 'danger',
    //                     'segera hadir' => 'warning',
    //                     default => 'secondary'
    //                 };
    //                 return '<span class="badge bg-' . $badgeClass . '">' . e(ucfirst($statusDisplay)) . '</span>';
    //             })
    //             ->addColumn('aksi', function ($lomba) {
    //                 $detailUrl = route('lomba.show', $lomba->lomba_id);
    //                 return '<div class="text-center btn-group btn-group-sm">'
    //                     . '<a href="' . e($detailUrl) . '" class="btn btn-info btn-sm" title="Lihat Detail">'
    //                     . '<i class="fas fa-eye"></i> Detail</a></div>';
    //             })
    //             ->rawColumns(['status', 'aksi'])
    //             ->make(true);
    //     }

    /**
     * Hitung MOORA Scores untuk user tertentu, 
     * sekaligus menyimpan setiap tahapan perhitungan.
     */
    // private function calculateMooraScores($userId)
    // {
    //     // Ambil user beserta relasi minat & keahlian
    //     $user = \App\Models\UserModel::with(['minat', 'keahlian'])->find($userId);
    //     if (!$user) {
    //         return [];
    //     }

    //     // Array bidang_id yang jadi minat dan keahlian user
    //     $userMinatIds = $user->minat->pluck('bidang_id')->toArray();
    //     $userKeahlianIds = $user->keahlian->pluck('bidang_id')->toArray();

    //     // Ambil semua lomba yang status pendaftaran masih berlaku
    //     $lombas = LombaModel::with(['detailBidang', 'daftarHadiah'])
    //         ->where(function ($query) {
    //             $query->where('batas_pendaftaran', '>=', Carbon::now()->toDateString())
    //                 ->orWhereNull('batas_pendaftaran');
    //         })
    //         ->get();

    //     if ($lombas->isEmpty()) {
    //         return [];
    //     }

    //     // Siapkan matriks keputusan awal
    //     $dataMatrix = [];
    //     foreach ($lombas as $lomba) {
    //         $row = [];

    //         // Pisahkan bidang 'minat' dan 'keahlian' pada detailBidang
    //         $lombaMinatBidangIds = $lomba->detailBidang
    //             ->pluck('bidang_id')
    //             ->toArray();

    //         $lombaKeahlianBidangIds = $lomba->detailBidang
    //             ->pluck('bidang_id')
    //             ->toArray();

    //         // 1. Skor 'minat': 1 jika ada irisan bidang minat antara lomba & user
    //         $row['minat'] = count(array_intersect($lombaMinatBidangIds, $userMinatIds)) > 0 ? 1 : 0;

    //         // 2. Skor 'keahlian': 1 jika ada irisan bidang keahlian antara lomba & user
    //         $row['keahlian'] = count(array_intersect($lombaKeahlianBidangIds, $userKeahlianIds)) > 0 ? 1 : 0;

    //         // 3. Skor 'tingkat': lokal=1, kota/kab=2, provinsi=3, nasional=4, internasional=5
    //         $tingkatStr = strtolower($lomba->tingkat ?? '');
    //         $row['tingkat'] = match ($tingkatStr) {
    //             'lokal' => 1,
    //             'kota' => 2,
    //             'kabupaten' => 2,
    //             'provinsi' => 3,
    //             'nasional' => 4,
    //             'internasional' => 5,
    //             default => 0,
    //         };

    //         // 4. Skor 'hadiah': jumlah item pada daftarHadiah
    //         $row['hadiah'] = $lomba->daftarHadiah->count();

    //         // 5. Skor 'penutupan': 
    //         //    hitung selisih hari dari sekarang ke batas_pendaftaran, lalu konversi
    //         if ($lomba->batas_pendaftaran) {
    //             $selisihHari = Carbon::now()->diffInDays(Carbon::parse($lomba->batas_pendaftaran), false);
    //             $row['penutupan'] = match (true) {
    //                 $selisihHari < 0 => 0,
    //                 $selisihHari == 0 => 1,
    //                 $selisihHari <= 7 => 2,
    //                 $selisihHari <= 14 => 3,
    //                 $selisihHari <= 30 => 4,
    //                 default => 5,
    //             };
    //         } else {
    //             // Tidak ada batas pendaftaran → nilai maksimal
    //             $row['penutupan'] = 5;
    //         }

    //         // 6. Skor 'biaya': nilai biaya pendaftaran (cost)
    //         $row['biaya'] = (float) ($lomba->biaya ?? 0);

    //         $dataMatrix[] = [
    //             'lomba' => $lomba,
    //             'values' => $row,
    //         ];
    //     }

    //     // Kirim ke proses normalisasi & hitung MOORA lengkap
    //     return $this->processMooraNormalization($dataMatrix);
    // }

    // /**
    //  * Proses normalisasi, pemberian bobot, hitung benefit/cost, dan skor akhir MOORA.
    //  * Mengembalikan array detail perhitungan untuk tiap lomba.
    //  */
    // private function processMooraNormalization($dataMatrix)
    // {
    //     // Daftar kriteria sesuai urutan
    //     $criteria = ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan', 'biaya'];

    //     // Bobot per kriteria (total = 1)
    //     $weights = [
    //         'minat' => 0.25,
    //         'keahlian' => 0.25,
    //         'tingkat' => 0.15,
    //         'hadiah' => 0.15,
    //         'penutupan' => 0.10,
    //         'biaya' => 0.10,
    //     ];

    //     // Tentukan mana yang benefit, mana cost
    //     $benefitCriteria = ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan'];
    //     $costCriteria = ['biaya'];

    //     // Jika dataMatrix kosong
    //     if (empty($dataMatrix)) {
    //         return [];
    //     }

    //     // 1. Hitung divisor (akar jumlah kuadrat) untuk tiap kriteria
    //     $divisors = [];
    //     foreach ($criteria as $c) {
    //         $sumOfSquares = array_sum(array_map(fn($data) => pow($data['values'][$c], 2), $dataMatrix));
    //         $divisors[$c] = ($sumOfSquares > 0) ? sqrt($sumOfSquares) : 1;
    //     }

    //     $results = [];
    //     foreach ($dataMatrix as $item) {
    //         // 2. Hitung nilai normalisasi per kriteria
    //         $normalizedValues = [];
    //         foreach ($criteria as $c) {
    //             $normalizedValues[$c] = $divisors[$c] != 0
    //                 ? $item['values'][$c] / $divisors[$c]
    //                 : 0;
    //         }

    //         // 3. Hitung nilai terbobot (normalized * bobot)
    //         $weightedValues = [];
    //         foreach ($criteria as $c) {
    //             $weightedValues[$c] = $normalizedValues[$c] * $weights[$c];
    //         }

    //         // 4. Hitung total benefit score dan total cost score
    //         $totalBenefitScore = 0;
    //         foreach ($benefitCriteria as $c) {
    //             $totalBenefitScore += $weightedValues[$c];
    //         }

    //         $totalCostScore = 0;
    //         foreach ($costCriteria as $c) {
    //             $totalCostScore += $weightedValues[$c];
    //         }

    //         // 5. Skor akhir MOORA = (sum benefit) - (sum cost)
    //         $mooraScore = $totalBenefitScore - $totalCostScore;

    //         // Simpan detail untuk lomba ini
    //         $results[] = [
    //             'lomba' => $item['lomba'],
    //             'asli' => $item['values'],         // Matriks keputusan awal
    //             'normalisasi' => $normalizedValues,        // Nilai setelah normalisasi
    //             'terbobot' => $weightedValues,          // Nilai setelah dikali bobot
    //             'benefit' => round($totalBenefitScore, 4),
    //             'cost' => round($totalCostScore, 4),
    //             'score' => round($mooraScore, 4),    // Skor MOORA akhir
    //         ];
    //     }

    //     // 6. Urutkan berdasarkan skor MOORA (descending)
    //     usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

    //     return $results;
    // }

    /**
     * Method untuk DataTables server-side. 
     * Jika request->rekomendasi == 1, panggil perhitungan MOORA dan sisipkan skor-nya.
     */
    // public function getList(Request $request)
    // {
    //     $query = LombaModel::query()->with(['detailBidang', 'daftarHadiah']);

    //     $mooraScoresMap = [];
    //     $isRekomendasiMode = ($request->input('rekomendasi') == 1);

    //     if ($isRekomendasiMode) {
    //         $userId = Auth::id();
    //         if ($userId) {
    //             // Hitung MOORA untuk user saat ini
    //             $mooraResults = $this->calculateMooraScores($userId);
    //             $orderedLombaIds = [];

    //             foreach ($mooraResults as $result) {
    //                 $lombaModel = $result['lomba'];
    //                 $mooraScoresMap[$lombaModel->lomba_id] = $result; // simpan seluruh detail
    //                 $orderedLombaIds[] = $lombaModel->lomba_id;
    //             }

    //             if (!empty($orderedLombaIds)) {
    //                 // Filter dan urutkan sesuai urutan perhitungan MOORA
    //                 $query->whereIn('lomba_id', $orderedLombaIds)
    //                     ->orderByRaw("FIELD(lomba_id, " . implode(',', $orderedLombaIds) . ")");
    //             }
    //         }
    //     } else {
    //         // Normal mode (fitur filter nama/status)
    //         if ($request->filled('search_nama')) {
    //             $query->where('nama_lomba', 'like', '%' . $request->search_nama . '%');
    //         }

    //         if ($request->filled('filter_status')) {
    //             $status = strtolower($request->filter_status);
    //             $today = Carbon::now('Asia/Jakarta');

    //             if ($status == 'buka') {
    //                 $query->where(function ($q) use ($today) {
    //                     $q->whereNull('pembukaan_pendaftaran')
    //                         ->orWhere('pembukaan_pendaftaran', '<=', $today);
    //                 })->where(function ($q) use ($today) {
    //                     $q->whereNull('batas_pendaftaran')
    //                         ->orWhere('batas_pendaftaran', '>=', $today);
    //                 });
    //             } elseif ($status == 'tutup') {
    //                 $query->whereNotNull('batas_pendaftaran')
    //                     ->where('batas_pendaftaran', '<', $today);
    //             } elseif ($status == 'segera hadir') {
    //                 $query->whereNotNull('pembukaan_pendaftaran')
    //                     ->where('pembukaan_pendaftaran', '>', $today);
    //             }
    //         }
    //     }

    //     return DataTables::of($query)
    //         ->addIndexColumn()
    //         ->addColumn('kategori', function ($lomba) {
    //             return $lomba->detailBidang
    //                 ->map(fn($detail) => ucfirst($detail->kategori) . ': ' . ($detail->bidang->bidang_nama ?? 'N/A'))
    //                 ->implode(', ');
    //         })
    //         ->addColumn('pembukaan_pendaftaran', function ($lomba) {
    //             return $lomba->pembukaan_pendaftaran
    //                 ? Carbon::parse($lomba->pembukaan_pendaftaran)
    //                 ->setTimezone('Asia/Jakarta')
    //                 ->isoFormat('D MMMM YYYY')
    //                 : 'N/A';
    //         })
    //         ->addColumn('batas_pendaftaran', function ($lomba) {
    //             return $lomba->batas_pendaftaran
    //                 ? Carbon::parse($lomba->batas_pendaftaran)
    //                 ->setTimezone('Asia/Jakarta')
    //                 ->isoFormat('D MMMM YYYY')
    //                 : 'N/A';
    //         })
    //         ->addColumn('moora_score', function ($lomba) use ($isRekomendasiMode, $mooraScoresMap) {
    //             if ($isRekomendasiMode && isset($mooraScoresMap[$lomba->lomba_id])) {
    //                 // Tampilkan skor akhir (rounded, 4 desimal)
    //                 $detail = $mooraScoresMap[$lomba->lomba_id];
    //                 return number_format($detail['score'], 4);
    //             }
    //             return '-';
    //         })
    //         ->addColumn('status', function ($lomba) {
    //             $today = Carbon::now('Asia/Jakarta');
    //             if ($lomba->pembukaan_pendaftaran && $today->lt($lomba->pembukaan_pendaftaran)) {
    //                 return 'Segera Hadir';
    //             } elseif (
    //                 (!$lomba->pembukaan_pendaftaran || $today->gte($lomba->pembukaan_pendaftaran)) &&
    //                 (!$lomba->batas_pendaftaran || $today->lte($lomba->batas_pendaftaran))
    //             ) {
    //                 return 'Buka';
    //             } elseif ($lomba->batas_pendaftaran && $today->gt($lomba->batas_pendaftaran)) {
    //                 return 'Tutup';
    //             } else {
    //                 return 'Tidak Diketahui';
    //             }
    //         })
    //         ->addColumn('aksi', function ($lomba) use ($mooraScoresMap, $isRekomendasiMode) {
    //             // Tombol "Detail Perhitungan" hanya muncul saat mode rekomendasi aktif
    //             $btn = '<a href="javascript:void(0)" data-id="' . $lomba->lomba_id . '" '
    //                 . 'class="btn btn-sm btn-primary btn-detail-hitungan" '
    //                 . 'data-bs-toggle="tooltip" title="Lihat Langkah Perhitungan">'
    //                 . '<i class="fas fa-calculator"></i></a>';

    //             return '<div class="text-center btn-group btn-group-sm">' . $btn . '</div>';
    //         })
    //         ->rawColumns(['aksi'])
    //         ->make(true);
    // }

    /**
     * Method AJAX untuk mengambil detail perhitungan MOORA satu lomba.
     * Akan dipanggil ketika tombol "btn-detail-hitungan" ditekan.
     */
    public function detailMoora(Request $request)
{
    $lombaId = $request->input('lomba_id');
    $userId = Auth::id();

    // Ambil custom weights jika dikirim
    $customWeightsInput = $request->input('weights', []);
    $criteriaKeys = ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan', 'biaya'];
    $customWeights = [];
    $totalInputWeight = 0;

    foreach ($criteriaKeys as $key) {
        if (isset($customWeightsInput[$key]) && is_numeric($customWeightsInput[$key])) {
            $customWeights[$key] = (float) $customWeightsInput[$key];
            $totalInputWeight += $customWeights[$key];
        }
    }

    // Normalisasi jika perlu
    if ($totalInputWeight > 0 && abs($totalInputWeight - 1.0) > 0.001) {
        foreach ($customWeights as $key => $val) {
            $customWeights[$key] = $val / $totalInputWeight;
        }
    }

    $mooraResults = $this->calculateMooraScores($userId, $customWeights);

    $detail = [];
    foreach ($mooraResults as $item) {
        if ($item['lomba']->lomba_id == $lombaId) {
            $detail = $item;
            break;
        }
    }

    if (empty($detail)) {
        return response()->json(['error' => 'Data perhitungan tidak ditemukan.'], 404);
    }

    return view('lomba.partial_detail_perhitungan', compact('detail'));
}


    // Fungsi ini akan dipanggil oleh DataTables di view lomba/index.blade.php
    public function getList(Request $request)
    {
        // Eager load relasi yang akan sering diakses
        $query = LombaModel::query()
            ->with([
                'bidangKeahlian.bidang', // Untuk menampilkan bidang lomba
                'daftarHadiah'          // Untuk kriteria hadiah
            ])
            ->where('status_verifikasi', 'disetujui'); // Hanya lomba yang disetujui

        $mooraScoresMap = [];
        $isRekomendasiMode = $request->rekomendasi === '1'; // Gunakan perbandingan ketat

        if ($isRekomendasiMode) {
            $userId = Auth::id();
            if ($userId) {
                $customWeightsInput = $request->input('weights', []);
                $customWeights = [];
                // Validasi dan normalisasi bobot dari input pengguna
                // Pastikan key-nya sesuai dengan yang ada di form slider (minat, keahlian, tingkat, hadiah, penutupan, biaya)
                $criteriaKeys = ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan', 'biaya'];
                $totalInputWeight = 0;
                foreach ($criteriaKeys as $key) {
                    if (isset($customWeightsInput[$key]) && is_numeric($customWeightsInput[$key])) {
                        $weightValue = (float) $customWeightsInput[$key];
                        $customWeights[$key] = $weightValue;
                        $totalInputWeight += $weightValue;
                    } else {
                        // Jika ada bobot yang tidak valid atau tidak ada, mungkin fallback ke default atau error
                        // Untuk sekarang, kita akan biarkan dan processMooraNormalization akan handle default
                    }
                }

                // Jika total input tidak 0, normalisasi agar totalnya 1 (atau 100 jika Anda mengirim % dari view)
                // Di JavaScript kita kirim sebagai desimal (0-1) yang sudah dinormalisasi totalnya 1
                // Jadi di sini kita bisa langsung pakai, atau validasi lagi
                if ($totalInputWeight > 0 && abs($totalInputWeight - 1.0) > 0.001 && abs($totalInputWeight - 100.0) > 0.001) {
                    // Jika dari JS belum dinormalisasi jadi 1, lakukan di sini
                    // Tapi dari JS sebelumnya, kita sudah bagi 100, jadi seharusnya sudah mendekati 1
                }


                $mooraResults = $this->calculateMooraScores($userId, $customWeights);

                $orderedLombaIds = [];
                foreach ($mooraResults as $result) {
                    $mooraScoresMap[$result['lomba']->lomba_id] = $result['score'];
                    $orderedLombaIds[] = $result['lomba']->lomba_id;
                }

                if (empty($orderedLombaIds)) {
                    return DataTables::of(collect())->addIndexColumn()->rawColumns(['status_display', 'aksi', 'biaya_display'])->make(true);
                }
                $query->whereIn('lomba_id', $orderedLombaIds)
                    ->orderByRaw("FIELD(lomba_id, " . implode(',', array_map('intval', $orderedLombaIds)) . ")");
            } else {
                return DataTables::of(collect())->addIndexColumn()->rawColumns(['status_display', 'aksi', 'biaya_display'])->make(true);
            }
        } else {
            if ($request->filled('search_nama')) {
                $query->where('nama_lomba', 'like', '%' . $request->search_nama . '%');
            }
            if ($request->filled('filter_status')) {
                $status = strtolower($request->filter_status);
                $today = Carbon::now('Asia/Jakarta')->startOfDay();

                if ($status == 'buka') {
                    $query->where(function ($q) use ($today) {
                        $q->whereNull('pembukaan_pendaftaran')
                            ->orWhereDate('pembukaan_pendaftaran', '<=', $today);
                    })->where(function ($q) use ($today) {
                        $q->whereNull('batas_pendaftaran')
                            ->orWhereDate('batas_pendaftaran', '>=', $today);
                    });
                } elseif ($status == 'tutup') {
                    $query->whereNotNull('batas_pendaftaran')
                        ->whereDate('batas_pendaftaran', '<', $today);
                } elseif ($status == 'segera hadir') {
                    $query->whereNotNull('pembukaan_pendaftaran')
                        ->whereDate('pembukaan_pendaftaran', '>', $today);
                }
            }
            $query->orderBy('batas_pendaftaran', 'asc');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('bidang_display', function ($lomba) {
                if ($lomba->bidangKeahlian && $lomba->bidangKeahlian->count() > 0) {
                    return $lomba->bidangKeahlian->map(function ($detail) {
                        return $detail->bidang->bidang_nama ?? '';
                    })->filter()->implode(', ');
                }
                return '-';
            })
            ->editColumn('pembukaan_pendaftaran', function ($lomba) {
                return $lomba->pembukaan_pendaftaran
                    ? Carbon::parse($lomba->pembukaan_pendaftaran)->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY')
                    : 'N/A';
            })
            ->editColumn('batas_pendaftaran', function ($lomba) {
                return $lomba->batas_pendaftaran
                    ? Carbon::parse($lomba->batas_pendaftaran)->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY')
                    : 'N/A';
            })
            ->addColumn('biaya_display', function ($lomba) { // Menggunakan nama kolom berbeda untuk display
                return $lomba->biaya > 0 ? 'Rp ' . number_format($lomba->biaya, 0, ',', '.') : '<span class="badge bg-light-success text-success px-2 py-1">Gratis</span>';
            })
            ->addColumn('status_display', function ($lomba) { // Menggunakan nama kolom berbeda untuk display
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
                $btnDetail = '<button onclick="modalActionLomba(\'' . route('lomba.publik.show_ajax', $lomba->lomba_id) . '\', \'Detail Lomba\', \'modalDetailLombaPublik\')" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i>Detail</button>';
                $btnHitung = '<button class="btn btn-sm btn-outline-secondary btn-detail-hitungan" data-lomba-id="' . $lomba->lomba_id . '"><i class="fas fa-calculator me-1"></i>Hitungan</button>';

                return '<div class="text-center">' . $btnDetail . $btnHitung . '</div>';
            })
            ->rawColumns(['status_display', 'aksi', 'biaya_display'])
            ->make(true);
    }

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
            $row['hadiah'] = $lomba->daftarHadiah->count() > 0 ? ($lomba->daftarHadiah->count() <= 5 ? $lomba->daftarHadiah->count() : 5) : 0; // Max skor 5

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
                    $row['penutupan'] = 5;                     // > 1 bulan
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
            if ($totalCustomWeight > 0) {
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
    public function indexLomba() // Ini yang dipanggil oleh route lomba.index
    {
        $userRole = Auth::user()->role;
        // $breadcrumb diatur sesuai kebutuhan, atau bisa dihilangkan jika tidak dipakai di view ini
        $breadcrumb = (object) ['title' => 'Informasi & Rekomendasi Lomba', 'list' => ['Info Lomba', 'Rekomendasi Lomba']];
        $activeMenu = 'info_lomba'; // Atau 'lomba_mahasiswa'

        // Nama-nama kriteria yang akan digunakan di view untuk slider bobot
        $kriteriaUntukBobot = [
            'minat' => 'Kesesuaian Minat',
            'keahlian' => 'Kesesuaian Keahlian',
            'tingkat' => 'Tingkat Lomba',
            'hadiah' => 'Potensi Hadiah',
            'penutupan' => 'Sisa Waktu Pendaftaran',
            'biaya' => 'Biaya Pendaftaran (Rendah Lebih Baik)',
        ];

        // Bobot default (dalam persentase untuk ditampilkan di slider)
        $defaultBobotView = [
            'minat' => 25,
            'keahlian' => 25,
            'tingkat' => 15,
            'hadiah' => 10,
            'penutupan' => 15,
            'biaya' => 10
        ];


        return view('lomba.mahasiswa.index', compact('breadcrumb', 'activeMenu', 'userRole', 'kriteriaUntukBobot', 'defaultBobotView'));
    }

    // private const BOBOT_POSISI_PRIORITAS = [
    //     1 => 0.30, // Prioritas 1
    //     2 => 0.25, // Prioritas 2
    //     3 => 0.20, // Prioritas 3
    //     4 => 0.10, // Prioritas 4
    //     5 => 0.10, // Prioritas 5
    //     6 => 0.05, // Prioritas 6
    // ];

    // // Kunci kriteria yang akan digunakan secara konsisten
    // private const KRITERIA_KEYS = ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan', 'biaya'];

    // public function indexLomba()
    // {
    //     $userRole = Auth::user()->role;
    //     $breadcrumb = (object) ['title' => 'Informasi & Rekomendasi Lomba', 'list' => ['Lomba']];
    //     $activeMenu = 'info_lomba';

    //     // Kriteria yang bisa diurutkan oleh pengguna
    //     // Label ini akan ditampilkan di view
    //     $kriteriaList = [
    //         'minat' => 'Kesesuaian Minat dengan Profil Anda',
    //         'keahlian' => 'Kesesuaian Keahlian dengan Profil Anda',
    //         'tingkat' => 'Tingkat Kesulitan/Prestise Lomba',
    //         'hadiah' => 'Potensi/Jumlah Hadiah',
    //         'penutupan' => 'Sisa Waktu Pendaftaran (Lama Lebih Baik)',
    //         'biaya' => 'Biaya Pendaftaran (Rendah Lebih Baik)',
    //     ];

    //     // Urutan default kriteria (bisa juga disimpan per user jika ingin lebih personal)
    //     $defaultUrutanKriteria = self::KRITERIA_KEYS;


    //     return view('lomba.mahasiswa.index', compact('breadcrumb', 'activeMenu', 'userRole', 'kriteriaList', 'defaultUrutanKriteria'));
    // }

    // public function getList(Request $request)
    // {
    //     $query = LombaModel::query()
    //         ->with(['bidangKeahlian.bidang', 'daftarHadiah'])
    //         ->where('status_verifikasi', 'disetujui');

    //     $mooraScoresMap = [];
    //     $isRekomendasiMode = $request->rekomendasi === '1';

    //     if ($isRekomendasiMode) {
    //         $userId = Auth::id();
    //         if ($userId) {
    //             // Ambil urutan kriteria dari request
    //             // Format yang diharapkan: ['kriteria_di_posisi_1', 'kriteria_di_posisi_2', ...]
    //             $urutanKriteriaInput = $request->input('urutan_kriteria', []);

    //             $finalWeights = [];
    //             if (count($urutanKriteriaInput) == count(self::KRITERIA_KEYS)) {
    //                 $posisi = 1;
    //                 foreach ($urutanKriteriaInput as $kriteriaKey) {
    //                     if (in_array($kriteriaKey, self::KRITERIA_KEYS) && isset(self::BOBOT_POSISI_PRIORITAS[$posisi])) {
    //                         $finalWeights[$kriteriaKey] = self::BOBOT_POSISI_PRIORITAS[$posisi];
    //                     }
    //                     $posisi++;
    //                 }
    //             }

    //             // Jika finalWeights tidak lengkap, gunakan default (meskipun idealnya JS memastikan urutan dikirim lengkap)
    //             if (count($finalWeights) !== count(self::KRITERIA_KEYS)) {
    //                 // Logika fallback jika urutan tidak lengkap/valid, misal gunakan bobot default merata atau error
    //                 // Untuk sekarang, kita biarkan processMooraNormalization memakai default weights jika customWeights kosong/tidak valid
    //                 $finalWeights = []; // Kosongkan agar processMooraNormalization pakai default
    //             }


    //             $mooraResults = $this->calculateMooraScores($userId, $finalWeights);

    //             $orderedLombaIds = [];
    //             foreach ($mooraResults as $result) {
    //                 $mooraScoresMap[$result['lomba']->lomba_id] = $result['score'];
    //                 $orderedLombaIds[] = $result['lomba']->lomba_id;
    //             }

    //             if (empty($orderedLombaIds)) {
    //                 return DataTables::of(collect())->addIndexColumn()->rawColumns(['status_display', 'aksi', 'biaya_display'])->make(true);
    //             }
    //             $query->whereIn('lomba_id', $orderedLombaIds)
    //                 ->orderByRaw("FIELD(lomba_id, " . implode(',', array_map('intval', $orderedLombaIds)) . ")");
    //         } else {
    //             return DataTables::of(collect())->addIndexColumn()->rawColumns(['status_display', 'aksi', 'biaya_display'])->make(true);
    //         }
    //     } else {
    //         // ... (logika filter pencarian biasa tetap sama) ...
    //         if ($request->filled('search_nama')) {
    //             $query->where('nama_lomba', 'like', '%' . $request->search_nama . '%');
    //         }
    //         if ($request->filled('filter_status')) {
    //             $status = strtolower($request->filter_status);
    //             $today = Carbon::now('Asia/Jakarta')->startOfDay();

    //             if ($status == 'buka') {
    //                 $query->where(function ($q) use ($today) {
    //                     $q->whereNull('pembukaan_pendaftaran')
    //                         ->orWhereDate('pembukaan_pendaftaran', '<=', $today);
    //                 })->where(function ($q) use ($today) {
    //                     $q->whereNull('batas_pendaftaran')
    //                         ->orWhereDate('batas_pendaftaran', '>=', $today);
    //                 });
    //             } elseif ($status == 'tutup') {
    //                 $query->whereNotNull('batas_pendaftaran')
    //                     ->whereDate('batas_pendaftaran', '<', $today);
    //             } elseif ($status == 'segera hadir') {
    //                 $query->whereNotNull('pembukaan_pendaftaran')
    //                     ->whereDate('pembukaan_pendaftaran', '>', $today);
    //             }
    //         }
    //         $query->orderBy('batas_pendaftaran', 'asc');
    //     }

    //     return DataTables::of($query)
    //         ->addIndexColumn()
    //         ->addColumn('bidang_display', function ($lomba) {
    //             if ($lomba->bidangKeahlian && $lomba->bidangKeahlian->count() > 0) {
    //                 return $lomba->bidangKeahlian->map(function ($detail) {
    //                     return $detail->bidang->bidang_nama ?? '';
    //                 })->filter()->implode(', ');
    //             }
    //             return '-';
    //         })
    //         ->editColumn('batas_pendaftaran', function ($lomba) {
    //             return $lomba->batas_pendaftaran
    //                 ? Carbon::parse($lomba->batas_pendaftaran)->setTimezone('Asia/Jakarta')->isoFormat('D MMM yyyy') // Format konsisten
    //                 : 'N/A';
    //         })
    //         ->addColumn('biaya_display', function ($lomba) {
    //             return $lomba->biaya > 0 ? 'Rp ' . number_format($lomba->biaya, 0, ',', '.') : '<span class="badge bg-light-success text-success px-2 py-1">Gratis</span>';
    //         })
    //         ->addColumn('status_display', function ($lomba) {
    //             $statusDisplay = $lomba->status_pendaftaran_display;
    //             $badgeClass = match (strtolower($statusDisplay)) {
    //                 'buka' => 'success',
    //                 'tutup' => 'danger',
    //                 'segera hadir' => 'warning',
    //                 default => 'secondary'
    //             };
    //             return '<span class="badge bg-light-' . $badgeClass . ' text-' . $badgeClass . ' px-2 py-1">' . e(ucfirst($statusDisplay)) . '</span>';
    //         })
    //         ->addColumn('moora_score', function ($lomba) use ($isRekomendasiMode, $mooraScoresMap) {
    //             return $isRekomendasiMode && isset($mooraScoresMap[$lomba->lomba_id])
    //                 ? number_format($mooraScoresMap[$lomba->lomba_id], 4)
    //                 : '-';
    //         })
    //         ->addColumn('aksi', function ($lomba) {
    //             $btnDetail = '<button onclick="modalActionLomba(\'' . route('lomba.publik.show_ajax', $lomba->lomba_id) . '\', \'Detail Lomba\', \'modalDetailLombaPublik\')" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i>Detail</button>';
    //             return '<div class="text-center">' . $btnDetail . '</div>';
    //         })
    //         ->rawColumns(['status_display', 'aksi', 'biaya_display'])
    //         ->make(true);
    // }

    // // calculateMooraScores dan processMooraNormalization tetap sama seperti di respons sebelumnya yang sudah
    // // dimodifikasi untuk menerima $customWeights (yang sekarang akan berasal dari pemetaan urutan prioritas)
    // // Pastikan $customWeights yang diterima oleh processMooraNormalization memiliki key yang sesuai
    // // dengan self::KRITERIA_KEYS dan value bobotnya (0-1).
    // private function calculateMooraScores($userId, $customWeights = []) // $customWeights: ['minat' => 0.3, 'keahlian' => 0.25, dst]
    // {
    //     $user = UserModel::with(['minatBidang', 'keahlianBidang'])->find($userId);
    //     if (!$user) {
    //         return [];
    //     }

    //     $userMinatIds = $user->minatBidang->pluck('bidang_id')->toArray();
    //     $userKeahlianIds = $user->keahlianBidang->pluck('bidang_id')->toArray();

    //     $lombas = LombaModel::with(['bidangKeahlian.bidang', 'daftarHadiah'])
    //         ->where('status_verifikasi', 'disetujui')
    //         ->where(function ($query) {
    //             $query->where('batas_pendaftaran', '>=', Carbon::now()->toDateString())
    //                 ->orWhereNull('batas_pendaftaran');
    //         })
    //         ->get();

    //     if ($lombas->isEmpty()) {
    //         return [];
    //     }

    //     $dataMatrix = [];
    //     foreach ($lombas as $lomba) {
    //         $row = [];
    //         $lombaBidangIds = $lomba->bidangKeahlian->pluck('bidang.bidang_id')->filter()->toArray();

    //         $row['minat'] = count(array_intersect($lombaBidangIds, $userMinatIds)) > 0 ? 5 : 1; // Skor 1-5 (placeholder, bisa lebih kompleks)
    //         $row['keahlian'] = count(array_intersect($lombaBidangIds, $userKeahlianIds)) > 0 ? 5 : 1; // Skor 1-5
    //         $row['tingkat'] = match (strtolower($lomba->tingkat ?? '')) {
    //             'lokal' => 2,
    //             'kota' => 2,
    //             'kabupaten' => 2,
    //             'provinsi' => 3,
    //             'nasional' => 4,
    //             'internasional' => 5,
    //             default => 1,
    //         };
    //         $row['hadiah'] = $lomba->daftarHadiah->count() > 0 ? ($lomba->daftarHadiah->count() <= 5 ? $lomba->daftarHadiah->count() : 5) : 1;

    //         if ($lomba->batas_pendaftaran) {
    //             $sisaHari = Carbon::now()->diffInDays(Carbon::parse($lomba->batas_pendaftaran), false);
    //             if ($sisaHari < 0) $row['penutupan'] = 1;
    //             elseif ($sisaHari == 0) $row['penutupan'] = 2;
    //             elseif ($sisaHari <= 7) $row['penutupan'] = 3;
    //             elseif ($sisaHari <= 30) $row['penutupan'] = 4;
    //             else $row['penutupan'] = 5;
    //         } else {
    //             $row['penutupan'] = 5;
    //         }
    //         $row['biaya'] = (float) ($lomba->biaya ?? 0);

    //         $dataMatrix[] = ['lomba' => $lomba, 'values' => $row];
    //     }
    //     return $this->processMooraNormalization($dataMatrix, $customWeights);
    // }

    // private function processMooraNormalization($dataMatrix, $customWeights = [])
    // {
    //     $criteria = self::KRITERIA_KEYS; // ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan', 'biaya']

    //     // Bobot default jika $customWeights kosong atau tidak valid
    //     $defaultWeights = [
    //         'minat' => 0.25,
    //         'keahlian' => 0.25,
    //         'tingkat' => 0.15,
    //         'hadiah' => 0.10,
    //         'penutupan' => 0.15,
    //         'biaya' => 0.10
    //     ];

    //     $weights = $defaultWeights;
    //     // Gunakan customWeights jika valid (semua key ada dan totalnya 1 atau bisa dinormalisasi)
    //     if (!empty($customWeights) && count(array_intersect_key(array_flip($criteria), $customWeights)) === count($criteria)) {
    //         $totalCustomWeight = array_sum($customWeights);
    //         if ($totalCustomWeight > 0.00001) { // Hindari pembagian dengan nol jika semua bobot 0
    //             $normalizedCustomWeights = [];
    //             foreach ($customWeights as $key => $val) {
    //                 $normalizedCustomWeights[$key] = $val / $totalCustomWeight; // Normalisasi agar total = 1
    //             }
    //             $weights = $normalizedCustomWeights;
    //         } else if ($totalCustomWeight == 0 && count($customWeights) == count($criteria)) {
    //             // Jika semua bobot custom adalah 0, beri bobot merata
    //             $equalWeight = 1 / count($criteria);
    //             foreach ($criteria as $c) {
    //                 $weights[$c] = $equalWeight;
    //             }
    //         }
    //     }

    //     $benefitCriteria = ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan'];
    //     $costCriteria = ['biaya'];

    //     if (empty($dataMatrix)) {
    //         return [];
    //     }

    //     // Normalisasi Matriks Keputusan (Akar dari jumlah kuadrat)
    //     $divisors = [];
    //     foreach ($criteria as $c) {
    //         $sumOfSquares = array_sum(array_map(fn($data) => pow($data['values'][$c], 2), $dataMatrix));
    //         $divisors[$c] = $sumOfSquares > 0 ? sqrt($sumOfSquares) : 1;
    //     }

    //     $results = [];
    //     foreach ($dataMatrix as $item) {
    //         $normalizedValues = [];
    //         foreach ($criteria as $c) {
    //             $normalizedValues[$c] = $divisors[$c] != 0 ? $item['values'][$c] / $divisors[$c] : 0;
    //         }

    //         // Hitung skor optimasi (Yi)
    //         $optimasiScore = 0;
    //         foreach ($benefitCriteria as $c) {
    //             $optimasiScore += ($normalizedValues[$c] * ($weights[$c] ?? 0));
    //         }
    //         foreach ($costCriteria as $c) {
    //             $optimasiScore -= ($normalizedValues[$c] * ($weights[$c] ?? 0));
    //         }

    //         $results[] = [
    //             'lomba' => $item['lomba'],
    //             'score' => round($optimasiScore, 6), // Tingkatkan presisi skor
    //         ];
    //     }

    //     // Urutkan hasil berdasarkan skor tertinggi
    //     usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
    //     return $results;
    // }

    public function create()
    {
        return view('lomba.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lomba' => 'required|string|max:255',
            'pembukaan_pendaftaran' => 'required|date',
            'kategori' => 'required|in:akademik,non-akademik,lainnya',
            'penyelenggara' => 'required|string|max:255',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'link_pendaftaran' => 'nullable|string|max:255',
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
            'nama_lomba' => 'required|string|max:255',
            'pembukaan_pendaftaran' => 'required|date',
            'kategori' => 'required|in:akademik,non-akademik,lainnya',
            'penyelenggara' => 'required|string|max:255',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'link_pendaftaran' => 'nullable|string|max:255',
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
                'bidangKeahlian.bidang' // Eager load LombaDetailModel dan relasi bidang di dalamnya
            ])
            ->findOrFail($id);
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
                    return '<button onclick="modalActionLomba(\'' . route('lomba.dosen.show_form', $row->lomba_id) . '\', \'Detail Lomba\', \'modalDetailLomba\')" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i>Detail</button>';
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
            'title' => 'Histori Pengajuan Info Lomba Saya',
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

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama_lomba', fn($row) => e($row->nama_lomba))
                ->addColumn('bidang_lomba', function ($lomba) {
                    return $lomba->detailBidang->map(function ($detail) {
                        return ucfirst($detail->kategori) . ' ' . ($detail->bidang->bidang_nama ?? 'N/A');
                    })->implode(', ');
                })
                ->editColumn('batas_pendaftaran', fn($row) => $row->batas_pendaftaran ? Carbon::parse($row->batas_pendaftaran)->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY') : '-')
                ->editColumn('created_at', fn($row) => $row->created_at ? Carbon::parse($row->created_at)->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY') : '-')
                ->editColumn('status_verifikasi', fn($row) => $row->status_verifikasi_badge)
                ->addColumn('aksi', function ($row) {
                    $btnEdit = '';
                    $btnDetail = '<button onclick="modalActionLomba(\'' . route('lomba.mhs.show_form', $row->lomba_id) . '\', \'Detail Lomba\', \'modalFormLombaUser\')" class="btn btn-sm btn-info me-1" title="Detail"><i class="fas fa-eye"></i></button>';
                    if ($row->status_verifikasi == 'ditolak' || $row->status_verifikasi == 'pending') {
                        $btnEdit = '<button onclick="modalActionLomba(\'' . route('lomba.mhs.edit_form', $row->lomba_id) . '\', \'Edit Pengajuan\', \'modalFormLombaUser\')" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>';
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
            'nama_lomba' => 'required|string|max:255',
            'pembukaan_pendaftaran' => 'required|date',
            'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
            'kategori' => 'required|in:individu,kelompok',
            'penyelenggara' => 'required|string|max:255',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|array|min:1',
            'bidang_keahlian.*' => 'exists:bidang,bidang_id', // Validasi setiap item dalam array
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:255',
            'link_penyelenggara' => 'nullable|url|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048', // Tambahkan pdf jika diizinkan
            'hadiah' => 'nullable|array',
            'hadiah.*' => 'nullable|string|max:255',
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

    // public function updateLombaMhs(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'nama_lomba' => 'required|string|max:255',
    //         'pembukaan_pendaftaran' => 'required|date',
    //         'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
    //         'kategori' => 'required|in:individu,kelompok',
    //         'tingkat' => 'required|in:lokal,nasional,internasional',
    //         'penyelenggara' => 'required|string|max:255',
    //         'bidang_keahlian' => 'required|array|min:1',
    //         'bidang_keahlian.*' => 'exists:bidang,bidang_id',
    //         'biaya' => 'nullable|numeric|min:0',
    //         'link_pendaftaran' => 'nullable|url',
    //         'link_penyelenggara' => 'nullable|url',
    //         'poster' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //     ]);

    //     $lomba = LombaModel::findOrFail($id);

    //     // Simpan poster baru jika ada
    //     if ($request->hasFile('poster')) {
    //         if ($lomba->poster && Storage::exists('public/' . $lomba->poster)) {
    //             Storage::delete('public/' . $lomba->poster);
    //         }

    //         $poster = $request->file('poster');
    //         $posterName = time() . '_' . $poster->getClientOriginalName();
    //         $poster->storeAs('public/poster', $posterName);
    //         $validated['poster'] = 'poster/' . $posterName;
    //     }

    //     // Update data utama lomba, kecuali bidang_keahlian
    //     $lomba->update(collect($validated)->except('bidang_keahlian')->toArray());

    //     // Hapus relasi bidang sebelumnya
    //     $lomba->detailBidang()->delete();

    //     // Tambah relasi bidang baru ke lomba_detail
    //     foreach ($validated['bidang_keahlian'] as $bidangId) {
    //         LombaDetailModel::create([
    //             'lomba_id' => $lomba->lomba_id,
    //             'bidang_id' => $bidangId
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Lomba berhasil diperbarui.',
    //     ]);
    // }

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
            'nama_lomba' => 'required|string|max:255',
            'pembukaan_pendaftaran' => 'required|date',
            'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
            'kategori' => 'required|in:individu,kelompok',
            'penyelenggara' => 'required|string|max:255',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|array|min:1',
            'bidang_keahlian.*' => 'exists:bidang,bidang_id',
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:255',
            'link_penyelenggara' => 'nullable|url|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048',
            'hadiah' => 'nullable|array',
            'hadiah.*' => 'nullable|string|max:255',
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

    public function showLombaMhs($id) // Atau showPengajuanLombaUmum
    {
        $user = Auth::user();
        $lomba = LombaModel::where('lomba_id', $id)
            // ->where('diinput_oleh', $user->user_id) // Opsional, jika hanya pemilik yang boleh lihat detail pengajuannya
            ->with(['inputBy', 'bidangKeahlian.bidang', 'daftarHadiah'])
            ->firstOrFail();

        // Jika view Anda adalah 'lomba.show' yang diunggah
        return view('lomba.mahasiswa.show', compact('lomba'));
    }


    /**
     * Menampilkan halaman histori pengajuan lomba untuk user yang login.
     */
    public function historiPengajuanLombaDsn()
    {
        $breadcrumb = (object) [
            'title' => 'Histori Pengajuan Info Lomba Saya',
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

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama_lomba', fn($row) => e($row->nama_lomba))
                ->editColumn('batas_pendaftaran', fn($row) => $row->batas_pendaftaran ? Carbon::parse($row->batas_pendaftaran)->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY') : '-')
                ->editColumn('created_at', fn($row) => $row->created_at ? Carbon::parse($row->created_at)->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY, HH:mm') : '-')
                ->editColumn('status_verifikasi', fn($row) => $row->status_verifikasi_badge)
                ->addColumn('aksi', function ($row) {
                    $btnEdit = '';
                    $btnDetail = '<button onclick="modalActionLomba(\'' . route('lomba.dosen.show_form', $row->lomba_id) . '\', \'Detail Lomba\', \'modalFormLombaUser\')" class="btn btn-sm btn-info me-1" title="Detail"><i class="fas fa-eye"></i></button>';
                    if ($row->status_verifikasi == 'ditolak' || $row->status_verifikasi == 'pending') {
                        $btnEdit = '<button onclick="modalActionLomba(\'' . route('lomba.dosen.edit_form', $row->lomba_id) . '\', \'Edit Pengajuan\', \'modalFormLombaUser\')" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>';
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
            'nama_lomba' => 'required|string|max:255',
            'pembukaan_pendaftaran' => 'required|date',
            'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
            'kategori' => 'required|in:individu,kelompok',
            'penyelenggara' => 'required|string|max:255',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|array|min:1',
            'bidang_keahlian.*' => 'exists:bidang,bidang_id', // Validasi setiap item dalam array
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:255',
            'link_penyelenggara' => 'nullable|url|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048', // Tambahkan pdf jika diizinkan
            'hadiah' => 'nullable|array',
            'hadiah.*' => 'nullable|string|max:255',
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
            'nama_lomba' => 'required|string|max:255',
            'pembukaan_pendaftaran' => 'required|date',
            'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
            'kategori' => 'required|in:individu,kelompok',
            'penyelenggara' => 'required|string|max:255',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|array|min:1',
            'bidang_keahlian.*' => 'exists:bidang,bidang_id',
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:255',
            'link_penyelenggara' => 'nullable|url|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048',
            'hadiah' => 'nullable|array',
            'hadiah.*' => 'nullable|string|max:255',
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
            'nama_lomba' => 'required|string|max:255',
            'pembukaan_pendaftaran' => 'required|date',
            'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
            'kategori' => 'required|in:individu,kelompok',
            'penyelenggara' => 'required|string|max:255',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|array|min:1',
            'bidang_keahlian.*' => 'exists:bidang,bidang_id',
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:255',
            'link_penyelenggara' => 'nullable|url|max:255',
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
        $breadcrumb = (object) ['title' => 'Verifikasi Pengajuan Lomba', 'list' => ['Lomba', 'Verifikasi Lomba']];
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
                    $btnDetail = '<button onclick="modalActionLombaAdminCrud(\'' . route('lomba.publik.show_ajax', $row->lomba_id) . '\', \'Detail Lomba\', \'modalDetailLombaAdminCrud\')" class="btn btn-sm btn-info me-1" title="Detail"><i class="fas fa-eye"></i></button>';

                    $btnEdit = '<button onclick="modalActionLombaAdminCrud(\'' . route('admin.lomba.crud.edit_form_ajax', $row->lomba_id) . '\', \'Edit Lomba\', \'modalFormLombaAdminCrud\')" class="btn btn-sm btn-warning me-1" title="Edit"><i class="fas fa-edit"></i></button>';

                    $btnDelete = '<button onclick="modalActionLombaAdminCrud(\'' . route('admin.lomba.crud.confirm_delete_ajax', $row->lomba_id) . '\', \'Konfirmasi Hapus Lomba\', \'modalConfirmDeleteLombaAdminCrud\')" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>';

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

    // Menyimpan lomba BARU yang diinput admin (AJAX) tanpa hadiah
    // public function adminStoreLombaAjax(Request $request)
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
    //         'status_verifikasi' => 'disetujui',
    //         'diinput_oleh' => $user->user_id,
    //         'poster' => $posterPath,
    //     ]);

    //     // Simpan bidang ke tabel lomba_detail
    //     foreach ($request->bidang_keahlian as $bidangId) {
    //         LombaDetailModel::create([
    //             'lomba_id' => $lomba->lomba_id,
    //             'bidang_id' => $bidangId
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Info lomba baru berhasil ditambahkan.'
    //     ]);
    // }

    // Tambah hadiah
    public function adminStoreLombaAjax(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'nama_lomba' => 'required|string|max:255',
            'pembukaan_pendaftaran' => 'required|date',
            'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
            'kategori' => 'required|in:individu,kelompok',
            'penyelenggara' => 'required|string|max:255',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|array|min:1',
            'bidang_keahlian.*' => 'exists:bidang,bidang_id',
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:255',
            'link_penyelenggara' => 'nullable|url|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'hadiah' => 'nullable|array', // Tambahkan validasi untuk hadiah
            'hadiah.*' => 'nullable|string|max:255', // Setiap item hadiah adalah string
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
            'nama_lomba' => 'required|string|max:255',
            'pembukaan_pendaftaran' => 'required|date',
            'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
            'kategori' => 'required|in:individu,kelompok',
            'penyelenggara' => 'required|string|max:255',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|array|min:1',
            'bidang_keahlian.*' => 'exists:bidang,bidang_id',
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:255',
            'link_penyelenggara' => 'nullable|url|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'hadiah' => 'nullable|array',
            'hadiah.*' => 'nullable|string|max:255',
        ];

        // Tambahkan validasi status_verifikasi dan catatan_verifikasi jika request datang dari admin
        // (Anda bisa menambahkan parameter $isAdmin ke request atau cek role di sini)
        if (Auth::user()->role === 'admin') {
            $rules['status_verifikasi'] = 'required|in:pending,disetujui,ditolak';
            $rules['catatan_verifikasi'] = 'nullable|string|max:1000';
        }


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validasi tambahan untuk catatan jika ditolak (jika form dikirim oleh admin)
        if (Auth::user()->role === 'admin' && $request->status_verifikasi == 'ditolak' && empty(trim($request->catatan_verifikasi ?? ''))) {
            return response()->json([
                'status' => false,
                'message' => 'Catatan verifikasi wajib diisi jika status ditolak.',
                'errors' => ['catatan_verifikasi' => ['Catatan verifikasi wajib diisi jika status ditolak.']]
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
            $sheet->setCellValue('F' . $baris, $lomba->pembukaan_pendaftaran);
            $sheet->setCellValue('G' . $baris, $lomba->batas_pendaftaran);

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
