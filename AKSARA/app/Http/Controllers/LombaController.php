<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\LombaModel;
use App\Models\BidangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // Untuk logging

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

    public function indexLomba()
    {
        $userRole = Auth::user()->role;
        $breadcrumb = (object) ['title' => 'Informasi Lomba', 'list' => ['Lomba']];
        $activeMenu = 'info_lomba';

        // view yang sama untuk daftar lomba yang disetujui.
        return view('lomba.index', compact('breadcrumb', 'activeMenu', 'userRole'));
    }

    // // DataTables untuk daftar lomba yang disetujui (dilihat semua role)
    // public function listLombaPublik(Request $request) // Ganti nama agar lebih jelas
    // {
    //     if ($request->ajax()) {
    //         $data = LombaModel::where('status_verifikasi', 'disetujui')
    //             ->orderBy('batas_pendaftaran', 'asc');

    //         return DataTables::of($data)
    //             ->addIndexColumn()
    //             ->editColumn('nama_lomba', function ($row) {
    //                 $html = '<span class="fw-semibold">' . e($row->nama_lomba) . '</span>';
    //                 if ($row->poster && Storage::disk('public')->exists($row->poster)) {
    //                     $html .= '<br><a href="' . asset('storage/' . $row->poster) . '" target="_blank" class="badge bg-light-info text-info mt-1">Lihat Poster</a>';
    //                 }
    //                 return $html;
    //             })
    //             ->addColumn('periode_pendaftaran', function ($row) {
    //                 $mulai = $row->pembukaan_pendaftaran ? $row->pembukaan_pendaftaran->format('d M Y') : 'N/A';
    //                 $selesai = $row->batas_pendaftaran ? $row->batas_pendaftaran->format('d M Y') : 'N/A';
    //                 return $mulai . ' - ' . $selesai;
    //             })
    //             ->addColumn('biaya_formatted', fn($row) => $row->biaya > 0 ? 'Rp ' . number_format($row->biaya, 0, ',', '.') : '<span class="badge bg-light-success text-success">Gratis</span>')
    //             ->addColumn('aksi', function ($row) {
    //                 $btn = '<button onclick="modalActionLomba(\'' . route('lomba.show.ajax', $row->lomba_id) . '\', \'Detail Lomba\')" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i>Detail</button>';
    //                 return $btn;
    //             })
    //             ->rawColumns(['nama_lomba', 'biaya_formatted', 'aksi'])
    //             ->make(true);
    //     }
    //     return abort(403);
    // }

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
                'lokal' => 1,
                'kota' => 2,
                'kabupaten' => 2,
                'provinsi' => 3,
                'nasional' => 4,
                'internasional' => 5,
                default => 0,
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


    //===================================
    //|        METHOD UNTUK ADMIN       |
    //===================================
    // public function indexAdmin()
    // {
    //     $breadcrumb = (object) [
    //         'title' => 'Manajemen Lomba',
    //         'list' => ['Dashboard', 'Lomba']
    //     ];

    //     $activeMenu = 'lomba';

    //     return view('lomba.mahasiswa.index', compact('breadcrumb', 'activeMenu'));
    // }

    // public function getListAdmin(Request $request)
    // {
    //     $query = LombaModel::query();

    //     // Filter pencarian
    //     if ($request->filled('search_nama')) {
    //         $search = $request->search_nama;
    //         $query->where(function ($q) use ($search) {
    //             $q->where('nama_lomba', 'like', '%' . $search . '%');
    //         });
    //     }

    //     if ($request->filled('filter_status')) {
    //         $query->where('status_verifikasi', $request->filter_status);
    //     }

    //     return DataTables::of($query)
    //         ->addIndexColumn()
    //         ->addColumn('aksi', function ($row) {
    //             // return view('components.lomba.aksi-buttons', compact('editUrl', 'verifUrl', 'deleteUrl'));
    //             $btn = '<button onclick="modalAction(\'' . e(route('lomba.show', $row->lomba_id)) . '\')" class="btn btn-info btn-sm">Detail</button> ';
    //             $btn .= '<button onclick="modalAction(\'' . e(route('lomba.edit', $row->lomba_id)) . '\')" class="btn btn-warning btn-sm">Edit</button> ';
    //             $btn .= '<button onclick="deleteConfirmAjax(' . e($row->lomba_id) . ')" class="btn btn-danger btn-sm">Hapus</button>';
    //             return $btn;
    //         })

    //         ->addColumn('status_verifikasi', function ($row) {
    //             switch ($row->status_verifikasi) {
    //                 case 'pending':
    //                     return '<span class="badge bg-warning text-dark">Pending</span>';
    //                 case 'disetujui':
    //                     return '<span class="badge bg-success">Disetujui</span>';
    //                 case 'ditolak':
    //                     return '<span class="badge bg-danger">Ditolak</span>';
    //                 default:
    //                     return '<span class="badge bg-secondary">Tidak Diketahui</span>';
    //             }
    //         })

    //         ->editColumn('pembukaan_pendaftaran', function ($row) {
    //             return \Carbon\Carbon::parse($row->pembukaan_pendaftaran)->format('d-m-Y');
    //         })
    //         ->editColumn('batas_pendaftaran', function ($row) {
    //             return \Carbon\Carbon::parse($row->batas_pendaftaran)->format('d-m-Y');
    //         })
    //         ->rawColumns(['aksi', 'status_verifikasi'])
    //         ->make(true);
    // }

    // public function destroy($id)
    // {
    //     LombaModel::destroy($id);
    //     return redirect()->route('lomba.admin.index')->with('success', 'Lomba berhasil dihapus');
    // }

    // public function verifikasi($id)
    // {
    //     $data = LombaModel::findOrFail($id);
    //     return view('lomba.verifikasi', compact('data'));
    // }

    // public function prosesVerifikasi(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'status_verifikasi' => 'required|in:pending,disetujui,ditolak',
    //         'catatan_verifikasi' => 'nullable|string'
    //     ]);

    //     $data = LombaModel::findOrFail($id);
    //     $data->update($validated);

    //     return redirect()->route('lomba.index')->with('success', 'Verifikasi berhasil diperbarui');
    // }

    // =======================================================================
    // METHOD UNTUK TAMPILAN PUBLIK (SEMUA USER LOGIN BISA AKSES)
    // =======================================================================

    /**
     * Menampilkan halaman daftar lomba yang sudah disetujui (untuk semua role).
     */
    public function indexLombaPublik()
    {
        $userRole = Auth::user()->role;
        $breadcrumb = (object) ['title' => 'Informasi Lomba Terkini', 'list' => ['Beranda', 'Info Lomba']];
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
                    $mulai = $row->pembukaan_pendaftaran ? Carbon::parse($row->pembukaan_pendaftaran)->isoFormat('D MMM YYYY') : 'N/A';
                    $selesai = $row->batas_pendaftaran ? Carbon::parse($row->batas_pendaftaran)->isoFormat('D MMM YYYY') : 'N/A';
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

    /**
     * Menampilkan detail lomba yang disetujui dalam modal (AJAX).
     */
    public function showAjaxLombaPublik($id)
    {
        $lomba = LombaModel::where('status_verifikasi', 'disetujui')->with('inputBy')->findOrFail($id);
        return view('lomba.publik.show_ajax', compact('lomba'));
    }

    // =======================================================================
    // METHOD UNTUK MAHASISWA & DOSEN (PENGAJUAN LOMBA & HISTORI)
    // =======================================================================

    /**
     * Menampilkan halaman histori pengajuan lomba untuk user yang login.
     */
    public function historiPengajuanLomba()
    {
        $breadcrumb = (object) [
            'title' => 'Histori Pengajuan Info Lomba Saya',
            'list' => ['Info Lomba', 'Histori Pengajuan']
        ];
        $activeMenu = 'histori_lomba_user';
        return view('lomba.histori_pengajuan_lomba', compact('breadcrumb', 'activeMenu'));
    }

    /**
     * Menyediakan data untuk DataTables histori pengajuan lomba user.
     */
    public function listHistoriPengajuanLomba(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $data = LombaModel::where('diinput_oleh', $user->user_id)
                ->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama_lomba', fn($row) => e($row->nama_lomba))
                ->editColumn('batas_pendaftaran', fn($row) => $row->batas_pendaftaran ? Carbon::parse($row->batas_pendaftaran)->isoFormat('D MMM YYYY') : '-')
                ->editColumn('created_at', fn($row) => $row->created_at ? Carbon::parse($row->created_at)->isoFormat('D MMM YYYY, HH:mm') : '-')
                ->editColumn('status_verifikasi', fn($row) => $row->status_verifikasi_badge)
                ->addColumn('aksi', function ($row) {
                    $catatan = $row->catatan_verifikasi ?? ''; // Pastikan ada kolom catatan_verifikasi di tabel lomba
                    $btnEdit = '';
                    $btnDetail = '<button onclick="modalActionLomba(\'' . route('lomba.show', $row->lomba_id) . '\', \'Detail Lomba\', \'modalDetailLomba\')" class="btn btn-sm btn-info me-1" title="Detail"><i class="fas fa-eye"></i></button>';
                    if ($row->status_verifikasi == 'ditolak' || $row->status_verifikasi == 'pending') {
                        $btnEdit = '<button onclick="modalActionLomba(\'' . route('lomba.edit', $row->lomba_id) . '\', \'Edit Pengajuan\', \'modalFormLombaUser\')" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>';
                    }
                    return '<div class="btn-group">' . $btnDetail . $btnEdit . '</div>';
                    return '-';
                })
                ->rawColumns(['status_verifikasi', 'aksi'])
                ->make(true);
        }
        return abort(403);
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
            'bidang_keahlian' => 'required|array',
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:255',
            'link_penyelenggara' => 'nullable|url|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('lomba_poster', 'public');
        }

        LombaModel::create([
            'nama_lomba' => $request->nama_lomba,
            'pembukaan_pendaftaran' => $request->pembukaan_pendaftaran,
            'batas_pendaftaran' => $request->batas_pendaftaran,
            'kategori' => $request->kategori,
            'penyelenggara' => $request->penyelenggara,
            'tingkat' => $request->tingkat,
            'bidang_keahlian' => implode(',', $request->bidang_keahlian),
            'biaya' => $request->biaya ?? 0,
            'link_pendaftaran' => $request->link_pendaftaran,
            'link_penyelenggara' => $request->link_penyelenggara,
            'poster' => $posterPath,
            'status_verifikasi' => 'pending', // Status default untuk pengajuan user
            'diinput_oleh' => $user->user_id,
        ]);

        return response()->json(['status' => true, 'message' => 'Pengajuan info lomba berhasil dikirim dan akan diverifikasi oleh Admin.']);
    }

    // =======================================================================
    // METHOD UNTUK ADMIN: VERIFIKASI LOMBA
    // =======================================================================
    public function adminIndexVerifikasiLomba()
    {
        $breadcrumb = (object) ['title' => 'Verifikasi Pengajuan Lomba', 'list' => ['Admin', 'Verifikasi Lomba']];
        $activeMenu = 'admin_verifikasi_lomba';
        return view('lomba.admin.verifikasi.index', compact('breadcrumb', 'activeMenu'));
    }

    public function adminListVerifikasiLomba(Request $request) // DataTables untuk halaman verifikasi
    {
        if ($request->ajax()) {
            $data = LombaModel::with(['inputBy' => function ($query) {
                // Hanya ambil field yang dibutuhkan dari user untuk mengurangi data
                $query->select('user_id', 'nama', 'role');
            }])
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
                    return $row->created_at ? Carbon::parse($row->created_at)->isoFormat('D MMM YYYY, HH:mm') : '-';
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

    public function adminShowVerifyFormAjax($id)
    {
        $lomba = LombaModel::with('inputBy')->findOrFail($id);
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
        $breadcrumb = (object) ['title' => 'Manajemen Data Lomba', 'list' => ['Admin', 'Kelola Lomba']];
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
                ->editColumn('batas_pendaftaran', fn($row) => $row->batas_pendaftaran ? Carbon::parse($row->batas_pendaftaran)->isoFormat('D MMM YYYY') : '-')
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

    // Menyimpan lomba BARU yang diinput admin (AJAX)
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
            'bidang_keahlian' => 'required|array', 
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:255',
            'link_penyelenggara' => 'nullable|url|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('lomba_poster', 'public');
        }

        // $bidangKeahlianInput = is_array($request->bidang_keahlian) ? implode(',', $request->bidang_keahlian) : $request->bidang_keahlian;

        LombaModel::create([
            'nama_lomba' => $request->nama_lomba,
            'pembukaan_pendaftaran' => $request->pembukaan_pendaftaran,
            'batas_pendaftaran' => $request->batas_pendaftaran,
            'kategori' => $request->kategori,
            'penyelenggara' => $request->penyelenggara,
            'tingkat' => $request->tingkat,
            'bidang_keahlian' => $request->bidang_keahlian, // Atau $bidangKeahlianInput jika array
            'biaya' => $request->biaya ?? 0,
            'link_pendaftaran' => $request->link_pendaftaran,
            'link_penyelenggara' => $request->link_penyelenggara,
            'status_verifikasi' => 'disetujui', // Lomba dari admin langsung disetujui
            'diinput_oleh' => $user->user_id,
            'poster' => $posterPath,
        ]);
        return response()->json(['status' => true, 'message' => 'Info lomba baru berhasil ditambahkan.']);
    }

    // Menampilkan form EDIT lomba (AJAX) untuk admin
    public function adminEditLombaFormAjax($id)
    {
        $lomba = LombaModel::findOrFail($id);
        // $bidangList = BidangModel::orderBy('bidang_nama')->get(); // Jika bidang_keahlian adalah multi-select
        // return view('lomba.admin.crud.edit_form_ajax', compact('lomba', 'bidangList'));
        return view('lomba.admin.crud.edit_lomba', compact('lomba'));
    }

    // Memproses UPDATE lomba oleh admin (AJAX)
    public function adminUpdateLombaAjax(Request $request, $id)
    {
        $lomba = LombaModel::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'nama_lomba' => 'required|string|max:255',
            'pembukaan_pendaftaran' => 'required|date',
            'batas_pendaftaran' => 'required|date|after_or_equal:pembukaan_pendaftaran',
            'kategori' => 'required|in:individu,kelompok',
            'penyelenggara' => 'required|string|max:255',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|string|max:255', // Jika teks biasa, atau 'required|array'
            'biaya' => 'nullable|integer|min:0',
            'link_pendaftaran' => 'nullable|url|max:255',
            'link_penyelenggara' => 'nullable|url|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status_verifikasi' => 'required|in:pending,disetujui,ditolak' // Admin bisa ubah status saat edit
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $updateData = $request->except(['_token', '_method', 'poster']);
        $updateData['biaya'] = $request->biaya ?? 0;
        // $updateData['bidang_keahlian'] = is_array($request->bidang_keahlian) ? implode(',', $request->bidang_keahlian) : $request->bidang_keahlian;

        if ($request->hasFile('poster')) {
            if ($lomba->poster && Storage::disk('public')->exists($lomba->poster)) {
                Storage::disk('public')->delete($lomba->poster);
            }
            $updateData['poster'] = $request->file('poster')->store('lomba_poster', 'public');
        }

        $lomba->update($updateData);
        return response()->json(['status' => true, 'message' => 'Info lomba berhasil diperbarui.']);
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

    // public function adminDestroyLombaAjax($id)
    // {
    //     $lomba = LombaModel::findOrFail($id);
    //     if ($lomba->poster && Storage::disk('public')->exists($lomba->poster)) {
    //         Storage::disk('public')->delete($lomba->poster);
    //     }
    //     $lomba->delete();
    //     return response()->json(['status' => true, 'message' => 'Info lomba berhasil dihapus.']);
    // }
}
