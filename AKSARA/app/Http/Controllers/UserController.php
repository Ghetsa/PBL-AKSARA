<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\AdminModel;
use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use App\Models\PeriodeModel;
use App\Models\PrestasiModel;
use App\Models\ProdiModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $data = UserModel::all();
        $prodi = ProdiModel::select('prodi_id', 'kode', 'nama')->get();
        $periode = PeriodeModel::select('periode_id', 'semester', 'tahun_akademik')->get();
        $roles = ['admin', 'dosen', 'mahasiswa'];

        $breadcrumb = (object) [
            'title' => 'Manajemen User',
            'list' => ['User']
        ];

        return view('user.index', compact('data', 'breadcrumb', 'prodi', 'periode', 'roles'));
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
                // Ubah onclick untuk menggunakan modalAction yang dimodifikasi
                $btn = '<button onclick="modalAction(\'' . e(url('/user/' . $user->user_id . '/show_ajax')) . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . e(url('/user/' . $user->user_id . '/edit_ajax')) . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                // Panggil fungsi deleteConfirmAjax yang akan memuat konten konfirmasi ke modal
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
                'bidang_keahlian' => 'required|string|max:50',
            ]);
        } elseif ($request->role == 'admin') {
            $request->validate([
                'nip' => 'required|string|max:50',
            ]);
        } elseif ($request->role == 'mahasiswa') {
            $request->validate([
                'nim' => 'required|string|max:50|unique:mahasiswa,nim',
                'prodi_id' => 'required|exists:program_studi,prodi_id',
                'periode_id' => 'required|exists:periode,periode_id',
                'bidang_minat' => 'nullable|string',
                'keahlian' => 'nullable|string',
                'sertifikasi' => 'nullable|string',
                'pengalaman' => 'nullable|string',
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
                'bidang_keahlian' => $request->bidang_keahlian
            ]);
        } elseif ($validated['role'] == 'mahasiswa') {
            MahasiswaModel::create([
                'user_id' => $user->user_id,
                'nim' => $request->nim,
                'prodi_id' => $request->prodi_id,
                'periode_id' => $request->periode_id,
                'bidang_minat' => $request->bidang_minat,
                'keahlian' => $request->keahlian,
                'sertifikasi' => $request->sertifikasi,
                'pengalaman' => $request->pengalaman,
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
                'bidang_keahlian' => 'required|string|max:50',
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
                    'bidang_keahlian' => $request->bidang_keahlian
                ]
            );
        } elseif ($validated['role'] == 'mahasiswa') {
            MahasiswaModel::updateOrCreate(
                ['user_id' => $user->user_id],
                [
                    'nim' => $request->nim,
                    'prodi_id' => $request->prodi_id,
                    'periode_id' => $request->periode_id,
                    'bidang_minat' => $request->bidang_minat,
                    'keahlian' => $request->keahlian,
                    'sertifikasi' => $request->sertifikasi,
                    'pengalaman' => $request->pengalaman,
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


    // public function create_ajax()
    // {
    //     $roles = ['admin', 'dosen', 'mahasiswa'];
    //     return view('user.create_ajax')->with('roles', $roles);
    // }

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
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            if ($request->role == 'dosen') {
                $dosenRules = [
                    'nip' => 'required|string|max:50',
                    'bidang_keahlian' => 'required|string|max:50',
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
                    'bidang_keahlian' => $request->bidang_keahlian
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


    public function edit_ajax(string $id)
    {
        $user = UserModel::find($id);
        $roles = ['admin', 'dosen', 'mahasiswa'];

        return view('user.edit_ajax', ['user' => $user, 'roles' => $roles]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id . ',user_id',
                'role' => 'required|in:admin,dosen,mahasiswa',
                'status' => 'required|in:aktif,nonaktif',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            if ($request->role == 'dosen') {
                $request->validate([
                    'nip' => 'required|string|max:50',
                    'bidang_keahlian' => 'required|string|max:50',
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
                'nama' => $request->nama,
                'email' => $request->email,
                'role' => $request->role,
                'status' => $request->status,
            ]);

            if ($request->role == 'admin') {
                AdminModel::updateOrCreate(
                    ['user_id' => $user->user_id],
                    ['nip' => $request->nip]
                );
            } elseif ($request->role == 'dosen') {
                DosenModel::updateOrCreate(
                    ['user_id' => $user->user_id],
                    [
                        'nip' => $request->nip,
                        'bidang_keahlian' => $request->bidang_keahlian
                    ]
                );
            } elseif ($request->role == 'mahasiswa') {
                MahasiswaModel::updateOrCreate(
                    ['user_id' => $user->user_id],
                    [
                        'nim' => $request->nim,
                        'prodi_id' => $request->prodi_id,
                        'periode_id' => $request->periode_id,
                    ]
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'User berhasil diupdate'
            ]);
        }

        return redirect('/');
    }

    public function confirm_ajax($id)
    {
        $user = UserModel::find($id);

        // Kita tetap kembalikan view, biarkan blade yang menangani jika $user kosong
        // Meskipun mengembalikan 404 JSON lebih 'standard' untuk API,
        // tapi jika ini loading konten modal, kembalikan view yang ada errornya juga valid.
        // Jika Anda ingin 404 JSON untuk panggilan non-AJAX langsung, tambahkan check $request->ajax()
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
}
