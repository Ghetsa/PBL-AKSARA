<?php

namespace App\Http\Controllers;

use App\Models\ProdiModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ProdiController extends Controller
{
    public function index()
    {
        $data = ProdiModel::all();
        $breadcrumb = (object) [
            'title' => 'Data Program Studi',
            'list' => ['Prodi']
        ];

        return view('prodi.index', compact('data', 'breadcrumb'));
    }

    // Ambil data prodi dalam bentuk json untuk datatables
    public function list(Request $request)
    {
        $prodis = ProdiModel::select('prodi_id', 'kode', 'nama');

        // Filter data prodi berdasarkan role
        // if ($request->role) {
        //     $prodis->where('role', $request->role);
        // }

        return DataTables::of($prodis)
            // menambahkan kolom index / no urut (default nama kolom: DT_Rowindex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($prodi) {  // menambahkan kolom aksi
                $btn = '<a href="' . url('/prodi/' . $prodi->prodi_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/prodi/' . $prodi->prodi_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' .
                    url('/prodi/' . $prodi->prodi_id) . '">'
                    . csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Prodi',
            'list' => ['Prodi', 'Tambah']
        ];

        return view('prodi.create', compact('breadcrumb'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $prodi = ProdiModel::create([
            'nama' => $validated['nama'],
        ]);

        return redirect()->route('prodi.index')->with('success', 'Data program studi berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = ProdiModel::findOrFail($id);

        $breadcrumb = (object) [
            'title' => 'Edit Prodi',
            'list' => ['Prodi', 'Edit']
        ];

        return view('prodi.edit', compact('breadcrumb', 'data'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $prodi = ProdiModel::findOrFail($id);
        $prodi->update([
            'nama' => $validated['nama']
        ]);
        return redirect()->route('prodi.index')->with('success', 'Data program studi berhasil diupdate');
    }

    public function destroy($id)
    {
        ProdiModel::destroy($id);
        return redirect()->route('prodi.index')->with('success', 'Data program studi berhasil dihapus');
    }
}
