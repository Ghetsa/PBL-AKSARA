<?php

namespace App\Http\Controllers;

use App\Models\BidangModel;
use App\Models\KeahlianUserModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KeahlianUserController extends Controller
{
    public function index()
    {
        
        $breadcrumb = (object) [
            'title' => 'Keahlian Saya',
            'list' => ['Keahlian']
        ];
        $activeMenu = 'keahlian_user';

        return view('keahlian_user.index', compact('breadcrumb', 'activeMenu'));
    }



    // ================================================================
    // |               METHOD UNTUK MAHASISWA DAN DOSEN               |
    // ================================================================
public function list(Request $request)
{
    if ($request->ajax()) {
        $user = Auth::user();
        
        // Pastikan user login valid
        if (!$user) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $user_id = $user->user_id; // Ambil ID dari user yang login langsung

        $data = KeahlianUserModel::with('bidang')
            ->where('user_id', $user_id)
            ->orderBy('created_at', 'desc');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('bidang_nama', fn($row) => $row->bidang->bidang_nama ?? '-')
            ->editColumn('sertifikasi', fn($row) => $row->sertifikasi ?? '-')
            ->editColumn('status_verifikasi', fn($row) => $row->status_verifikasi_badge)
            ->addColumn('aksi', function ($row) {
                $editUrl = route('keahlian_user.edit', $row->keahlian_user_id);
                $deleteUrl = route('keahlian_user.destroy', $row->keahlian_user_id);

                $btn = '<button onclick="modalAction(\'' . $editUrl . '\', \'Edit Bidang\')" class="btn btn-warning btn-sm me-1">Edit</button>';
                $btn .= '<button class="btn btn-danger btn-sm btn-delete-keahlian" data-url="' . $deleteUrl . '" data-nama="' . ($row->bidang->bidang_nama ?? '-') . '">Hapus</button>';

                return $btn;
            })
            ->rawColumns(['aksi', 'status_verifikasi'])
            ->make(true);
    }

    return abort(403, 'Akses ditolak.');
}


    public function show_ajax($id)
    {
        $data = KeahlianUserModel::with(['bidang', 'user'])->findOrFail($id);
        return view('keahlian_user.show_ajax', ['keahlianUser' => $data]);
    }

    public function create()
    {
        $users = UserModel::orderBy('user_id')->get();
        $keahlianList = BidangModel::orderBy('bidang_nama')->get();

        $breadcrumb = (object) [
            'title' => 'Tambah Keahlian User',
            'list' => ['Master', 'Keahlian User', 'Tambah']
        ];
        $activeMenu = 'keahlian_user';

        return view('keahlian_user.create', compact('users', 'keahlianList', 'breadcrumb', 'activeMenu'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'bidang_id' => 'required|exists:bidang,bidang_id',
            'sertifikasi' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('sertifikasi')) {
            $filePath = $request->file('sertifikasi')->store('sertifikasi', 'public');
        }

        KeahlianUserModel::create([
            'user_id' => auth()->id(), // atau $request->user_id jika dikirim dari form
            'bidang_id' => $request->bidang_id,
            'sertifikasi' => $filePath,
            'status_verifikasi' => 'pending',
            'catatan_verifikasi' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data keahlian berhasil ditambahkan.',
        ]);
    }


    public function edit($id)
    {
        $data = KeahlianUserModel::findOrFail($id);
        $keahlian = BidangModel::orderBy('bidang_nama')->get();

        $breadcrumb = (object) [
            'title' => 'Edit Keahlian User',
            'list' => ['Master', 'Keahlian User', 'Edit']
        ];
        $activeMenu = 'keahlian_user';

        return view('keahlian_user.edit', compact('data', 'bidang', 'breadcrumb', 'activeMenu'));
    }

    public function update(Request $request, $id)
    {
        $data = KeahlianUserModel::where('keahlian_user_id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'bidang_id' => 'required|exists:bidang,bidang_id',
            'sertifikasi' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('sertifikasi')) {
            if ($data->sertifikasi && Storage::exists($data->sertifikasi)) {
                Storage::delete($data->sertifikasi);
            }
            $data->sertifikasi = $request->file('sertifikasi')->store('sertifikasi', 'public');
        }

        $data->bidang_id = $request->bidang_id;
        $data->save();

        return response()->json([
            'status' => true,
            'message' => 'Data keahlian berhasil diperbarui.',
        ]);
    }

    public function destroy($id)
    {
        $data = KeahlianUserModel::findOrFail($id);
        if ($data->sertifikasi && Storage::exists($data->sertifikasi)) {
            Storage::delete($data->sertifikasi);
        }
        $data->delete();
        return redirect()->route('keahlian_user.index')->with('success', 'Data keahlian berhasil dihapus.');
    }

    // ================================================================
    // |                    METHOD UNTUK ADMIN                    |
    // ================================================================
    public function list_admin(Request $request)
    {
        $keahlianUser = KeahlianUserModel::with(['bidang', 'user']);

        return DataTables::of($keahlianUser)
            ->addIndexColumn()
            ->addColumn('user_nama', fn($row) => $row->user->nama ?? '-')
            ->addColumn('bidang_nama', fn($row) => $row->bidang->bidang_nama ?? '-')
            ->addColumn('sertifikasi', function ($row) {
                if ($row->sertifikasi) {
                    $url = asset('storage/' . $row->sertifikasi);
                    return '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-success">Lihat</a>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('status_verifikasi', fn($row) => ucfirst($row->status_verifikasi))
            ->addColumn('aksi', function ($row) {
                $btn = '<button onclick="modalAction(\'' . route('keahlian_user.verifikasi', $row->keahlian_user_id) . '\')" class="btn btn-info btn-sm">Verifikasi</button> ';
                $btn .= '<button onclick="modalAction(\'' . route('keahlian_user.edit', $row->keahlian_user_id) . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="deleteConfirmAjax(' . $row->keahlian_user_id . ')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['sertifikasi', 'aksi'])
            ->make(true);
    }
    public function verifikasi($id)
    {
        $data = KeahlianUserModel::with(['user', 'bidang'])->findOrFail($id);

        $breadcrumb = (object) [
            'title' => 'Verifikasi Keahlian',
            'list' => ['Master', 'Keahlian User', 'Verifikasi']
        ];
        $activeMenu = 'keahlian_user';

        return view('keahlian_user.verifikasi', compact('data', 'breadcrumb', 'activeMenu'));
    }

    public function prosesVerifikasi(Request $request, $id)
    {
        $data = KeahlianUserModel::findOrFail($id);

        $request->validate([
            'status_verifikasi' => 'required|in:disetujui,ditolak',
            'catatan_verifikasi' => 'nullable|string',
        ]);

        $data->status_verifikasi = $request->status_verifikasi;
        $data->catatan_verifikasi = $request->catatan_verifikasi;
        $data->save();

        return redirect()->route('keahlian_user.index')->with('success', 'Verifikasi keahlian berhasil diproses.');
    }
}
