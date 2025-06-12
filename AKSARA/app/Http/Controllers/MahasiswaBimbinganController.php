<?php

namespace App\Http\Controllers;

use App\Models\MahasiswaBimbinganModel;
use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MahasiswaBimbinganController extends Controller
{
    /**
     * Menampilkan halaman daftar bimbingan.
     */
    public function index()
    {
        // Menyiapkan data untuk breadcrumb dan menu aktif
        $breadcrumb = (object) [
            'title' => 'Manajemen Bimbingan',
            'list' => ['Home', 'Manajemen Bimbingan']
        ];
        $activeMenu = 'bimbingan';

        // Mengambil data Dosen dan Mahasiswa untuk filter atau form
        $dosens = DosenModel::all();
        $mahasiswas = MahasiswaModel::all();

        return view('admin.bimbingan.index', [
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'dosens' => $dosens,
            'mahasiswas' => $mahasiswas
        ]);
    }

    /**
     * Menyediakan data bimbingan untuk DataTables.
     */
    public function list(Request $request)
    {
        // Mengambil data dengan relasi untuk ditampilkan
        $bimbingans = MahasiswaBimbinganModel::with(['dosen.user', 'mahasiswa.user', 'mahasiswa.prodi']);

        // Filter data jika ada request
        if ($request->dosen_id) {
            $bimbingans->where('dosen_id', $request->dosen_id);
        }
        if ($request->mahasiswa_id) {
            $bimbingans->where('mahasiswa_id', $request->mahasiswa_id);
        }

        return DataTables::of($bimbingans)
            ->addIndexColumn()
            ->addColumn('dosen_nama', function ($bimbingan) {
                return $bimbingan->dosen->user->nama ?? 'N/A';
            })
            ->addColumn('mahasiswa_nama', function ($bimbingan) {
                return $bimbingan->mahasiswa->user->nama ?? 'N/A';
            })
            ->addColumn('mahasiswa_nim', function ($bimbingan) {
                return $bimbingan->mahasiswa->nim ?? 'N/A';
            })
            ->addColumn('status_aktif', function ($bimbingan) {
                return $bimbingan->aktif ? 
                    '<span class="badge badge-success">Aktif</span>' : 
                    '<span class="badge badge-secondary">Tidak Aktif</span>';
            })
            ->addColumn('aksi', function ($bimbingan) {
                $btn = '<a href="' . url('/admin/bimbingan/' . $bimbingan->bimbingan_id . '/edit') . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' . url('/admin/bimbingan/' . $bimbingan->bimbingan_id) . '">'
                    . csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');"><i class="fas fa-trash-alt"></i> Hapus</button></form>';
                return $btn;
            })
            ->rawColumns(['status_aktif', 'aksi'])
            ->make(true);
    }

    /**
     * Menampilkan formulir untuk membuat bimbingan baru.
     */
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Data Bimbingan',
            'list' => ['Home', 'Manajemen Bimbingan', 'Tambah']
        ];
        $activeMenu = 'bimbingan';

        $dosens = DosenModel::with('user')->get();
        $mahasiswas = MahasiswaModel::with('user')->get();

        return view('admin.bimbingan.create', [
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'dosens' => $dosens,
            'mahasiswas' => $mahasiswas
        ]);
    }

    /**
     * Menyimpan data bimbingan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dosen_id' => 'required|integer|exists:dosen,dosen_id',
            'mahasiswa_id' => 'required|integer|exists:mahasiswa,mahasiswa_id|unique:mahasiswa_bimbingan,mahasiswa_id,NULL,bimbingan_id,aktif,1',
            'aktif' => 'required|boolean',
        ]);

        MahasiswaBimbinganModel::create($request->all());

        return redirect()->route('bimbingan.index')->with('success', 'Data bimbingan berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir untuk mengedit data bimbingan.
     */
    public function edit(string $id)
    {
        $bimbingan = MahasiswaBimbinganModel::findOrFail($id);
        $dosens = DosenModel::with('user')->get();
        $mahasiswas = MahasiswaModel::with('user')->get();
        
        $breadcrumb = (object) [
            'title' => 'Edit Data Bimbingan',
            'list' => ['Home', 'Manajemen Bimbingan', 'Edit']
        ];
        $activeMenu = 'bimbingan';

        return view('admin.bimbingan.edit', [
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'bimbingan' => $bimbingan,
            'dosens' => $dosens,
            'mahasiswas' => $mahasiswas
        ]);
    }

    /**
     * Memperbarui data bimbingan di database.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'dosen_id' => 'required|integer|exists:dosen,dosen_id',
            'mahasiswa_id' => 'required|integer|exists:mahasiswa,mahasiswa_id|unique:mahasiswa_bimbingan,mahasiswa_id,' . $id . ',bimbingan_id,aktif,1',
            'aktif' => 'required|boolean',
        ]);

        $bimbingan = MahasiswaBimbinganModel::findOrFail($id);
        $bimbingan->update($request->all());

        return redirect()->route('bimbingan.index')->with('success', 'Data bimbingan berhasil diperbarui.');
    }

    /**
     * Menghapus data bimbingan dari database.
     */
    public function destroy(string $id)
    {
        try {
            MahasiswaBimbinganModel::destroy($id);
            return redirect()->route('bimbingan.index')->with('success', 'Data bimbingan berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('bimbingan.index')->with('error', 'Data bimbingan gagal dihapus karena masih terkait dengan data lain.');
        }
    }
}