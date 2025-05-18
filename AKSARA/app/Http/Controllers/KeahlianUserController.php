<?php

namespace App\Http\Controllers;

use App\Models\KeahlianUserModel;
use App\Models\KeahlianModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class KeahlianUserController extends Controller
{
    // Tampilkan semua data (admin)
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Keahlian User',
            'list' => ['Dashboard', 'Keahlian User']
        ];
        $activeMenu = 'keahlian';


        return view('keahlianuser.index', compact('breadcrumb', 'activeMenu'));

        
    }

    // Data untuk datatables ajax (admin)
    public function listData(Request $request)
{
    if ($request->ajax()) {
        $data = KeahlianUserModel::with(['user', 'keahlian']) // relasi model
            ->where('user_id', Auth::id()) // tampilkan hanya data milik user login
            ->orderBy('created_at', 'desc');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nama', fn($row) => $row->user->nama ?? '-') // pastikan field 'nama' ada di tabel user
            ->addColumn('keahlian_nama', fn($row) => $row->keahlian->keahlian_nama ?? '-') // sesuai dengan nama kolom di tabel keahlian
            ->editColumn('sertifikasi', fn($row) => $row->sertifikasi ?? '-')
            ->editColumn('status_verifikasi', fn($row) => $row->status_verifikasi ?? 'pending')
            ->addColumn('aksi', function ($row) {
                $btn = '<button onclick="modalAction(\'' . route('mahasiswa.keahlianuser.edit', $row->keahlian_user_id) . '\', \'Edit Keahlian\')" class="btn btn-warning btn-sm me-1">Edit</button>';
                $btn .= '<button class="btn btn-danger btn-sm btn-delete-keahlian" data-url="' . route('mahasiswa.keahlianuser.update', $row->keahlian_user_id) . '" data-nama="' . ($row->keahlian->keahlian_nama ?? '-') . '">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    return abort(403);
}

    // Form tambah (user)
 public function create()
{
    $keahlians = KeahlianModel::all();

    // Jika request AJAX, kirim partial/modal saja
    if (request()->ajax()) {
        return view('keahlianuser.modal_create', compact('keahlians'));
    }

    // Kalau bukan AJAX, fallback (opsional)
    return abort(404);
}


public function store(Request $request)
{
    $userId = Auth::id();

    $validated = $request->validate([
        'keahlian_id' => 'required|exists:keahlian,keahlian_id',
        'sertifikasi' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // max 2MB
    ]);

    $data = [
        'keahlian_id' => $validated['keahlian_id'],
        'user_id' => $userId,
        'status_verifikasi' => 'pending',
    ];

    // Kalau ada file sertifikasi, simpan dulu filenya
    if ($request->hasFile('sertifikasi')) {
        $file = $request->file('sertifikasi');
        $path = $file->store('sertifikasi', 'public'); // simpan di storage/app/public/sertifikasi
        $data['sertifikasi'] = $path; // simpan path file ke kolom sertifikasi di DB
    }

    KeahlianUserModel::create($data);
    
    return redirect()->route('mahasiswa.keahlianuser.index')
                     ->with('success', 'Keahlian berhasil ditambahkan dan menunggu verifikasi.');
}



    // Form edit user
    public function edit($id)
{
    $data = KeahlianUserModel::where('keahlian_user_id', $id)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    $keahlians = KeahlianModel::all();

    if (Auth::user()->role != 'admin' && $data->user_id != Auth::id()) {
        abort(403);
    }

    $breadcrumb = collect([
        ['url' => route('mahasiswa.keahlianuser.index'), 'title' => 'Keahlian Saya'],
        ['url' => route('mahasiswa.keahlianuser.edit', $id), 'title' => 'Edit Keahlian'],
    ]);


    return view('keahlianuser.edit', [
        'keahlianUser' => $data,
        'keahlians' => $keahlians,
        'activeMenu' => 'keahlianuser',
        'breadcrumb' => $breadcrumb,
        
    ]);
}


public function update(Request $request, $id)
{
    $data = KeahlianUserModel::findOrFail($id);

    if (Auth::user()->role != 'admin' && $data->user_id != Auth::id()) {
        abort(403);
    }

    $validated = $request->validate([
        'keahlian_id' => 'required|exists:keahlian,keahlian_id',
        'sertifikasi' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);

    $data->keahlian_id = $validated['keahlian_id'];

    if ($request->hasFile('sertifikasi')) {
        // Hapus file lama jika ada
        if ($data->sertifikasi && Storage::disk('public')->exists($data->sertifikasi)) {
            Storage::disk('public')->delete($data->sertifikasi);
        }

        $file = $request->file('sertifikasi');
        $path = $file->store('sertifikasi', 'public');
        $data->sertifikasi = $path;
    }

    $data->save();

    return redirect()->route('mahasiswa.keahlianuser.index')
        ->with('success', 'Keahlian berhasil diperbarui.');
}

    // Form verifikasi admin
    public function verifyForm($id)
    {
        $data = KeahlianUserModel::with('user', 'keahlian')->findOrFail($id);

        // Hanya admin yang bisa akses
        if (Auth::user()->role != 'admin') {
            abort(403);
        }

        return view('keahlianuser.verify', compact('data'));
    }

    // Proses verifikasi admin
    public function processVerify(Request $request, $id)
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }

        $data = KeahlianUserModel::findOrFail($id);

        $validated = $request->validate([
            'status_verifikasi' => 'required|in:pending,disetujui,ditolak',
            'catatan_verifikasi' => 'nullable|string|max:500',
        ]);

        $data->update($validated);

        return redirect()->route('keahlianuser.index')->with('success', 'Status verifikasi berhasil diperbarui.');
    }
}
