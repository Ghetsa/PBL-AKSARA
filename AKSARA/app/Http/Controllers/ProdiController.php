<?php

namespace App\Http\Controllers;

use App\Models\ProdiModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // Untuk validasi unique ignore
use Illuminate\Support\Facades\Log; // Untuk logging
use Exception; // Untuk menangkap exception
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProdiController extends Controller
{
    public function index()
    {
        $data = ProdiModel::all();
        $breadcrumb = (object) [
            'title' => 'Data Program Studi',
            'list' => ['Akademik', 'Program Studi']
        ];
        $activeMenu = 'prodi';

        return view('prodi.index', compact('data', 'breadcrumb', 'activeMenu'));
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
                // $btn = '<a href="' . url('/prodi/' . $prodi->prodi_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                // $btn .= '<a href="' . url('/prodi/' . $prodi->prodi_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' .
                //     url('/prodi/' . $prodi->prodi_id) . '">'
                //     . csrf_field() . method_field('DELETE') .
                //     '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                // Tombol Hapus tetap menggunakan deleteConfirmAjax yang sudah memanggil modalAction
                $btn = '<button onclick="modalAction(\'' . e(route('prodi.show', $prodi->prodi_id)) . '\')" class="btn btn-outline-info btn-sm"><i class="fas fa-eye"></i></button> ';
                $btn .= '<button onclick="modalAction(\'' . e(route('prodi.edit', $prodi->prodi_id)) . '\')" class="btn btn-outline-warning btn-sm"><i class="fas fa-edit"></i></button> ';
                $btn .= '<button onclick="deleteConfirmAjax(' . e($prodi->prodi_id) . ')" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function create()
    {
        return view('prodi.create');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'kode' => 'required|string|max:10|unique:program_studi,kode',
                'nama' => 'required|string|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $prodi = ProdiModel::create([
                'kode' => $request->kode,
                'nama' => $request->nama
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data program studi berhasil ditambahkan'
            ]);
        }

        return redirect('/');
    }

    public function edit($prodi_id)
    {
        // Pastikan request adalah AJAX
        // if (!request()->ajax()) {
        //     abort(403, 'Akses tidak diizinkan.');
        // }

        $prodi = ProdiModel::find($prodi_id); // Ganti 'prodi_id' jika nama primary key berbeda

        if (!$prodi) {
            // Jika menggunakan if (!request()->ajax()) di atas, ini tidak akan dieksekusi jika request bukan AJAX
            // Jika view dipanggil langsung (tanpa AJAX), Anda mungkin ingin redirect atau abort
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Data program studi tidak ditemukan.'], 404);
            }
            abort(404, 'Data program studi tidak ditemukan.');
        }
        // Kirim data prodi ke view yang akan dimuat di modal
        return view('prodi.edit', compact('prodi'));
    }

    public function update(Request $request, $prodi_id)
    {
        if (!($request->ajax() || $request->wantsJson())) {
            return response()->json(['status' => false, 'message' => 'Akses tidak diizinkan'], 403);
        }

        $prodi = ProdiModel::find($prodi_id);

        if (!$prodi) {
            return response()->json([
                'status' => false,
                'message' => 'Data program studi tidak ditemukan.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode' => [
                'required',
                'string',
                'max:10',
                Rule::unique('program_studi', 'kode')->ignore($prodi->prodi_id, 'prodi_id')
            ],
            'nama' => 'required|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $prodi->kode = $request->kode;
            $prodi->nama = $request->nama;
            $prodi->save();

            return response()->json([
                'status' => true,
                'message' => 'Data program studi berhasil diperbarui',
                'data' => $prodi // Opsional: kirim data yang diupdate
            ], 200); // Status 200 untuk OK

        } catch (Exception $e) {
            Log::error("Error updating prodi ID {$prodi_id}: " . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data program studi. Terjadi kesalahan server.'
            ], 500);
        }
    }

    public function show($prodi_id)
    {
        $prodi = ProdiModel::find($prodi_id); // Ganti 'prodi_id' jika nama primary key berbeda

        if (!$prodi) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Data program studi tidak ditemukan.'], 404);
            }
            // Jika bukan AJAX, bisa redirect atau abort
            return abort(404, 'Data program studi tidak ditemukan.');
        }

        // Kirim data prodi ke view yang akan dimuat di modal
        return view('prodi.show', compact('prodi'));
    }

    // public function destroy($id)
    // {
    //     ProdiModel::destroy($id);
    //     return redirect()->route('prodi.index')->with('success', 'Data program studi berhasil dihapus');
    // }

    public function confirm_ajax($id)
    {
        $prodi = ProdiModel::find($id);

        return view('prodi.confirm_ajax', compact('prodi'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $prodi = ProdiModel::find($id);

            if ($prodi) {
                try {
                    $prodi->delete(); // Hapus data prodi 

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
                ], 404); // Kembalikan 404 jika prodi tidak ditemukan
            }
        }

        // Jika bukan AJAX, redirect
        return redirect('/');
    }

    public function export_excel()
    {
        $prodi = ProdiModel::select('kode', 'nama')
            ->orderBy('kode')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Prodi');
        $sheet->setCellValue('C1', 'Nama Prodi');
        // $sheet->setCellValue('D1', 'Periode Aktif'); // Example if you use periode

        $sheet->getStyle('A1:C1')->getFont()->setBold(true); // Adjust range if more columns

        $no = 1;
        $baris = 2;
        foreach ($prodi as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->kode);
            $sheet->setCellValue('C' . $baris, $value->nama);
            $baris++;
            $no++;
        }

        foreach (range('A', 'C') as $columnID) { // Adjust range if more columns
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Program Studi');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Program Studi ' . date('Y-m-d H_i_s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        // Additional headers from your example
        header('Cache-Control: max-age=1'); // Replaced $writer->setPreCalculateFormulas(false);
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');


        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $prodi = ProdiModel::select('kode', 'nama')
            ->orderBy('kode')
            ->get();

        $pdf = Pdf::loadView('prodi.export_pdf', ['prodi' => $prodi]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true);
        // $pdf->render(); // render() is often called by stream() or download()

        return $pdf->stream('Data Program Studi ' . date('Y-m-d H_i_s') . '.pdf');
    }

    public function checkKode(Request $request)
    {
        $kode = $request->input('kode');
        $ignoreId = $request->input('ignore_id'); // Akan dikirim dari form edit

        $query = ProdiModel::where('kode', $kode);

        // Jika ada ignoreId (saat mengedit), kecualikan prodi dengan ID tersebut dari pengecekan
        if ($ignoreId) {
            $query->where('prodi_id', '!=', $ignoreId);
        }

        $isExists = $query->exists();

        if ($isExists) {
            // Jika sudah ada, kembalikan string pesan error dalam format JSON.
            // jQuery validation akan menganggap ini sebagai kegagalan validasi.
            return response()->json('Kode program studi ini sudah terdaftar.');
        } else {
            // Jika unik (tidak ada), kembalikan 'true'.
            // jQuery validation akan menganggap ini sebagai keberhasilan validasi.
            return response()->json(true);
        }
    }
}
