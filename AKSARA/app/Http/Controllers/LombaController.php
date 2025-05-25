<?php

namespace App\Http\Controllers;

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
    public function rekomendasiLomba()
    {
        $user = Auth::user();

        $userMinat = $user->minat->pluck('id')->toArray(); // minat_user relasi
        $userKeahlian = $user->keahlian->pluck('id')->toArray(); // keahlian_user relasi

        $lombas = LombaModel::all();
        $dataMatrix = [];

        foreach ($lombas as $lomba) {
            $row = [];

            // 1. Bidang minat sesuai (0.25)
            $row['minat'] = in_array($lomba->bidang_id, $userMinat) ? 1 : 0;

            // 2. Bidang keahlian sesuai (0.25)
            $row['keahlian'] = in_array($lomba->bidang_id, $userKeahlian) ? 1 : 0;

            // 3. Tingkat lomba (0.15)
            $row['tingkat'] = match (strtolower($lomba->tingkat)) {
                'lokal' => 3,
                'nasional' => 4,
                'internasional' => 5,
                default => 0
            };

            // 4. Banyak hadiah (0.15)
            $row['hadiah'] = $lomba->jumlah_hadiah ?? 0;

            // 5. Selisih hari ke penutupan (0.10)
            $selisih = Carbon::now()->diffInDays(Carbon::parse($lomba->tanggal_penutupan), false);
            if ($selisih < 1)
                $skorHari = 1;
            elseif ($selisih <= 7)
                $skorHari = 1;
            elseif ($selisih <= 14)
                $skorHari = 2;
            elseif ($selisih <= 21)
                $skorHari = 3;
            elseif ($selisih <= 30)
                $skorHari = 4;
            else
                $skorHari = 5;
            $row['penutupan'] = $skorHari;

            // 6. Biaya pendaftaran (0.10) — cost
            $row['biaya'] = $lomba->biaya_pendaftaran ?? 0;

            $dataMatrix[] = [
                'lomba' => $lomba,
                'values' => $row
            ];
        }

        // Normalisasi MOORA
        $normal = $this->normalisasiMoora($dataMatrix);

        return view('rekomendasi.index', compact('normal'));
    }

    private function normalisasiMoora($dataMatrix)
    {
        $criteria = ['minat', 'keahlian', 'tingkat', 'hadiah', 'penutupan', 'biaya'];
        $weights = ['minat' => 0.25, 'keahlian' => 0.25, 'tingkat' => 0.15, 'hadiah' => 0.15, 'penutupan' => 0.10, 'biaya' => 0.10];

        // Hitung akar kuadrat dari jumlah kuadrat untuk tiap kriteria
        $divisors = [];
        foreach ($criteria as $c) {
            $divisors[$c] = sqrt(array_sum(array_map(fn($d) => pow($d['values'][$c], 2), $dataMatrix)));
        }

        $results = [];

        foreach ($dataMatrix as $item) {
            $normalized = [];
            foreach ($criteria as $c) {
                $normalized[$c] = $divisors[$c] != 0 ? $item['values'][$c] / $divisors[$c] : 0;
            }

            // Benefit: minat, keahlian, tingkat, hadiah, penutupan
            $benefit = (
                $normalized['minat'] * $weights['minat'] +
                $normalized['keahlian'] * $weights['keahlian'] +
                $normalized['tingkat'] * $weights['tingkat'] +
                $normalized['hadiah'] * $weights['hadiah'] +
                $normalized['penutupan'] * $weights['penutupan']
            );

            // Cost: biaya
            $cost = $normalized['biaya'] * $weights['biaya'];

            $score = $benefit - $cost;

            $results[] = [
                'lomba' => $item['lomba'],
                'score' => round($score, 4)
            ];
        }

        // Urutkan dari score tertinggi
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        return $results;
    }

    public function getList(Request $request)
    {
        $query = LombaModel::query();
        $mooraScores = [];

        if ($request->rekomendasi == 1) {
            $userId = auth()->id();
            $mooraScores = $this->prosesMoora($userId); // hasil berupa array [lomba_id => score]

            if (empty($mooraScores)) {
                return DataTables::of(collect())->make(true);
            }

            $query->whereIn('id', array_keys($mooraScores))
                ->orderByRaw("FIELD(id, " . implode(',', array_keys($mooraScores)) . ")");
        } else {
            if ($request->search_nama) {
                $query->where('nama_lomba', 'like', '%' . $request->search_nama . '%');
            }

            if ($request->filter_status) {
                $query->where('status_verifikasi', $request->filter_status);
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('moora_score', function ($row) use ($request, $mooraScores) {
                return $request->rekomendasi == 1 && isset($mooraScores[$row->id])
                    ? number_format($mooraScores[$row->id], 4)
                    : '-';
            })
            ->addColumn('aksi', function ($row) {
            })
            ->rawColumns(['aksi'])
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
