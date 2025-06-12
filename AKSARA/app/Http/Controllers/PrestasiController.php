<?php

namespace App\Http\Controllers;

use App\Models\PrestasiModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\NotifikasiModel;
use Illuminate\Validation\Rule; // Untuk validasi unique ignore
use Illuminate\Support\Facades\Log; // Untuk logging
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\DosenModel;
use App\Models\BidangModel;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PrestasiController extends Controller
{
    public function index()
    {
        $data = PrestasiModel::all();
        $breadcrumb = (object) [
            'title' => 'Manajemen Prestasi',
            'list' => ['Prestasi']
        ];

        return view('prestasi.index', compact('data', 'breadcrumb'));
    }

    public function create()
    {
        return view('prestasi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mahasiswa_id' => 'required|integer',
            'nama_prestasi' => 'required|string|max:100',
            'kategori' => 'required|in:akademik,non-akademik,lainnya',
            'penyelenggara' => 'required|string|max:50',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'tahun' => 'required|digits:4|integer|min:2000|max:' . date('Y'),
            'file_bukti' => 'nullable|string|max:512',
            'status_verifikasi' => 'required|in:pending,disetujui,ditolak',
            'catatan_verifikasi' => 'nullable|string'
        ]);

        PrestasiModel::create($validated);

        return redirect()->route('prestasi.index')->with('success', 'Prestasi berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = PrestasiModel::findOrFail($id);
        return view('prestasi.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'mahasiswa_id' => 'required|integer',
            'nama_prestasi' => 'required|string|max:100',
            'kategori' => 'required|in:akademik,non-akademik,lainnya',
            'penyelenggara' => 'required|string|max:50',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'tahun' => 'required|digits:4|integer|min:2000|max:' . date('Y'),
            'file_bukti' => 'nullable|string|max:512',
            'status_verifikasi' => 'required|in:pending,disetujui,ditolak',
            'catatan_verifikasi' => 'nullable|string'
        ]);

        $data = PrestasiModel::findOrFail($id);
        $data->update($validated);

        return redirect()->route('prestasi.index')->with('success', 'Prestasi berhasil diperbarui');
    }

    public function destroy($id)
    {
        PrestasiModel::destroy($id);
        return redirect()->route('prestasi.index')->with('success', 'Prestasi berhasil dihapus');
    }

    public function verifikasi($id)
    {
        $data = PrestasiModel::findOrFail($id);
        return view('prestasi.verifikasi', compact('data'));
    }

    public function prosesVerifikasi(Request $request, $id)
    {
        $validated = $request->validate([
            'status_verifikasi' => 'required|in:pending,disetujui,ditolak',
            'catatan_verifikasi' => 'nullable|string'
        ]);

        $data = PrestasiModel::findOrFail($id);
        $data->update($validated);

        return redirect()->route('prestasi.index')->with('success', 'Verifikasi berhasil diperbarui');
    }

    // ==========================================================================================
    // Metode untuk Mahasiswa
    // ==========================================================================================

    public function indexMahasiswa()
    {

        $dosenList = DosenModel::all();
        // View ini akan berisi tabel yang diisi oleh DataTables via AJAX call ke listMahasiswa()
        $breadcrumb = (object) [
            'title' => 'Histori Prestasi Saya',
            'list' => ['Histori Prestasi']
        ];

        $activeMenu = 'dashboard';
        return view('prestasi.mahasiswa.index', compact('breadcrumb', 'activeMenu', 'dosenList'));
    }

    public function listMahasiswa(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            if (!$user || !$user->mahasiswa) { // Pastikan user adalah mahasiswa dan memiliki profil mahasiswa
                return response()->json(['error' => 'Akses ditolak atau profil mahasiswa tidak ditemukan.'], 403);
            }
            $mahasiswa_id = $user->mahasiswa->mahasiswa_id; // Ambil mahasiswa_id dari user yang login

            $data = PrestasiModel::where('mahasiswa_id', $mahasiswa_id)
                ->with(['dosenPembimbing.user']) // Eager load relasi dosen dan user terkait dosen
                ->orderBy('tahun', 'desc');

            // Filter data user berdasarkan status
            if (!empty($request->status_verifikasi)) {
                $data->where('status_verifikasi', $request->status_verifikasi);
            }
            // Filter data user berdasarkan tingkat
            if (!empty($request->tingkat)) {
                $data->where('tingkat', $request->tingkat);
            }
            // Filter data user berdasarkan kategori
            if (!empty($request->kategori)) {
                $data->where('kategori', $request->kategori);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('bidang_nama', fn($row) => $row->bidang->bidang_nama ?? '-')
                ->addColumn('dosen_pembimbing', function ($row) {
                    return $row->dosenPembimbing->user->nama ?? ($row->dosenPembimbing->nama ?? '-');
                })
                ->editColumn('kategori', function ($row) {
                    return ucfirst($row->kategori);
                })
                ->editColumn('tingkat', function ($row) {
                    return ucfirst($row->tingkat);
                })
                ->editColumn('status_verifikasi', fn($row) => $row->status_verifikasi_badge)
                // ->addColumn('status_verifikasi_badge', function ($row) { // Ganti nama kolom dari 'status_verifikasi'
                //     $badgeClass = 'bg-secondary';
                //     $label = ucfirst($row->status_verifikasi);

                //     switch (strtolower($row->status_verifikasi)) {
                //         case 'disetujui':
                //             $badgeClass = 'bg-success';
                //             $label = 'Terverifikasi';
                //             break;
                //         case 'pending':
                //             $badgeClass = 'bg-warning';
                //             $label = 'Menunggu';
                //             break;
                //         case 'ditolak':
                //             $badgeClass = 'bg-danger';
                //             $label = 'Ditolak';
                //             break;
                //     }

                //     return '<span class="badge ' . $badgeClass . '">' . $label . '</span>';
                // })
                ->addColumn('aksi', function ($row) {
                    $btnDetail = '<button type="button" class="btn btn-outline-info btn-sm me-1" onclick="modalAction(\'' . route('prestasi.mahasiswa.show_ajax', $row->prestasi_id) . '\', \'Detail Prestasi\')"><i class="fas fa-eye"></i></button>';
                    $btnEdit = '';
                    $btnDelete = '';

                    if (in_array($row->status_verifikasi, ['pending', 'ditolak'])) {
                        $btnEdit = '<button type="button" class="btn btn-outline-warning btn-sm me-1" onclick="modalAction(\'' . route('prestasi.mahasiswa.edit_ajax', $row->prestasi_id) . '\', \'Edit Prestasi\')"><i class="fas fa-edit"></i></button>';
                    }

                    return '<div class="btn-group">' . $btnDetail . $btnEdit . $btnDelete . '</div>';
                })
                ->rawColumns(['status_verifikasi', 'aksi']) // Tambahkan 'aksi' ke rawColumns
                ->make(true);
        }
        return abort(403, "Akses ditolak.");
    }


    public function createFormAjaxMahasiswa()
    {
        $dosens = DosenModel::with('user')->whereHas('user', function ($q) {
            $q->where('status', 'aktif');
        })->get()->map(function ($dosen) {
            return (object) [
                'id' => $dosen->dosen_id,
                'nama' => $dosen->user->nama ?? 'Nama Dosen Tidak Ada'
            ];
        })->sortBy('nama');
        $bidangs = BidangModel::select('bidang_id', 'bidang_nama')->get()->map(function ($bidang) {
            return (object) [
                'id' => $bidang->bidang_id,
                'nama' => $bidang->bidang_nama ?? 'Nama Bidang Tidak Ada'
            ];
        })->sortBy('nama');
        return view('prestasi.mahasiswa.create_ajax', compact('bidangs', 'dosens'));
    }
    public function storeAjaxMahasiswa(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->mahasiswa) {
            return response()->json(['status' => false, 'message' => 'Aksi tidak diizinkan. Profil mahasiswa tidak ditemukan.'], 403);
        }
        $mahasiswa_id = $user->mahasiswa->mahasiswa_id;

        $validator = Validator::make($request->all(), [
            'nama_prestasi' => 'required|string|max:100',
            'kategori' => ['required', Rule::in(['akademik', 'non-akademik'])],
            'penyelenggara' => 'required|string|max:50',
            'tingkat' => ['required', Rule::in(['kota', 'provinsi', 'nasional', 'internasional'])],
            'tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 1),
            'dosen_id' => 'nullable|integer|exists:dosen,dosen_id',
            'bidang_id' => 'required|integer|exists:bidang,bidang_id',
            'file_bukti' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $filePath = null;
        if ($request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $safeNamaPrestasi = substr(preg_replace('/[^A-Za-z0-9\-]/', '_', $request->nama_prestasi), 0, 50);
            $fileName = $user->mahasiswa->nim . '_' . $safeNamaPrestasi . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_prestasi/' . $user->mahasiswa->nim, $fileName, 'public');
        }

        try {
            PrestasiModel::create([
                'mahasiswa_id' => $user->mahasiswa->mahasiswa_id,
                'dosen_id' => $request->dosen_id,
                'bidang_id' => $request->bidang_id,
                'nama_prestasi' => $request->nama_prestasi,
                'kategori' => $request->kategori,
                'penyelenggara' => $request->penyelenggara,
                'tingkat' => $request->tingkat,
                'tahun' => $request->tahun,
                'file_bukti' => $filePath,
                'status_verifikasi' => 'pending', // Default status
                'catatan_verifikasi' => null,
            ]);
            return response()->json(['status' => true, 'message' => 'Prestasi berhasil ditambahkan dan sedang menunggu verifikasi.']);
        } catch (\Exception $e) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            Log::error('Error simpan prestasi (AJAX Mahasiswa): ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json(['status' => false, 'message' => 'Gagal menyimpan prestasi. Terjadi kesalahan server.'], 500);
        }
    }

    public function showAjaxMahasiswa($id)
    {
        $user = Auth::user();
        if (!$user || !$user->mahasiswa) {
            return response()->json(['message' => 'Akses ditolak atau profil mahasiswa tidak ditemukan.'], 403);
        }

        $prestasi = PrestasiModel::with(['dosenPembimbing.user', 'mahasiswa.user']) // Eager load relasi
            ->where('mahasiswa_id', $user->mahasiswa->mahasiswa_id) // Pastikan milik mahasiswa ybs
            ->findOrFail($id);

        return view('prestasi.mahasiswa.show_ajax', compact('prestasi'));
    }

    public function editAjaxMahasiswa($id)
    {
        $user = Auth::user();
        if (!$user || !$user->mahasiswa) {
            return response()->json(['message' => 'Akses ditolak atau profil mahasiswa tidak ditemukan.'], 403);
        }

        $prestasi = PrestasiModel::where('mahasiswa_id', $user->mahasiswa->mahasiswa_id)
            ->findOrFail($id);

        // Hanya boleh edit jika status 'pending' atau 'ditolak'
        if (!in_array($prestasi->status_verifikasi, ['pending', 'ditolak'])) {
            return response()->json(['message' => 'Prestasi ini tidak dapat diedit karena sudah diverifikasi.'], 403);
        }

        $dosens = DosenModel::with('user')->whereHas('user', function ($q) {
            $q->where('status', 'aktif');
        })->get()->map(function ($dosen) {
            return (object) [
                'id' => $dosen->dosen_id,
                'nama' => $dosen->user->nama ?? 'Nama Dosen Tidak Ada'
            ];
        })->sortBy('nama');
        $bidangs = BidangModel::select('bidang_id', 'bidang_nama')->get()->map(function ($bidang) {
            return (object) [
                'id' => $bidang->bidang_id,
                'nama' => $bidang->bidang_nama ?? 'Nama Bidang Tidak Ada'
            ];
        })->sortBy('nama');
        // Kita bisa menggunakan view create_ajax yang sama dengan mengirimkan data $prestasi
        return view('prestasi.mahasiswa.create_ajax', compact('bidangs', 'prestasi', 'dosens'));
    }

    /**
     * Update prestasi yang diinput oleh mahasiswa via AJAX.
     */
    public function updateAjaxMahasiswa(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->mahasiswa) {
            return response()->json(['status' => false, 'message' => 'Aksi tidak diizinkan. Profil mahasiswa tidak ditemukan.'], 403);
        }

        $prestasi = PrestasiModel::where('mahasiswa_id', $user->mahasiswa->mahasiswa_id)->findOrFail($id);

        // Hanya boleh update jika status 'pending' atau 'ditolak'
        if (!in_array($prestasi->status_verifikasi, ['pending', 'ditolak'])) {
            return response()->json(['status' => false, 'message' => 'Prestasi ini tidak dapat diupdate.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama_prestasi' => 'required|string|max:100',
            'kategori' => ['required', Rule::in(['akademik', 'non-akademik'])],
            'penyelenggara' => 'required|string|max:50',
            'tingkat' => ['required', Rule::in(['kota', 'provinsi', 'nasional', 'internasional'])],
            'tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 1),
            'dosen_id' => 'nullable|integer|exists:dosen,dosen_id',
            'bidang_id' => 'nullable|integer|exists:bidang,bidang_id',
            'file_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Opsional saat update
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $dataToUpdate = $request->only(['nama_prestasi', 'kategori', 'penyelenggara', 'tingkat', 'tahun', 'dosen_id', 'bidang_id']);

        if ($request->hasFile('file_bukti')) {
            // Hapus file lama jika ada
            if ($prestasi->file_bukti && Storage::disk('public')->exists($prestasi->file_bukti)) {
                Storage::disk('public')->delete($prestasi->file_bukti);
            }
            $file = $request->file('file_bukti');
            $safeNamaPrestasi = substr(preg_replace('/[^A-Za-z0-9\-]/', '_', $request->nama_prestasi), 0, 50);
            $fileName = $user->mahasiswa->nim . '_' . $safeNamaPrestasi . '_' . time() . '.' . $file->getClientOriginalExtension();
            $dataToUpdate['file_bukti'] = $file->storeAs('bukti_prestasi/' . $user->mahasiswa->nim, $fileName, 'public');
        }

        // Jika diedit setelah ditolak, kembalikan status ke pending
        $dataToUpdate['status_verifikasi'] = 'pending';
        $dataToUpdate['catatan_verifikasi'] = null; // Hapus catatan lama

        try {
            $prestasi->update($dataToUpdate);
            return response()->json(['status' => true, 'message' => 'Prestasi berhasil diperbarui dan sedang menunggu verifikasi ulang.']);
        } catch (\Exception $e) {
            Log::error('Error update prestasi (AJAX Mahasiswa): ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal memperbarui prestasi. Terjadi kesalahan server.'], 500);
        }
    }



    // =========================================================================
    // == METHOD UNTUK ROLE ADMIN
    // =========================================================================

    /**
     * Menampilkan halaman daftar prestasi untuk verifikasi admin.
     */
    public function indexAdmin()
    {
        $dosenList = DosenModel::all();
        $breadcrumb = (object) [
            'title' => 'Data prestasi',
            'list' => ['Prestasi Mahasiswa', 'Verifikasi Prestasi']
        ];
        $activeMenu = 'dashboard';
        return view('prestasi.admin.index', compact('breadcrumb', 'activeMenu', 'dosenList'));
    }

    public function listAdmin(Request $request)
    {
        if ($request->ajax()) {
            $data = PrestasiModel::with('mahasiswa.user', 'mahasiswa.prodi')
                ->select('prestasi.*') // Pilih semua kolom dari prestasi
                ->orderByRaw("FIELD(status_verifikasi, 'pending', 'disetujui', 'ditolak')");
            // ->orderByRaw("FIELD(status_verifikasi, 'pending', 'disetujui', 'ditolak'), created_at DESC");


            // Filter
            // if ($request->filled('search_nama')) {
            //     $searchTerm = $request->search_nama;
            //     $data->where(function ($q) use ($searchTerm) {
            //         $q->where('nama_prestasi', 'like', '%' . $searchTerm . '%')
            //             ->orWhereHas('mahasiswa.user', function ($userQuery) use ($searchTerm) {
            //                 $userQuery->where('nama', 'like', '%' . $searchTerm . '%');
            //             })
            //             ->orWhereHas('mahasiswa', function ($mhsQuery) use ($searchTerm) {
            //                 $mhsQuery->where('nim', 'like', '%' . $searchTerm . '%');
            //             });
            //     });
            // }
            // Filter data user berdasarkan status
            if (!empty($request->status_verifikasi)) {
                $data->where('status_verifikasi', $request->status_verifikasi);
            }
            // Filter data user berdasarkan tingkat
            if (!empty($request->tingkat)) {
                $data->where('tingkat', $request->tingkat);
            }
            // Filter data user berdasarkan kategori
            if (!empty($request->kategori)) {
                $data->where('kategori', $request->kategori);
            }

            // if ($request->filled('filter_status') && in_array($request->filter_status, ['pending', 'disetujui', 'ditolak'])) {
            //     $data->where('status_verifikasi', $request->filter_status);
            // }


            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_mahasiswa', function ($row) {
                    return $row->mahasiswa->user->nama ?? 'N/A';
                })
                ->addColumn('nim_mahasiswa', function ($row) {
                    return $row->mahasiswa->nim ?? 'N/A';
                })
                ->editColumn('kategori', function ($row) {
                    return ucfirst($row->kategori);
                })
                ->editColumn('tingkat', function ($row) {
                    return ucfirst($row->tingkat);
                })
                ->editColumn('status_verifikasi', fn($row) => $row->status_verifikasi_badge)
                // ->editColumn('status_verifikasi', function ($row) {
                //     if ($row->status_verifikasi == 'pending') {
                //         return '<span class="badge bg-warning text-dark">Pending</span>';
                //     } elseif ($row->status_verifikasi == 'disetujui') {
                //         return '<span class="badge bg-success">Disetujui</span>';
                //     } elseif ($row->status_verifikasi == 'ditolak') {
                //         return '<span class="badge bg-danger">Ditolak</span>';
                //     }
                //     return '<span class="badge bg-secondary">' . ucfirst($row->status_verifikasi) . '</span>';
                // })
                ->addColumn('aksi', function ($row) {
                    $verifyUrl = route('prestasi.admin.verify_form_ajax', $row->prestasi_id);
                    return '<button type="button" class="btn btn-primary btn-sm" onclick="modalAction(\'' . $verifyUrl . '\')"><i class="fas fa-clipboard-check"></i> Verifikasi</button>';
                })
                ->rawColumns(['status_verifikasi', 'aksi'])
                ->make(true);
        }
        return abort(403);
    }

    public function showVerifyFormAjaxAdmin(PrestasiModel $prestasi)
    {
        $prestasi->load('mahasiswa.user', 'mahasiswa.prodi');
        return view('prestasi.admin.verify_ajax', compact('prestasi'));
    }

    public function processVerificationAjaxAdmin(Request $request, PrestasiModel $prestasi)
    {
        $validator = Validator::make($request->all(), [
            'status_verifikasi' => ['required', Rule::in(['pending', 'disetujui', 'ditolak'])],
            'catatan_verifikasi' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $prestasi->status_verifikasi = $request->status_verifikasi;
            $prestasi->catatan_verifikasi = $request->catatan_verifikasi;
            // Laravel akan mengisi updated_at jika $timestamps = true di model
            $prestasi->save();

            // TODO: Kirim Notifikasi ke Mahasiswa

            return response()->json(['status' => true, 'message' => 'Status verifikasi prestasi berhasil diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Error verifikasi prestasi (AJAX): ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui status verifikasi. Terjadi kesalahan server.'
            ], 500);
        }
    }

    // =========================================================================
    // == METHOD UNTUK ROLE DOSEN
    // =========================================================================

    /**
     * Menampilkan halaman daftar prestasi untuk verifikasi admin.
     */
    public function indexDosen()
    {
        $dosenList = DosenModel::all();
        $breadcrumb = (object) [
            'title' => 'Data prestasi',
            'list' => ['Manajemen Mahasiswa', 'Prestasi Mahasiswa']
        ];
        $activeMenu = 'dashboard';
        return view('prestasi.dosen.index', compact('breadcrumb', 'activeMenu', 'dosenList'));
    }

    public function listDosen(Request $request)
    {
        if ($request->ajax()) {

            // $userId = Auth::id();

            // $dosen = DosenModel::where('user_id', $userId)->first();
            // if (!$dosen)
            //     return abort(403);

            $data = PrestasiModel::with('mahasiswa.user', 'mahasiswa.prodi')
                ->where('status_verifikasi', 'disetujui') // hanya data yang disetujui
                ->select('prestasi.*');

            // Filter
            if ($request->filled('search_nama')) {
                $searchTerm = $request->search_nama;
                $data->where(function ($q) use ($searchTerm) {
                    $q->where('nama_prestasi', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('mahasiswa.user', function ($userQuery) use ($searchTerm) {
                            $userQuery->where('nama', 'like', '%' . $searchTerm . '%');
                        })
                        ->orWhereHas('mahasiswa', function ($mhsQuery) use ($searchTerm) {
                            $mhsQuery->where('nim', 'like', '%' . $searchTerm . '%');
                        });
                });
            }

            if ($request->filled('tingkat')) {
                $data->where('tingkat', $request->tingkat);
            }

            if ($request->filled('kategori')) {
                $data->where('kategori', $request->kategori);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_mahasiswa', function ($row) {
                    return $row->mahasiswa->user->nama ?? 'N/A';
                })
                ->addColumn('nim_mahasiswa', function ($row) {
                    return $row->mahasiswa->nim ?? 'N/A';
                })
                ->editColumn('kategori', function ($row) {
                    return ucfirst($row->kategori);
                })
                ->editColumn('tingkat', function ($row) {
                    return ucfirst($row->tingkat);
                })
                ->addColumn('dosen', function ($row) {
                    return $row->dosen ? $row->dosen->user->nama : '-';
                })
                ->editColumn('status_verifikasi', function ($row) {
                    if ($row->status_verifikasi == 'pending') {
                        return '<span class="badge bg-warning text-dark">Pending</span>';
                    } elseif ($row->status_verifikasi == 'disetujui') {
                        return '<span class="badge bg-success">Disetujui</span>';
                    } elseif ($row->status_verifikasi == 'ditolak') {
                        return '<span class="badge bg-danger">Ditolak</span>';
                    }
                    return '<span class="badge bg-secondary">' . ucfirst($row->status_verifikasi) . '</span>';
                })
                ->addColumn('aksi', function ($row) {
                    $detailbtn = route('bimbingan.show', $row->prestasi_id);
                    return '<button type="button" class="btn btn-sm btn-outline-primary" onclick="modalAction(\'' . $detailbtn . '\')">
                <i class="fas fa-eye"></i>
            </button>';
                })
                ->rawColumns(['status_verifikasi', 'aksi'])
                ->make(true);
        }
        return abort(403);
    }

    public function indexDosBim()
    {
        $dosenList = DosenModel::all();
        $breadcrumb = (object) [
            'title' => 'Data prestasi',
            'list' => ['Manajemen Mahasiswa', 'Prestasi Mahasiswa']
        ];
        $activeMenu = 'dashboard';
        return view('prestasi.dosen.indexBim', compact('breadcrumb', 'activeMenu', 'dosenList'));
    }

    public function listDosBim(Request $request)
    {
        if ($request->ajax()) {
            if ($request->ajax()) {

                $userId = Auth::id();

                $dosen = DosenModel::where('user_id', $userId)->first();
                if (!$dosen)
                    return abort(403);

                $data = PrestasiModel::with('mahasiswa.user', 'mahasiswa.prodi')
                    ->where('dosen_id', $dosen->dosen_id)
                    ->select('prestasi.*') // Pilih semua kolom dari prestasi
                    ->orderByRaw("FIELD(status_verifikasi, 'pending', 'disetujui', 'ditolak')");
                // ->orderByRaw("FIELD(status_verifikasi, 'pending', 'disetujui', 'ditolak'), created_at DESC");



                // Filter
                if ($request->filled('search_nama')) {
                    $searchTerm = $request->search_nama;
                    $data->where(function ($q) use ($searchTerm) {
                        $q->where('nama_prestasi', 'like', '%' . $searchTerm . '%')
                            ->orWhereHas('mahasiswa.user', function ($userQuery) use ($searchTerm) {
                                $userQuery->where('nama', 'like', '%' . $searchTerm . '%');
                            })
                            ->orWhereHas('mahasiswa', function ($mhsQuery) use ($searchTerm) {
                                $mhsQuery->where('nim', 'like', '%' . $searchTerm . '%');
                            });
                    });
                }
                if ($request->filled('status_verifikasi')) {
                    $data->where('status_verifikasi', $request->status_verifikasi);
                }

                if ($request->filled('tingkat')) {
                    $data->where('tingkat', $request->tingkat);
                }

                if ($request->filled('kategori')) {
                    $data->where('kategori', $request->kategori);
                }

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('nama_mahasiswa', function ($row) {
                        return $row->mahasiswa->user->nama ?? 'N/A';
                    })
                    ->addColumn('nim_mahasiswa', function ($row) {
                        return $row->mahasiswa->nim ?? 'N/A';
                    })
                    ->editColumn('kategori', function ($row) {
                        return ucfirst($row->kategori);
                    })
                    ->editColumn('tingkat', function ($row) {
                        return ucfirst($row->tingkat);
                    })
                    ->addColumn('dosen', function ($row) {
                        return $row->dosen ? $row->dosen->user->nama : '-';
                    })
                    ->editColumn('status_verifikasi', function ($row) {
                        if ($row->status_verifikasi == 'pending') {
                            return '<span class="badge bg-warning text-dark">Pending</span>';
                        } elseif ($row->status_verifikasi == 'disetujui') {
                            return '<span class="badge bg-success">Disetujui</span>';
                        } elseif ($row->status_verifikasi == 'ditolak') {
                            return '<span class="badge bg-danger">Ditolak</span>';
                        }
                        return '<span class="badge bg-secondary">' . ucfirst($row->status_verifikasi) . '</span>';
                    })
                    ->addColumn('aksi', function ($row) {
                        $verifyUrl = route('prestasi.dosen.verify_form_ajax', $row->prestasi_id);
                        return '<button type="button" class="btn btn-primary btn-sm" onclick="modalAction(\'' . $verifyUrl . '\')"><i class="fas fa-clipboard-check"></i> Verifikasi</button>';
                    })
                    ->rawColumns(['status_verifikasi', 'aksi'])
                    ->make(true);
            }
            return abort(403);
        }
    }
    public function showDosen(PrestasiModel $prestasi)
    {
        $prestasi->load('mahasiswa.user', 'mahasiswa.prodi');
        return view('prestasi.dosen.show', compact('prestasi'));
    }

    public function showVerifyFormAjaxDosen(PrestasiModel $prestasi)
    {
        $prestasi->load('mahasiswa.user', 'mahasiswa.prodi');
        return view('prestasi.dosen.verify_ajax', compact('prestasi'));
    }

    public function processVerificationAjaxDosen(Request $request, PrestasiModel $prestasi)
    {
        $validator = Validator::make($request->all(), [
            'status_verifikasi' => ['required', Rule::in(['pending', 'disetujui', 'ditolak'])],
            'catatan_verifikasi' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // $prestasi = PrestasiModel::findOrFail($prestasi_id); // Ambil manual
            $prestasi->status_verifikasi = $request->status_verifikasi;
            $prestasi->catatan_verifikasi = $request->catatan_verifikasi;
            $prestasi->save();

            return response()->json(['status' => true, 'message' => 'Status verifikasi prestasi berhasil diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Error verifikasi prestasi (AJAX): ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui status verifikasi. Terjadi kesalahan server.'
            ], 500);
        }
    }

    public function listDosenAll(Request $request)
    {
        if ($request->ajax()) {

            $userId = Auth::id();

            $dosen = DosenModel::where('user_id', $userId)->first();
            if (!$dosen)
                return abort(403);

            $data = PrestasiModel::with('mahasiswa.user', 'mahasiswa.prodi')
                ->where('dosen_id', $dosen->dosen_id)
                ->select('prestasi.*') // Pilih semua kolom dari prestasi
                ->orderByRaw("FIELD(status_verifikasi, 'pending', 'disetujui', 'ditolak')");

            // Filter
            if ($request->filled('search_nama')) {
                $searchTerm = $request->search_nama;
                $data->where(function ($q) use ($searchTerm) {
                    $q->where('nama_prestasi', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('mahasiswa.user', function ($userQuery) use ($searchTerm) {
                            $userQuery->where('nama', 'like', '%' . $searchTerm . '%');
                        })
                        ->orWhereHas('mahasiswa', function ($mhsQuery) use ($searchTerm) {
                            $mhsQuery->where('nim', 'like', '%' . $searchTerm . '%');
                        });
                });
            }
            if ($request->filled('filter_status') && in_array($request->filter_status, ['pending', 'disetujui', 'ditolak'])) {
                $data->where('status_verifikasi', $request->filter_status);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_mahasiswa', function ($row) {
                    return $row->mahasiswa->user->nama ?? 'N/A';
                })
                ->addColumn('nim_mahasiswa', function ($row) {
                    return $row->mahasiswa->nim ?? 'N/A';
                })
                ->editColumn('kategori', function ($row) {
                    return ucfirst($row->kategori);
                })
                ->editColumn('tingkat', function ($row) {
                    return ucfirst($row->tingkat);
                })
                ->addColumn('dosen', function ($row) {
                    return $row->dosen ? $row->dosen->user->nama : '-';
                })
                ->editColumn('status_verifikasi', function ($row) {
                    if ($row->status_verifikasi == 'pending') {
                        return '<span class="badge bg-warning text-dark">Pending</span>';
                    } elseif ($row->status_verifikasi == 'disetujui') {
                        return '<span class="badge bg-success">Disetujui</span>';
                    } elseif ($row->status_verifikasi == 'ditolak') {
                        return '<span class="badge bg-danger">Ditolak</span>';
                    }
                    return '<span class="badge bg-secondary">' . ucfirst($row->status_verifikasi) . '</span>';
                })
                // Hapus addColumn aksi dan rawColumns aksi
                ->rawColumns(['status_verifikasi'])
                ->make(true);
        }
        return abort(403);
    }

    public function export_excel()
    {
        $prestasi = PrestasiModel::with(['mahasiswa.user', 'bidang', 'dosenPembimbing.user'])
            ->where('status_verifikasi', 'disetujui') // Filter hanya prestasi yang disetujui
            ->orderBy('tahun', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Mahasiswa');
        $sheet->setCellValue('C1', 'Nama Prestasi');
        $sheet->setCellValue('D1', 'Kategori');
        $sheet->setCellValue('E1', 'Bidang');
        $sheet->setCellValue('F1', 'Penyelenggara');
        $sheet->setCellValue('G1', 'Tingkat');
        $sheet->setCellValue('H1', 'Tahun');
        $sheet->setCellValue('I1', 'Dosen Pembimbing');
        $sheet->setCellValue('J1', 'Status Verifikasi');

        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($prestasi as $item) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $item->mahasiswa->user->nama ?? '-');
            $sheet->setCellValue('C' . $baris, $item->nama_prestasi);
            $sheet->setCellValue('D' . $baris, ucfirst($item->kategori));
            $sheet->setCellValue('E' . $baris, $item->bidang->bidang_nama ?? '-');
            $sheet->setCellValue('F' . $baris, $item->penyelenggara);
            $sheet->setCellValue('G' . $baris, ucfirst($item->tingkat));
            $sheet->setCellValue('H' . $baris, $item->tahun);
            // Perbaikan: gunakan relasi dosenPembimbing
            $sheet->setCellValue('I' . $baris, $item->dosenPembimbing->user->nama ?? '-');
            $sheet->setCellValue('J' . $baris, ucfirst($item->status_verifikasi));

            $baris++;
            $no++;
        }

        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Prestasi Terverifikasi');

        $writer = new Xlsx($spreadsheet); // Menggunakan new Xlsx() lebih disarankan
        $filename = 'Data_Prestasi_Terverifikasi_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $prestasi = PrestasiModel::with(['mahasiswa.user', 'bidang', 'dosenPembimbing.user'])
            ->where('status_verifikasi', 'disetujui') // Filter hanya prestasi yang disetujui
            ->orderBy('tahun', 'desc')
            ->get();

        $pdf = Pdf::loadView('prestasi.export_pdf', ['prestasi' => $prestasi]);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Data_Prestasi_Terverifikasi_' . date('Y-m-d') . '.pdf');
    }

    public function showAjaxDosen($id)
    {
        // Ambil data prestasi berdasarkan ID, beserta relasi yang dibutuhkan
        $prestasi = PrestasiModel::with([
            'mahasiswa.user',
            'mahasiswa.prodi',
            'dosen.user'
            // [PERBAIKAN] Relasi 'lomba' dihapus karena tidak terdefinisi di PrestasiModel
        ])->findOrFail($id);

        // Kirim data ke view partial yang akan dirender di dalam modal
        return view('prestasi.dosen.show_ajax', compact('prestasi'));
    }
}
