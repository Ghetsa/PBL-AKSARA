<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\AdminModel;
use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use App\Models\PeriodeModel;
use App\Models\PrestasiModel;
use App\Models\ProdiModel;
use App\Models\KeahlianModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // Untuk validasi unique ignore
use Illuminate\Support\Facades\Log; // Untuk logging
use Exception; // Untuk menangkap exception
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $data = UserModel::all();
        $prodi = ProdiModel::select('prodi_id', 'kode', 'nama')->get();
        $periode = PeriodeModel::select('periode_id', 'semester', 'tahun_akademik')->get();
        $roles = ['admin', 'dosen', 'mahasiswa'];
        $activeMenu = 'user';

        $breadcrumb = (object) [
            'title' => 'Manajemen User',
            'list' => ['User']
        ];

        return view('user.index', compact('data', 'breadcrumb', 'prodi', 'periode', 'roles', 'activeMenu'));
    }

    // Ambil data user dalam bentuk json untuk datatables
    public function list(Request $request)
    {
        $users = UserModel::select('user_id', 'nama', 'email', 'role', 'status', 'foto', 'no_telepon', 'alamat')
            ->with(['admin', 'dosen', 'mahasiswa']);

        if (!empty($request->role)) {
            $users->where('role', $request->role);
        }
        if (!empty($request->status)) {
            $users->where('status', $request->status);
        }

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('nama_dan_detail', function ($user) {
                $avatar = '<img src="' . $user->foto_url . '" alt="' . e($user->nama) . '" class="img-thumbnail rounded-circle me-2" width="40" height="40" style="object-fit: cover; aspect-ratio: 1/1; vertical-align: middle;">';
                $namaText = e($user->nama);
                $detailText = '';

                if ($user->role === 'admin' && $user->admin) {
                    $detailText = '<small class="d-block text-muted">NIP: ' . e($user->admin->nip ?? '-') . '</small>';
                } elseif ($user->role === 'dosen' && $user->dosen) {
                    $detailText = '<small class="d-block text-muted">NIP: ' . e($user->dosen->nip ?? '-') . '</small>';
                } elseif ($user->role === 'mahasiswa' && $user->mahasiswa) {
                    $detailText = '<small class="d-block text-muted">NIM: ' . e($user->mahasiswa->nim ?? '-') . '</small>';
                }
                return '<div class="d-flex align-items-center">' . $avatar . '<div>' . $namaText . $detailText . '</div></div>';
            })
            ->editColumn('role', function ($user) {
                $role = strtolower($user->role);
                $badgeBgClass = 'bg-light-secondary';
                $textColorClass = 'text-secondary';

                switch ($role) {
                    case 'admin':
                        $badgeBgClass = 'bg-light-danger';
                        $textColorClass = 'text-danger';
                        break;
                    case 'dosen':
                        $badgeBgClass = 'bg-light-success';
                        $textColorClass = 'text-success';
                        break;
                    case 'mahasiswa':
                        $badgeBgClass = 'bg-light-primary';
                        $textColorClass = 'text-primary';
                        break;
                }
                return '<span class="badge ' . $badgeBgClass . ' ' . $textColorClass . '">' . ucfirst($user->role) . '</span>';
            })
            ->editColumn('status', function ($user) {
                return $user->status == 'aktif' ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>';
            })
            ->addColumn('aksi', function ($user) {
                $btnDetail = '<button onclick="modalAction(\'' . route('user.show_ajax', $user->user_id) . '\')" class="btn btn-outline-info btn-sm me-1" title="Detail"><i class="fas fa-eye"></i></button>';
                $btnEdit = '<button onclick="modalAction(\'' . route('user.edit_ajax', $user->user_id) . '\')" class="btn btn-outline-warning btn-sm me-1" title="Edit"><i class="fas fa-edit"></i></button>';
                $btnDelete = '<button onclick="deleteConfirmAjax(' . $user->user_id . ')" class="btn btn-outline-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>';
                return '<div class="btn-group">' . $btnDetail . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['nama_dan_detail', 'role', 'status', 'aksi'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah User',
            'list' => ['User', 'Tambah']
        ];

        $prodi = ProdiModel::all();
        $periode = PeriodeModel::all();

        return view('user.create', compact('breadcrumb', 'prodi', 'periode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,dosen,mahasiswa',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($request->role == 'dosen') {
            $request->validate([
                'nip' => 'required|string|max:10',
            ]);
        } elseif ($request->role == 'admin') {
            $request->validate([
                'nip' => 'required|string|max:10',
            ]);
        } elseif ($request->role == 'mahasiswa') {
            $request->validate([
                'nim' => 'required|string|max:12|unique:mahasiswa,nim',
                'prodi_id' => 'required|exists:program_studi,prodi_id',
                'periode_id' => 'required|exists:periode,periode_id'
            ]);
        }

        $user = UserModel::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        if ($validated['role'] == 'admin') {
            AdminModel::create([
                'user_id' => $user->user_id,
                'nip' => $request->nip
            ]);
        } elseif ($validated['role'] == 'dosen') {
            DosenModel::create([
                'user_id' => $user->user_id,
                'nip' => $request->nip,
            ]);
        } elseif ($validated['role'] == 'mahasiswa') {
            MahasiswaModel::create([
                'user_id' => $user->user_id,
                'nim' => $request->nim,
                'prodi_id' => $request->prodi_id,
                'periode_id' => $request->periode_id
            ]);
        }

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = UserModel::findOrFail($id); // Ambil user berdasarkan ID

        $breadcrumb = (object) [
            'title' => 'Edit user',
            'list' => ['User', 'Edit']
        ];

        // Muat data relasi (admin, dosen, atau mahasiswa) ke dalam objek $data
        switch ($data->role) {
            case 'admin':
                $data->load('admin'); // Muat relasi 'admin'
                return view('user.edit_admin', compact('breadcrumb', 'data'));
            case 'dosen':
                $data->load('dosen'); // Muat relasi 'dosen'
                return view('user.edit_dosen', compact('breadcrumb', 'data'));
            case 'mahasiswa':
                $data->load('mahasiswa'); // Muat relasi 'mahasiswa'
                return view('user.edit_mahasiswa', compact('breadcrumb', 'data'));
            default:
                abort(404, 'User role not supported for editing.');
        }
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $id . ',user_id',
            'role' => 'required|in:admin,dosen,mahasiswa',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($request->role == 'dosen') {
            $request->validate([
                'nip' => 'required|string|max:10',
            ]);
        } elseif ($request->role == 'admin') {
            $request->validate([
                'nip' => 'required|string|max:10',
            ]);
        } elseif ($request->role == 'mahasiswa') {
            $request->validate([
                'nim' => 'required|string|max:12|unique:mahasiswa,nim,' . $id . ',user_id',
                'prodi_id' => 'required|exists:program_studi,prodi_id',
                'periode_id' => 'required|exists:periode,periode_id',
            ]);
        }

        $user = UserModel::findOrFail($id);
        $user->update([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        if ($validated['role'] == 'admin') {
            AdminModel::updateOrCreate(
                ['user_id' => $user->user_id],
                ['nip' => $request->nip]
            );
        } elseif ($validated['role'] == 'dosen') {
            DosenModel::updateOrCreate(
                ['user_id' => $user->user_id],
                [
                    'nip' => $request->nip,
                ]
            );
        } elseif ($validated['role'] == 'mahasiswa') {
            MahasiswaModel::updateOrCreate(
                ['user_id' => $user->user_id],
                [
                    'nim' => $request->nim,
                    'prodi_id' => $request->prodi_id,
                    'periode_id' => $request->periode_id
                ]
            );
        }

        return redirect()->route('user.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy($id)
    {
        UserModel::destroy($id);
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus');
    }

    public function create_ajax()
    {
        $roles = ['admin', 'dosen', 'mahasiswa'];
        $prodi = ProdiModel::select('prodi_id', 'kode', 'nama')->get();
        $periode = PeriodeModel::select('periode_id', 'semester', 'tahun_akademik')->get();

        return view('user.create_ajax')
            ->with('roles', $roles)
            ->with('prodi', $prodi)
            ->with('periode', $periode);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'role' => 'required|in:admin,dosen,mahasiswa',
                'status' => 'required|in:aktif,nonaktif',
                'no_telepon' => 'nullable|string|max:15|regex:/^[0-9\-\+\(\)\s]*$/', // Validasi nomor telepon sederhana
                'alamat' => 'nullable|string|max:100',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ]);
            }

            if ($request->role == 'dosen') {
                $dosenRules = [
                    'nip' => 'required|string|max:10',
                ];
                $request->validate($dosenRules);
            } elseif ($request->role == 'admin') {
                $request->validate([
                    'nip' => 'required|string|max:10',
                ]);
            } elseif ($request->role == 'mahasiswa') {
                $request->validate([
                    'nim' => 'required|string|max:10|unique:mahasiswa,nim',
                    'prodi_id' => 'required|exists:program_studi,prodi_id',
                    'periode_id' => 'required|exists:periode,periode_id',
                ]);
            }

            $user = UserModel::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'no_telepon' => $request->no_telepon,
                'alamat' => $request->alamat,
                'role' => $request->role,
                'status' => $request->status,
            ]);

            if ($request->role == 'admin') {
                AdminModel::create([
                    'user_id' => $user->user_id,
                    'nip' => $request->nip
                ]);
            } elseif ($request->role == 'dosen') {
                DosenModel::create([
                    'user_id' => $user->user_id,
                    'nip' => $request->nip,
                ]);
            } elseif ($request->role == 'mahasiswa') {
                MahasiswaModel::create([
                    'user_id' => $user->user_id,
                    'nim' => $request->nim,
                    'prodi_id' => $request->prodi_id,
                    'periode_id' => $request->periode_id,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'User berhasil ditambahkan'
            ]);
        }

        return redirect('/');
    }

    public function edit_ajax($user_id)
    {
        $user = UserModel::with(['admin', 'dosen', 'mahasiswa.prodi', 'mahasiswa.periode'])
            ->findOrFail($user_id);

        if (!$user) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'User tidak ditemukan.'], 404);
            }
            return abort(404, 'User tidak ditemukan.');
        }

        $roles = ['admin', 'dosen', 'mahasiswa'];
        $prodi = ProdiModel::select('prodi_id', 'nama')->orderBy('nama')->get();
        $periode = PeriodeModel::select('periode_id', 'semester', 'tahun_akademik')->orderBy('tahun_akademik', 'desc')->orderBy('semester', 'desc')->get();

        return view('user.edit_ajax', compact('user', 'roles', 'prodi', 'periode'));
    }

    public function update_ajax(Request $request, $user_id)
    {
        if (!($request->ajax() || $request->wantsJson())) {
            return response()->json(['status' => false, 'message' => 'Akses tidak diizinkan.'], 403);
        }

        $user = UserModel::find($user_id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User tidak ditemukan.'], 404);
        }

        $baseRules = [
            'nama' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')
            ],
            'password' => 'nullable|string|min:6', // Password opsional saat update
            'no_telepon' => 'nullable|string|max:15|regex:/^[0-9\-\+\(\)\s]*$/',
            'alamat' => 'nullable|string|max:100',
            'role' => 'required|in:admin,dosen,mahasiswa',
            'status' => 'required|in:aktif,nonaktif',
        ];

        // Aturan validasi tambahan berdasarkan role
        $roleRules = [];

        if ($request->role === 'admin') {
            $roleRules = [
                'nip' => ['required', 'string', 'max:50', Rule::unique('admin', 'nip')->ignore($user->admin->admin_id ?? null, 'admin_id')],
            ];
        } elseif ($request->role === 'dosen') {
            $roleRules = [
                'nip' => ['required', 'string', 'max:50', Rule::unique('dosen', 'nip')->ignore($user->dosen->dosen_id ?? null, 'dosen_id')],
            ];
        } elseif ($request->role === 'mahasiswa') {
            $roleRules = [
                'nim' => ['required', 'string', 'max:50', Rule::unique('mahasiswa', 'nim')->ignore($user->mahasiswa->mahasiswa_id ?? null, 'mahasiswa_id')],
                'prodi_id' => 'required|exists:program_studi,prodi_id',
                'periode_id' => 'required|exists:periode,periode_id'
            ];
        }


        $validator = Validator::make($request->all(), array_merge($baseRules, $roleRules));

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $userData = [
                'nama' => $request->nama,
                'email' => $request->email,
                'role' => $request->role,
                'status' => $request->status,
                'no_telepon' => $request->no_telepon,
                'alamat' => $request->alamat,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            if ($request->role == 'dosen') {
                DosenModel::updateOrCreate(
                    ['user_id' => $user->user_id],
                    ['nip' => $request->nip]
                );

                if ($user->admin)
                    $user->admin->delete();
                if ($user->mahasiswa)
                    $user->mahasiswa->delete();
            }
            if ($request->role == 'mahasiswa') {
                MahasiswaModel::updateOrCreate(
                    ['user_id' => $user->user_id],
                    [
                        'nim' => $request->nim,
                        'prodi_id' => $request->prodi_id,
                        'periode_id' => $request->periode_id
                    ]
                );

                // Hapus data admin dan dosen jika sebelumnya user punya
                if ($user->admin)
                    $user->admin->delete();
                if ($user->dosen)
                    $user->dosen->delete();
            }

            // DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'User berhasil diperbarui.'
            ], 200);
        } catch (Exception $e) {
            // DB::rollBack();
            Log::error("Error updating user ID {$user_id}: " . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui user. Terjadi kesalahan server.'
            ], 500);
        }
    }

    public function confirm_ajax($id)
    {
        $user = UserModel::find($id);

        return view('user.confirm_ajax', compact('user'));
    }

    // INI METODE delete_ajax ANDA, BIARKAN SEPERTI INI
    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $user = UserModel::find($id);

            if ($user) {
                try {
                    // Hapus relasi jika ada (sesuaikan dengan model Anda)
                    if ($user->admin) {
                        $user->admin->delete();
                    }
                    if ($user->dosen) {
                        $user->dosen->delete();
                    }
                    if ($user->mahasiswa) {
                        $user->mahasiswa->delete();
                    }

                    $user->delete(); // Hapus data user utama

                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Gagal menghapus data: ' . $e->getMessage()
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404); // Kembalikan 404 jika user tidak ditemukan
            }
        }

        // Jika bukan AJAX, redirect
        return redirect('/');
    }

    public function show_ajax($user_id)
    {
        $user = UserModel::with([
            'admin',
            'dosen',
            'mahasiswa' => function ($query) {
                $query->with(['prodi', 'periode']);
            }
        ])->findOrFail($user_id);
        return view('user.show_ajax', compact('user'));
    }

    // public function show_ajax($user_id)
    // {
    //     // Eager load relasi untuk menampilkan data terkait role
    //     $user = UserModel::with([
    //         'admin', // Relasi ke AdminModel
    //         'dosen', // Relasi ke DosenModel
    //         'mahasiswa.prodi', // Relasi ke MahasiswaModel lalu ke ProdiModel
    //         'mahasiswa.periode', // Relasi ke MahasiswaModel lalu ke PeriodeModel
    //     ])
    //         ->find($user_id);

    //     if (!$user) {
    //         if (request()->ajax() || request()->wantsJson()) {
    //             return response()->json(['status' => false, 'message' => 'Data user tidak ditemukan.'], 404);
    //         }
    //         return abort(404, 'Data user tidak ditemukan.');
    //     }


    //     return view('user.show_ajax', compact('user'));
    // }

    public function import()
    {
        return view('user.import');
    }

    public function import_ajax(Request $request)
    {
        // Validasi file awal
        $validator = Validator::make($request->all(), [
            'file_user_excel' => 'required|mimes:xls,xlsx|max:5120', // Max 5MB
        ], [
            'file_user_excel.required' => 'File Excel wajib diunggah.',
            'file_user_excel.mimes' => 'Format file harus .xls atau .xlsx.',
            'file_user_excel.max' => 'Ukuran file maksimal 5MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi file gagal.',
                'msgField' => $validator->errors()->toArray()
            ], 422);
        }

        $file = $request->file('file_user_excel');

        try {
            $reader = IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true); // Hanya baca data, abaikan styling untuk performa
            $spreadsheet = $reader->load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true); // Baca sebagai array asosiatif dengan header

            $importedCount = 0;
            $errorCount = 0;
            $errorsDetail = []; // Untuk menyimpan detail error per baris

            // Ambil header (baris pertama)
            $header = array_map('trim', array_map('strtolower', $rows[1]));
            unset($rows[1]); // Hapus baris header dari data

            // Ekspektasi header (sesuaikan dengan template Anda, case insensitive)
            // Ini hanya untuk referensi, validasi akan berdasarkan indeks kolom
            // $expectedHeaders = ['nama lengkap', 'email', 'password', 'role', 'nip/nim', 'no telepon', 'alamat', 'kode prodi', 'tahun angkatan'];

            DB::beginTransaction();

            foreach ($rows as $rowIndex => $row) {
                // Kolom Excel: A=Nama, B=Email, C=Password, D=Role, E=NIP/NIM,
                // F=No Telepon, G=Alamat, H=Kode Prodi, I=Tahun Angkatan
                $nama = trim($row['A'] ?? '');
                $email = trim($row['B'] ?? '');
                $password = trim($row['C'] ?? '');
                $role = strtolower(trim($row['D'] ?? ''));
                $nip_nim = trim($row['E'] ?? '');
                $no_telepon = trim($row['F'] ?? null);
                $alamat = trim($row['G'] ?? null);
                $kode_prodi = trim($row['H'] ?? null);
                $tahun_angkatan = trim($row['I'] ?? null);

                // Minimal data yang harus ada
                if (empty($nama) || empty($email) || empty($password) || empty($role)) {
                    $errorCount++;
                    $errorsDetail[] = "Baris " . $rowIndex . ": Data tidak lengkap (Nama, Email, Password, Role wajib diisi). Lewati baris ini.";
                    continue;
                }

                // Validasi Role
                if (!in_array($role, ['admin', 'dosen', 'mahasiswa'])) {
                    $errorCount++;
                    $errorsDetail[] = "Baris " . $rowIndex . ": Role '$role' tidak valid. Gunakan 'admin', 'dosen', atau 'mahasiswa'. Lewati baris ini.";
                    continue;
                }

                // Validasi NIP/NIM berdasarkan Role
                if (in_array($role, ['admin', 'dosen', 'mahasiswa']) && empty($nip_nim)) {
                    $errorCount++;
                    $errorsDetail[] = "Baris " . $rowIndex . ": NIP/NIM wajib diisi untuk role '$role'. Lewati baris ini.";
                    continue;
                }

                // Cek duplikasi Email
                if (UserModel::where('email', $email)->exists()) {
                    $errorCount++;
                    $errorsDetail[] = "Baris " . $rowIndex . ": Email '$email' sudah terdaftar. Lewati baris ini.";
                    continue;
                }

                $prodi_id = null;
                if (!empty($kode_prodi) && in_array($role, ['dosen', 'mahasiswa'])) {
                    $prodi = ProdiModel::where('kode', $kode_prodi)->first(); // Menggunakan prodi_kode dari SQL
                    if (!$prodi) {
                        $errorCount++;
                        $errorsDetail[] = "Baris " . $rowIndex . ": Kode Prodi '$kode_prodi' tidak ditemukan. Lewati baris ini.";
                        continue;
                    }
                    $prodi_id = $prodi->prodi_id;
                } elseif (in_array($role, ['dosen', 'mahasiswa']) && empty($kode_prodi)) {
                    $errorCount++;
                    $errorsDetail[] = "Baris " . $rowIndex . ": Kode Prodi wajib diisi untuk role '$role'. Lewati baris ini.";
                    continue;
                }


                $periode_id = null;
                if ($role === 'mahasiswa' && !empty($tahun_angkatan)) {
                    if (!preg_match('/^\d{4}$/', $tahun_angkatan)) {
                        $errorCount++;
                        $errorsDetail[] = "Baris " . $rowIndex . ": Format Tahun Angkatan '$tahun_angkatan' tidak valid (gunakan YYYY). Lewati baris ini.";
                        continue;
                    }
                    // Cari periode Ganjil untuk tahun angkatan tersebut
                    $periode = PeriodeModel::where('semester', 'Ganjil')
                        ->where('tahun_akademik', $tahun_angkatan . '/' . ($tahun_angkatan + 1))
                        ->first();
                    if (!$periode) {
                        // Jika tidak ada, coba buat periode baru atau tandai error
                        // Untuk impor, lebih baik tandai error jika periode tidak ada
                        $errorCount++;
                        $errorsDetail[] = "Baris " . $rowIndex . ": Periode Ganjil untuk tahun ajaran " . $tahun_angkatan . "/" . ($tahun_angkatan + 1) . " tidak ditemukan. Pastikan periode sudah ada. Lewati baris ini.";
                        continue;
                    }
                    $periode_id = $periode->periode_id;
                } elseif ($role === 'mahasiswa' && empty($tahun_angkatan)) {
                    $errorCount++;
                    $errorsDetail[] = "Baris " . $rowIndex . ": Tahun Angkatan wajib diisi untuk role 'mahasiswa'. Lewati baris ini.";
                    continue;
                }


                // Buat User
                $newUser = UserModel::create([
                    'nama' => $nama,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => $role,
                    'no_telepon' => $no_telepon,
                    'alamat' => $alamat,
                    'status' => 'aktif', // Default status
                    // 'level_id' => $level_id, // Jika Anda punya logika untuk ini
                ]);

                // Buat data role-specific
                if ($role === 'admin') {
                    AdminModel::create([
                        'user_id' => $newUser->user_id,
                        'nip' => $nip_nim,
                    ]);
                } elseif ($role === 'dosen') {
                    DosenModel::create([
                        'user_id' => $newUser->user_id,
                        'nip' => $nip_nim,
                        'prodi_id' => $prodi_id,
                        // 'keahlian_id' => null, // Jika ada default atau diisi nanti
                    ]);
                } elseif ($role === 'mahasiswa') {
                    MahasiswaModel::create([
                        'user_id' => $newUser->user_id,
                        'nim' => $nip_nim,
                        'prodi_id' => $prodi_id,
                        'periode_id' => $periode_id, // Periode masuk/angkatan
                        // 'mahasiswa_angkatan' => $tahun_angkatan,
                    ]);
                }
                $importedCount++;
            }

            if ($errorCount > 0) {
                DB::rollBack();
                $message = "Impor selesai dengan beberapa kesalahan. ";
                $message .= $importedCount . " data berhasil diimpor, " . $errorCount . " data gagal.";
                return response()->json([
                    'status' => false, // Set false jika ada error agar user tahu tidak semua berhasil
                    'message' => $message,
                    'errors_detail' => $errorsDetail
                ], 400); // atau 200 dengan status false
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => $importedCount . " data user berhasil diimpor."
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error during Excel import for users: " . $e->getMessage() . "\nStack Trace:\n" . $e->getTraceAsString());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan server saat impor: ' . $e->getMessage(),
                'errors_detail' => ['Kesalahan sistem: ' . $e->getMessage()]
            ], 500);
        }
    }

    public function export_excel()
    {
        // ambil data user yang akan di export
        // Pastikan untuk eager load relasi yang dibutuhkan
        $users = UserModel::select('user_id', 'nama', 'email', 'role', 'no_telepon', 'alamat') // Tambahkan 'username' jika belum ada
            ->orderBy('nama')
            ->with(['admin', 'dosen', 'mahasiswa']) // Eager load relasi
            ->get();

        // Load library excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

        // Set header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Username'); // Ditambahkan/disesuaikan
        $sheet->setCellValue('C1', 'NIM/NIP');   // Kolom baru
        $sheet->setCellValue('D1', 'Email');
        $sheet->setCellValue('E1', 'Role');
        $sheet->setCellValue('F1', 'No Telepon');
        $sheet->setCellValue('G1', 'Alamat');

        // Set style bold untuk header
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $no = 1; // nomor data dimulai dari 1
        $baris = 2; // baris data dimulai dari baris ke-2
        foreach ($users as $user) { // Menggunakan $user sebagai iterator
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $user->nama);

            // Logika untuk NIM/NIP
            $nim_nip = 'N/A';
            if (isset($user->role)) {
                $roleLower = strtolower($user->role);
                if ($roleLower === 'admin' && $user->admin) {
                    $nim_nip = $user->admin->nip;
                } elseif ($roleLower === 'dosen' && $user->dosen) {
                    $nim_nip = $user->dosen->nip;
                } elseif ($roleLower === 'mahasiswa' && $user->mahasiswa) {
                    $nim_nip = $user->mahasiswa->nim;
                }
            }
            $sheet->setCellValue('C' . $baris, $nim_nip);

            $sheet->setCellValue('D' . $baris, $user->email);
            $sheet->setCellValue('E' . $baris, $user->role);
            $sheet->setCellValue('F' . $baris, $user->no_telepon);
            $sheet->setCellValue('G' . $baris, $user->alamat);

            $baris++;
            $no++;
        }

        // Set auto size untuk semua kolom yang digunakan
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data User'); // set title sheet

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data User ' . date('Y-m-d H_i_s') . '.xlsx'; // Menggunakan H_i_s agar nama file lebih unik

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
        $user = UserModel::select('user_id', 'nama', 'email', 'role', 'no_telepon', 'alamat')
            ->orderBy('nama')
            ->with(['admin', 'dosen', 'mahasiswa'])
            ->get();

        // use Barryvdh\DomPDF\Facade\Pdf PDF
        $pdf = Pdf::loadView('user.export_pdf', ['users' => $user]);
        $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi 
        $pdf->setOption('isRemoteEnabled', true); // set true jika ada gambar dari URL
        $pdf->render();

        return $pdf->stream('Data User ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
