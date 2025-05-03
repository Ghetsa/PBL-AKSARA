<?php

namespace App\Http\Controllers;

use App\Models\PrestasiModel;
use Illuminate\Http\Request;

class PrestasiController extends Controller
{
    public function index()
    {
        $data = PrestasiModel::all();
        $breadcrumb = (object)[
            'title' => 'Manajemen Prestasi',
            'list' => ['Dashboard', 'Prestasi']
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
            'nama_prestasi' => 'required|string|max:255',
            'kategori' => 'required|in:akademik,non-akademik,lainnya',
            'penyelenggara' => 'required|string|max:255',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'tahun' => 'required|digits:4|integer|min:2000|max:' . date('Y'),
            'file_bukti' => 'nullable|string|max:255',
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
            'nama_prestasi' => 'required|string|max:255',
            'kategori' => 'required|in:akademik,non-akademik,lainnya',
            'penyelenggara' => 'required|string|max:255',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'tahun' => 'required|digits:4|integer|min:2000|max:' . date('Y'),
            'file_bukti' => 'nullable|string|max:255',
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
}