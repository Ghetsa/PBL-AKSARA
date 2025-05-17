<?php

namespace App\Http\Controllers;

use App\Models\KeahlianUserModel;
use App\Models\KeahlianModel;
use App\Models\UserModel;
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
            $data = KeahlianUserModel::with('user', 'keahlian')
                ->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_user', fn($row) => $row->user->nama ?? '-')
                ->addColumn('keahlian', fn($row) => $row->keahlian->nama ?? '-')
                ->editColumn('sertifikasi', fn($row) => $row->sertifikasi ?? '-')
                ->editColumn('status_verifikasi', fn($row) => $row->status_verifikasi ?? 'pending')
                ->addColumn('aksi', function ($row) {
                    $btn = '<a href="' . route('keahlianuser.edit', $row->keahlian_user_id) . '" class="btn btn-warning btn-sm me-1">Edit</a>';
                    $btn .= '<a href="' . route('keahlianuser.verify_form', $row->keahlian_user_id) . '" class="btn btn-info btn-sm">Verifikasi</a>';
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
        return view('keahlianuser.create', compact('keahlians'));
    }

    // Simpan data (user)
    public function store(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'keahlian_id' => 'required|exists:keahlian,keahlian_id',
            'sertifikasi' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = $userId;
        $validated['status_verifikasi'] = 'pending';

        KeahlianUserModel::create($validated);

        return redirect()->route('keahlianuser.index')->with('success', 'Keahlian berhasil ditambahkan dan menunggu verifikasi.');
    }

    // Form edit user
    public function edit($id)
    {
        $data = KeahlianUserModel::findOrFail($id);
        $keahlians = KeahlianModel::all();

        // Pastikan user yang edit adalah pemilik, kecuali admin
        if (Auth::user()->role != 'admin' && $data->user_id != Auth::id()) {
            abort(403);
        }

        return view('keahlianuser.edit', compact('data', 'keahlians'));
    }

    // Update user
    public function update(Request $request, $id)
    {
        $data = KeahlianUserModel::findOrFail($id);

        if (Auth::user()->role != 'admin' && $data->user_id != Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'keahlian_id' => 'required|exists:keahlian,keahlian_id',
            'sertifikasi' => 'nullable|string|max:255',
        ]);

        $data->update($validated);

        // Saat user update, status verifikasi jadi pending lagi
        if (Auth::user()->role != 'admin') {
            $data->status_verifikasi = 'pending';
            $data->save();
        }

        return redirect()->route('keahlianuser.index')->with('success', 'Keahlian berhasil diperbarui.');
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
