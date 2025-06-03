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
                $avatar = '<img src="' . $user->foto_url . '" alt="' . e($user->nama) . '" class="img-thumbnail rounded-circle me-2" width="40" height="40" style="object-fit: cover; vertical-align: middle;">';
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
                $btnDetail = '<button onclick="modalAction(\'' . route('user.show_ajax', $user->user_id) . '\')" class="btn btn-info btn-sm me-1" title="Detail"><i class="fas fa-eye"></i></button>';
                $btnEdit = '<button onclick="modalAction(\'' . route('user.edit_ajax', $user->user_id) . '\')" class="btn btn-warning btn-sm me-1" title="Edit"><i class="fas fa-edit"></i></button>';
                $btnDelete = '<button onclick="deleteConfirmAjax(' . $user->user_id . ')" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>';
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
                'no_telepon' => 'nullable|string|max:15|regex:/^[0-9\-\+\(\)\s]*$/', // Validasi nomor telepon sederhana
                'alamat' => 'nullable|string|max:255',
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

    // public function edit_ajax($user_id)
    // {
    //     $user = UserModel::with(['admin', 'dosen', 'mahasiswa.prodi', 'mahasiswa.periode'])
    //         ->find($user_id);

    //     if (!$user) {
    //         if (request()->ajax() || request()->wantsJson()) {
    //             return response()->json(['status' => false, 'message' => 'User tidak ditemukan.'], 404);
    //         }
    //         return abort(404, 'User tidak ditemukan.');
    //     }

    //     $roles = ['admin', 'dosen', 'mahasiswa'];
    //     $keahlian = KeahlianModel::all();
    //     $prodi = ProdiModel::select('prodi_id', 'kode', 'nama')->get();
    //     $periode = PeriodeModel::select('periode_id', 'semester', 'tahun_akademik')->get();

    //     return view('user.edit_ajax', compact('user', 'roles', 'prodi', 'periode', 'keahlian'));
    // }

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
            'alamat' => 'nullable|string|max:255',
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

    public function export_excel()
    {
        // ambil data user yang akan di export
        $user = UserModel::select('user_id', 'role', 'username', 'nama', 'password') // Ditambahkan user_id untuk konsistensi jika diperlukan
            ->orderBy('nama') // Ditambahkan order by nama
            ->with('level')
            ->get();

        // Load library excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Username');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'Password'); // Sesuai contoh Anda, pertimbangkan keamanan
        $sheet->setCellValue('E1', 'Role');

        $sheet->getStyle('A1:E1')->getFont()->setBold(true); // bold header

        $no = 1; // nomor data dimulai dari 1
        $baris = 2; // baris data dimulai dari baris ke-2
        foreach ($user as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->username);
            $sheet->setCellValue('C' . $baris, $value->nama);
            $sheet->setCellValue('D' . $baris, $value->password); // Mengekspor password (seperti contoh Anda)
            $sheet->setCellValue('E' . $baris, $value->level ? $value->level->level_nama : 'N/A'); // ambil nama level
            $baris++;
            $no++;
        }

        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true); // set auto size untuk kolom
        }

        $sheet->setTitle('Data User'); // set title sheet

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data User ' . date('Y-m-d H_i_s') . '.xlsx'; // Menggunakan H_i_s untuk nama file unik

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
        $user = UserModel::select('user_id', 'level_id', 'username', 'nama') // Password tidak diambil untuk PDF
            ->orderBy('level_id')
            ->orderBy('nama')
            ->with('level')
            ->get();

        // view yang akan digenerate adalah user.export_pdf
        // variabel $user akan dikirimkan ke view tersebut
        $pdf = Pdf::loadView('user.export_pdf', ['user' => $user]);
        $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
        $pdf->setOption('isRemoteEnabled', true); // set true jika ada gambar dari URL
        // $pdf->render(); // render() seringkali otomatis dipanggil oleh stream()

        return $pdf->stream('Data User ' . date('Y-m-d H_i_s') . '.pdf'); // Menggunakan H_i_s untuk nama file unik
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
}
