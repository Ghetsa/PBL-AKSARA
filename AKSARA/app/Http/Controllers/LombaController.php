<?php

namespace App\Http\Controllers;

use App\Models\LombaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use DataTables;


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

    protected function calculateMooraRanking(Collection $lomba)
    {
        $user = auth()->user();

        // Ambil array bidang minat + prestasi dari database
        $bidangMinat = $user->minat()->pluck('bidang_id')->toArray();
        $bidangPrestasi = $user->keahlian()->pluck('bidang_id')->toArray();

        $data = $lomba->map(function ($item) use ($bidangMinat, $bidangPrestasi) {
            // 1. Kesesuaian Bidang (Benefit)
            $bidangLomba = array_map('trim', explode(',', strtolower($item->bidang_keahlian)));
            $jumlahBidangLomba = count($bidangLomba) ?: 1;
            $bidangSesuai = count(array_intersect($bidangLomba, $bidangMinat));
            $skorBidang = $bidangSesuai / $jumlahBidangLomba;

            // 2. Kesesuaian Prestasi (Benefit)
            $prestasiSesuai = count(array_intersect($bidangLomba, $bidangPrestasi));
            $skorPrestasi = $prestasiSesuai / $jumlahBidangLomba;

            // 3. Tingkat Lomba (Benefit)
            $skorTingkat = match (strtolower($item->tingkat)) {
                'lokal' => 3,
                'nasional' => 4,
                'internasional' => 5,
                default => 1,
            };

            // 4. Durasi Tersisa (Benefit)
            $durasi = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($item->batas_pendaftaran), false);
            $skorDurasi = match (true) {
                $durasi <= 7 => 1,
                $durasi <= 14 => 2,
                $durasi <= 21 => 3,
                $durasi <= 30 => 4,
                $durasi > 30 => 5,
                default => 1,
            };

            // 5. Biaya (Cost)
            $skorBiaya = ($item->biaya ?? 0) > 0 ? 1 : 2;

            return [
                'model' => $item,
                'nilai' => [
                    $skorBidang,                   // Benefit
                    $skorPrestasi,                 // Benefit
                    $skorTingkat,                  // Benefit
                    $skorDurasi,                   // Benefit
                    1 / $skorBiaya                 // Cost dibalik
                ]
            ];
        });

        // Proses MOORA (normalisasi + skor akhir)
        $matriks = $data->pluck('nilai')->toArray();
        $transpose = array_map(null, ...$matriks);
        $divisor = array_map(fn($col) => sqrt(array_sum(array_map(fn($x) => $x ** 2, $col))), $transpose);

        $normal = collect($matriks)->map(
            fn($baris) =>
            array_map(fn($nilai, $d) => $d == 0 ? 0 : $nilai / $d, $baris, $divisor)
        );

        $moora = $normal->map(
            fn($nilai) =>
            array_sum(array_slice($nilai, 0, 4)) - $nilai[4]
        );

        // Urutkan dan kembalikan koleksi Model Lomba
        return $data->zip($moora)
            ->sortByDesc(fn($x) => $x[1])
            ->values()
            ->map(fn($x) => $x[0]['model']);
    }

    public function getList(Request $request)
    {
        // Jika tombol Rekomendasi ditekan
        if ($request->rekomendasi == 1) {
            // Ambil semua lomba beserta count hadiah
            $lomba = LombaModel::withCount('hadiah')->get();

            // Hitung ranking MOORA berdasarkan data login
            $ranking = $this->calculateMooraRanking($lomba);

            return DataTables::of($ranking)
                ->addIndexColumn()
                ->addColumn('aksi', function ($row) {
                    $btn = '<button onclick="modalAction(\'' . e(route('lomba.show', $row->lomba_id)) . '\')" class="btn btn-info btn-sm">Detail</button> ';
                    $btn .= '<button onclick="modalAction(\'' . e(route('lomba.edit', $row->lomba_id)) . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                    $btn .= '<button onclick="deleteConfirmAjax(' . e($row->lomba_id) . ')" class="btn btn-danger btn-sm">Hapus</button>';
                    return $btn;
                })
                ->addColumn('status_verifikasi', fn($row) => match ($row->status_verifikasi) {
                    'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
                    'disetujui' => '<span class="badge bg-success">Disetujui</span>',
                    'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
                    default => '<span class="badge bg-secondary">Tidak Diketahui</span>',
                })
                ->editColumn('bidang_keahlian', fn() => '') // sembunyikan
                ->editColumn('pembukaan_pendaftaran', fn($row) => \Carbon\Carbon::parse($row->pembukaan_pendaftaran)->format('d-m-Y'))
                ->editColumn('batas_pendaftaran', fn($row) => \Carbon\Carbon::parse($row->batas_pendaftaran)->format('d-m-Y'))
                ->rawColumns(['aksi', 'status_verifikasi'])
                ->make(true);
        }

        // MODE NORMAL TANPA SPK
        $query = LombaModel::query();

        if ($request->filled('search_nama')) {
            $search = $request->search_nama;
            $query->where(
                fn($q) =>
                $q->where('nama_lomba', 'like', "%{$search}%")
                    ->orWhere('bidang_keahlian', 'like', "%{$search}%")
            );
        }

        if ($request->filled('filter_status')) {
            $query->where('status_verifikasi', $request->filter_status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $btn = '<button onclick="modalAction(\'' . e(route('lomba.show', $row->lomba_id)) . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . e(route('lomba.edit', $row->lomba_id)) . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="deleteConfirmAjax(' . e($row->lomba_id) . ')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->addColumn('status_verifikasi', fn($row) => match ($row->status_verifikasi) {
                'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
                'disetujui' => '<span class="badge bg-success">Disetujui</span>',
                'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
                default => '<span class="badge bg-secondary">Tidak Diketahui</span>',
            })
            ->editColumn('bidang_keahlian', fn() => '') // sembunyikan
            ->editColumn('pembukaan_pendaftaran', fn($row) => Carbon::parse($row->pembukaan_pendaftaran)->format('d-m-Y'))
            ->editColumn('batas_pendaftaran', fn($row) => Carbon::parse($row->batas_pendaftaran)->format('d-m-Y'))
            ->rawColumns(['aksi', 'status_verifikasi'])
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
