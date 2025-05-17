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
        $users = UserModel::select('user_id', 'nama', 'email', 'role', 'status');

        // Filter data user berdasarkan role
        if (!empty($request->role)) {
            $users->where('role', $request->role);
        }

        // Filter data user berdasarkan status
        if (!empty($request->status)) {
            $users->where('status', $request->status);
        }

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('aksi', function ($user) {
                // Ubah tombol Detail agar memanggil modalAction dengan route show_ajax
                $btn = '<button onclick="modalAction(\'' . e(route('user.show_ajax', $user->user_id)) . '\')" class="btn btn-info btn-sm">Detail</button> ';

                // Ubah tombol Edit agar memanggil modalAction dengan route edit_ajax
                $btn .= '<button onclick="modalAction(\'' . e(route('user.edit_ajax', $user->user_id)) . '\')" class="btn btn-warning btn-sm">Edit</button> ';

                // Tombol Hapus tetap menggunakan deleteConfirmAjax yang sudah memanggil modalAction
                $btn .= '<button onclick="deleteConfirmAjax(' . e($user->user_id) . ')" class="btn btn-danger btn-sm">Hapus</button>';

                return $btn;
            })
            ->rawColumns(['aksi'])
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
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,dosen,mahasiswa',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($request->role == 'dosen') {
            $request->validate([
                'nip' => 'required|string|max:50',
            ]);
        } elseif ($request->role == 'admin') {
            $request->validate([
                'nip' => 'required|string|max:50',
            ]);
        } elseif ($request->role == 'mahasiswa') {
            $request->validate([
                'nim' => 'required|string|max:50|unique:mahasiswa,nim',
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
                // 'bidang_keahlian' => $request->bidang_keahlian
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
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',user_id',
            'role' => 'required|in:admin,dosen,mahasiswa',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($request->role == 'dosen') {
            $request->validate([
                'nip' => 'required|string|max:50',
            ]);
        } elseif ($request->role == 'admin') {
            $request->validate([
                'nip' => 'required|string|max:50',
            ]);
        } elseif ($request->role == 'mahasiswa') {
            $request->validate([
                'nim' => 'required|string|max:50|unique:mahasiswa,nim,' . $id . ',user_id',
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
                    // 'bidang_keahlian' => $request->bidang_keahlian
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
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'role' => 'required|in:admin,dosen,mahasiswa',
                'status' => 'required|in:aktif,nonaktif',
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
                    'nip' => 'required|string|max:50',
                ];
                $request->validate($dosenRules);
            } elseif ($request->role == 'admin') {
                $request->validate([
                    'nip' => 'required|string|max:50',
                ]);
            } elseif ($request->role == 'mahasiswa') {
                $request->validate([
                    'nim' => 'required|string|max:50|unique:mahasiswa,nim',
                    'prodi_id' => 'required|exists:program_studi,prodi_id',
                    'periode_id' => 'required|exists:periode,periode_id',
                ]);
            }

            $user = UserModel::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => bcrypt($request->password),
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
            ->find($user_id);

        if (!$user) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'User tidak ditemukan.'], 404);
            }
            return abort(404, 'User tidak ditemukan.');
        }

        $roles = ['admin', 'dosen', 'mahasiswa'];
        $keahlian = KeahlianModel::all();
        $prodi = ProdiModel::select('prodi_id', 'kode', 'nama')->get();
        $periode = PeriodeModel::select('periode_id', 'semester', 'tahun_akademik')->get();

        return view('user.edit_ajax', compact('user', 'roles', 'prodi', 'periode', 'keahlian'));
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
        // Eager load relasi untuk menampilkan data terkait role
        $user = UserModel::with([
            'admin', // Relasi ke AdminModel
            'dosen', // Relasi ke DosenModel
            'mahasiswa.prodi', // Relasi ke MahasiswaModel lalu ke ProdiModel
            'mahasiswa.periode', // Relasi ke MahasiswaModel lalu ke PeriodeModel
        ])
            ->find($user_id);

        if (!$user) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Data user tidak ditemukan.'], 404);
            }
            return abort(404, 'Data user tidak ditemukan.');
        }

        
        return view('user.show_ajax', compact('user'));
    }

}
