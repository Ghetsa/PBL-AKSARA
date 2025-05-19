<?php

namespace App\Http\Controllers;

use App\Models\KeahlianModel;
use App\Models\KeahlianUserModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class KeahlianUserController extends Controller
{
    public function index()
    {
        $data = KeahlianUserModel::with(['keahlian', 'user']);
        $breadcrumb = (object) [
            'title' => 'Data Keahlian User',
            'list' => ['Master', 'Keahlian User']
        ];
        $activeMenu = 'keahlian_user';

        return view('keahlian_user.index', compact('data', 'breadcrumb', 'activeMenu'));
    }

    public function list(Request $request)
    {
        $keahlianUser = KeahlianUserModel::with(['keahlian', 'user']);

        return DataTables::of($keahlianUser)
            ->addIndexColumn()
            ->addColumn('user_nama', fn($row) => $row->user->nama ?? '-')
            ->addColumn('keahlian_nama', fn($row) => $row->keahlian->keahlian_nama ?? '-')
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

    public function create()
    {
        $users = UserModel::orderBy('user_id')->get();
        $keahlianList = KeahlianModel::orderBy('keahlian_nama')->get();

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
            'keahlian_id' => 'required|exists:keahlian,keahlian_id',
            'sertifikasi' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('sertifikasi')) {
            $filePath = $request->file('sertifikasi')->store('sertifikasi', 'public');
        }

        KeahlianUserModel::create([
            'user_id' => auth()->id(), // atau $request->user_id jika dikirim dari form
            'keahlian_id' => $request->keahlian_id,
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
        $keahlian = KeahlianModel::orderBy('keahlian_nama')->get();

        $breadcrumb = (object) [
            'title' => 'Edit Keahlian User',
            'list' => ['Master', 'Keahlian User', 'Edit']
        ];
        $activeMenu = 'keahlian_user';

        return view('keahlian_user.edit', compact('data', 'keahlian', 'breadcrumb', 'activeMenu'));
    }

    public function update(Request $request, $id)
    {
        $data = KeahlianUserModel::where('keahlian_user_id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'keahlian_id' => 'required|exists:keahlian,keahlian_id',
            'sertifikasi' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('sertifikasi')) {
            if ($data->sertifikasi && Storage::exists($data->sertifikasi)) {
                Storage::delete($data->sertifikasi);
            }
            $data->sertifikasi = $request->file('sertifikasi')->store('sertifikasi', 'public');
        }

        $data->keahlian_id = $request->keahlian_id;
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

    public function verifikasi($id)
    {
        $data = KeahlianUserModel::with(['user', 'keahlian'])->findOrFail($id);

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
