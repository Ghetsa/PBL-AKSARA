<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\LombaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;


class LombaController extends Controller
{
    //=================================================
    //|        METHOD UNTUK MAHASISWA DAN DOSEN       |
    //=================================================
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Lomba',
            'list' => ['Dashboard', 'Lomba']
        ];

        $activeMenu = 'lomba';

        return view('lomba.mahasiswa.index', compact('breadcrumb', 'activeMenu'));
    }

    private function calculateMooraScores($userId)
    {
        $user = UserModel::with(['minat', 'keahlian'])->find($userId);
        if (!$user) {
            return [];
        }

        $userMinatIds = $user->minat->pluck('minat_id')->toArray();
        $userKeahlianIds = $user->keahlian->pluck('keahlian_id')->toArray();

        $lombas = LombaModel::with(['detailBidang', 'daftarHadiah'])
            ->where(function ($query) {
                $query->where('batas_pendaftaran', '>=', Carbon::now()->toDateString())
                    ->orWhereNull('batas_pendaftaran');
            })->get();

        if ($lombas->isEmpty()) {
            return [];
        }

        $dataMatrix = [];

        foreach ($lombas as $lomba) {
            $row = [];

            $lombaMinatBidangIds = $lomba->detailBidang
                ->where('kategori', 'minat')
                ->pluck('bidang_id')
                ->toArray();
            $row['minat'] = !empty(array_intersect($lombaMinatBidangIds, $userMinatIds)) ? 1 : 0;

            $lombaKeahlianBidangIds = $lomba->detailBidang
                ->where('kategori', 'keahlian')
                ->pluck('bidang_id')
                ->toArray();
            $row['keahlian'] = !empty(array_intersect($lombaKeahlianBidangIds, $userKeahlianIds)) ? 1 : 0;

            $row['tingkat'] = match (strtolower($lomba->tingkat ?? '')) {
                'lokal' => 1, 'kota' => 2, 'kabupaten' => 2, 'provinsi' => 3,
                'nasional' => 4, 'internasional' => 5, default => 0,
            };

            $row['hadiah'] = $lomba->daftarHadiah->count();

            if ($lomba->batas_pendaftaran) {
                $selisihHari = Carbon::now()->diffInDays(Carbon::parse($lomba->batas_pendaftaran), false);
                $skorHari = match (true) {
                    $selisihHari < 0 => 0,
                    $selisihHari == 0 => 1,
                    $selisihHari <= 7 => 2,
                    $selisihHari <= 14 => 3,
                    $selisihHari <= 30 => 4,
                    default => 5,
                };
            } else {
                $skorHari = 5;
            }
            $row['penutupan'] = $skorHari;

            $row['biaya'] = (float) ($lomba->biaya ?? 0);

            $dataMatrix[] = [
                'lomba' => $lomba,
                'values' => $row,
            ];
        }
        return $this->processMooraNormalization($dataMatrix);
    }

    private function processMooraNormalization($dataMatrix)
    {
        $criteria = ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan', 'biaya'];
        $weights = ['minat' => 0.25, 'keahlian' => 0.25, 'tingkat' => 0.15, 'hadiah' => 0.15, 'penutupan' => 0.10, 'biaya' => 0.10];
        $benefitCriteria = ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan'];
        $costCriteria = ['biaya'];

        if (empty($dataMatrix)) {
            return [];
        }

        $divisors = [];
        foreach ($criteria as $c) {
            $sumOfSquares = array_sum(array_map(fn($data) => pow($data['values'][$c], 2), $dataMatrix));
            $divisors[$c] = $sumOfSquares > 0 ? sqrt($sumOfSquares) : 1;
        }

        $results = [];
        foreach ($dataMatrix as $item) {
            $normalizedValues = [];
            foreach ($criteria as $c) {
                $normalizedValues[$c] = $divisors[$c] != 0 ? $item['values'][$c] / $divisors[$c] : 0;
            }

            $totalBenefitScore = 0;
            foreach ($benefitCriteria as $c) {
                $totalBenefitScore += $normalizedValues[$c] * $weights[$c];
            }

            $totalCostScore = 0;
            foreach ($costCriteria as $c) {
                $totalCostScore += $normalizedValues[$c] * $weights[$c];
            }

            $mooraScore = $totalBenefitScore - $totalCostScore;
            $results[] = [
                'lomba' => $item['lomba'],
                'score' => round($mooraScore, 4),
            ];
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
        return $results;
    }

    public function getList(Request $request)
    {
        $query = LombaModel::query()->with(['bidangTerkait', 'daftarHadiah']);

        $mooraScoresMap = [];
        $isRekomendasiMode = $request->rekomendasi == 1;

        if ($isRekomendasiMode) {
            $userId = Auth::id();
            if ($userId) {
                $mooraResults = $this->calculateMooraScores($userId);
                $orderedLombaIds = [];
                foreach ($mooraResults as $result) {
                    $mooraScoresMap[$result['lomba']->lomba_id] = $result['score'];
                    $orderedLombaIds[] = $result['lomba']->lomba_id;
                }

                if (empty($orderedLombaIds)) {
                    return DataTables::of(collect())->make(true);
                }

                $query->whereIn('lomba_id', $orderedLombaIds)
                    ->orderByRaw("FIELD(lomba_id, " . implode(',', $orderedLombaIds) . ")");
            } else {
                return DataTables::of(collect())->make(true);
            }
        } else {
            if ($request->filled('search_nama')) {
                $query->where('nama_lomba', 'like', '%' . $request->search_nama . '%');
            }

            if ($request->filled('filter_status')) {
                $status = strtolower($request->filter_status);
                $today = Carbon::now()->toDateString();

                if ($status == 'buka') {
                    $query->where(function ($q) use ($today) {
                        $q->whereNull('pembukaan_pendaftaran')
                            ->orWhere('pembukaan_pendaftaran', '<=', $today);
                    })->where(function ($q) use ($today) {
                        $q->whereNull('batas_pendaftaran')
                            ->orWhere('batas_pendaftaran', '>=', $today);
                    });
                } elseif ($status == 'tutup') {
                    $query->whereNotNull('batas_pendaftaran')
                        ->where('batas_pendaftaran', '<', $today);
                } elseif ($status == 'segera hadir') {
                    $query->whereNotNull('pembukaan_pendaftaran')
                        ->where('pembukaan_pendaftaran', '>', $today);
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kategori', function ($lomba) {
                return $lomba->detailBidang->map(function ($detail) {
                    return ucfirst($detail->kategori) . ': ' . ($detail->bidang->bidang_nama ?? 'N/A');
                })->implode(', ');
            })
            ->addColumn('pembukaan_pendaftaran', function ($lomba) {
                return $lomba->pembukaan_pendaftaran
                    ? Carbon::parse($lomba->pembukaan_pendaftaran)->isoFormat('D MMMM YYYY')
                    : 'N/A';
            })
            ->addColumn('batas_pendaftaran', function ($lomba) {
                return $lomba->batas_pendaftaran
                    ? Carbon::parse($lomba->batas_pendaftaran)->isoFormat('D MMMM YYYY')
                    : 'N/A';
            })
            ->addColumn('moora_score', function ($lomba) use ($isRekomendasiMode, $mooraScoresMap) {
                return $isRekomendasiMode && isset($mooraScoresMap[$lomba->lomba_id])
                    ? number_format($mooraScoresMap[$lomba->lomba_id], 4)
                    : '-';
            })
            ->addColumn('status', function ($lomba) {
                $statusDisplay = $lomba->status_pendaftaran_display;
                $badgeClass = match (strtolower($statusDisplay)) {
                    'buka' => 'success',
                    'tutup' => 'danger',
                    'segera hadir' => 'warning',
                    default => 'secondary'
                };
                return '<span class="badge bg-' . $badgeClass . '">' . e(ucfirst($statusDisplay)) . '</span>';
            })
            ->addColumn('aksi', function ($lomba) {
                $detailUrl = route('lomba.show', $lomba->lomba_id);
                return '<div class="text-center btn-group btn-group-sm">'
                    . '<a href="' . e($detailUrl) . '" class="btn btn-info btn-sm" title="Lihat Detail">'
                    . '<i class="fas fa-eye"></i> Detail</a></div>';
            })
            ->rawColumns(['status', 'aksi'])
            ->make(true);
    }

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


    //===================================
    //|        METHOD UNTUK ADMIN       |
    //===================================
    public function indexAdmin()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Lomba',
            'list' => ['Dashboard', 'Lomba']
        ];

        $activeMenu = 'lomba';

        return view('lomba.mahasiswa.index', compact('breadcrumb', 'activeMenu'));
    }

    public function getListAdmin(Request $request)
    {
        $query = LombaModel::query();

        // Filter pencarian
        if ($request->filled('search_nama')) {
            $search = $request->search_nama;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lomba', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('filter_status')) {
            $query->where('status_verifikasi', $request->filter_status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                // return view('components.lomba.aksi-buttons', compact('editUrl', 'verifUrl', 'deleteUrl'));
                $btn = '<button onclick="modalAction(\'' . e(route('lomba.show', $row->lomba_id)) . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . e(route('lomba.edit', $row->lomba_id)) . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="deleteConfirmAjax(' . e($row->lomba_id) . ')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })

            ->addColumn('status_verifikasi', function ($row) {
                switch ($row->status_verifikasi) {
                    case 'pending':
                        return '<span class="badge bg-warning text-dark">Pending</span>';
                    case 'disetujui':
                        return '<span class="badge bg-success">Disetujui</span>';
                    case 'ditolak':
                        return '<span class="badge bg-danger">Ditolak</span>';
                    default:
                        return '<span class="badge bg-secondary">Tidak Diketahui</span>';
                }
            })

            ->editColumn('pembukaan_pendaftaran', function ($row) {
                return \Carbon\Carbon::parse($row->pembukaan_pendaftaran)->format('d-m-Y');
            })
            ->editColumn('batas_pendaftaran', function ($row) {
                return \Carbon\Carbon::parse($row->batas_pendaftaran)->format('d-m-Y');
            })
            ->rawColumns(['aksi', 'status_verifikasi'])
            ->make(true);
    }

    public function destroy($id)
    {
        LombaModel::destroy($id);
        return redirect()->route('lomba.admin.index')->with('success', 'Lomba berhasil dihapus');
    }

    public function verifikasi($id)
    {
        $data = LombaModel::findOrFail($id);
        return view('lomba.verifikasi', compact('data'));
    }

    public function prosesVerifikasi(Request $request, $id)
    {
        $validated = $request->validate([
            'status_verifikasi' => 'required|in:pending,disetujui,ditolak',
            'catatan_verifikasi' => 'nullable|string'
        ]);

        $data = LombaModel::findOrFail($id);
        $data->update($validated);

        return redirect()->route('lomba.index')->with('success', 'Verifikasi berhasil diperbarui');
    }
}
