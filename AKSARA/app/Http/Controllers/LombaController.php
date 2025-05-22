<?php

namespace App\Http\Controllers;

use App\Models\LombaModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LombaController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Lomba',
            'list' => ['Dashboard', 'Lomba']
        ];

         $activeMenu = 'lomba';

        return view('lomba.index', compact('breadcrumb', 'activeMenu'));
    }

    public function getList(Request $request)
    {
        $query = LombaModel::query();

        // Filter pencarian
        if ($request->filled('search_nama')) {
            $search = $request->search_nama;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lomba', 'like', '%' . $search . '%')
                  ->orWhere('bidang_keahlian', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('filter_status')) {
            $query->where('status_verifikasi', $request->filter_status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $editUrl = route('lomba.edit', $row->id);
                $verifUrl = route('lomba.verifikasi', $row->id);
                $deleteUrl = route('lomba.destroy', $row->id);

                return view('components.lomba.aksi-buttons', compact('editUrl', 'verifUrl', 'deleteUrl'));
            })
            ->editColumn('pembukaan_pendaftaran', function ($row) {
                return \Carbon\Carbon::parse($row->pembukaan_pendaftaran)->format('d-m-Y');
            })
            ->editColumn('batas_pendaftaran', function ($row) {
                return \Carbon\Carbon::parse($row->batas_pendaftaran)->format('d-m-Y');
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('lomba.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lomba' => 'required|string|max:255',
            'pembukaan_pendaftaran' => 'required|date',
            'kategori' => 'required|in:akademik,non-akademik,lainnya',
            'penyelenggara' => 'required|string|max:255',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|string|max:255',
            'link_pendaftaran' => 'nullable|string|max:255',
            'batas_pendaftaran' => 'required|date',
            'status_verifikasi' => 'required|in:pending,disetujui,ditolak',
            'diinput_oleh' => 'required|integer'
        ]);

        LombaModel::create($validated);

        return redirect()->route('lomba.index')->with('success', 'Lomba berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = LombaModel::findOrFail($id);
        return view('lomba.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_lomba' => 'required|string|max:255',
            'pembukaan_pendaftaran' => 'required|date',
            'kategori' => 'required|in:akademik,non-akademik,lainnya',
            'penyelenggara' => 'required|string|max:255',
            'tingkat' => 'required|in:lokal,nasional,internasional',
            'bidang_keahlian' => 'required|string|max:255',
            'link_pendaftaran' => 'nullable|string|max:255',
            'batas_pendaftaran' => 'required|date',
            'status_verifikasi' => 'required|in:pending,disetujui,ditolak',
            'diinput_oleh' => 'required|integer'
        ]);

        $data = LombaModel::findOrFail($id);
        $data->update($validated);

        return redirect()->route('lomba.index')->with('success', 'Lomba berhasil diupdate');
    }

    public function destroy($id)
    {
        LombaModel::destroy($id);
        return redirect()->route('lomba.index')->with('success', 'Lomba berhasil dihapus');
    }

    public function verifikasi($id)
    {
        $data = LombaModel::findOrFail($id);
        return view('lomba.verifikasi', compact('data'));
    }

    public function prosesVerifikasi(Request $request, $id)
    {
        $validated = $request->validate([
            'status_verifikasi' => 'required|in:pending,disetujui,ditolak',
            'catatan_verifikasi' => 'nullable|string'
        ]);

        $data = LombaModel::findOrFail($id);
        $data->update($validated);

        return redirect()->route('lomba.index')->with('success', 'Verifikasi berhasil diperbarui');
    }
}
