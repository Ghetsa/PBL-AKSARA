<?php

namespace App\Http\Controllers;

use App\Models\PeriodeModel;
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

class PeriodeController extends Controller
{
    public function index()
    {
        $data = PeriodeModel
            ::all();
        $breadcrumb = (object) [
            'title' => 'Data periode semester',
            'list' => ['Akademik', 'Periode Semester']
        ];
        $activeMenu = 'periode';

        return view('periode.index', compact('data', 'breadcrumb', 'activeMenu'));
    }

    // Ambil data periode dalam bentuk json untuk datatables
    public function list(Request $request)
    {
        $periodes = PeriodeModel
            ::select('periode_id', 'semester', 'tahun_akademik');

        return DataTables::of($periodes)
            // menambahkan kolom index / no urut (default nama kolom: DT_Rowindex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($periode) {  // menambahkan kolom aksi
                // $btn = '<a href="' . url('/periode/' . $periode->periode_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                // $btn .= '<a href="' . url('/periode/' . $periode->periode_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' .
                //     url('/periode/' . $periode->periode_id) . '">'
                //     . csrf_field() . method_field('DELETE') .
                //     '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                // Tombol Hapus tetap menggunakan deleteConfirmAjax yang sudah memanggil modalAction
                $btn = '<button onclick="modalAction(\'' . e(route('periode.show', $periode->periode_id)) . '\')" class="btn btn-outline-info btn-sm"><i class="fas fa-eye"></i></button> ';
                $btn .= '<button onclick="modalAction(\'' . e(route('periode.edit', $periode->periode_id)) . '\')" class="btn btn-outline-warning btn-sm"><i class="fas fa-edit"></i></button> ';
                $btn .= '<button onclick="deleteConfirmAjax(' . e($periode->periode_id) . ')" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function create()
    {
        return view('periode.create');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'semester' => 'required|string|max:10',
                'tahun_akademik' => 'required|string|max:10'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $periode = PeriodeModel
                ::create([
                    'semester' => $request->semester,
                    'tahun_akademik' => $request->tahun_akademik
                ]);

            return response()->json([
                'status' => true,
                'message' => 'Data periode semester berhasil ditambahkan'
            ]);
        }

        return redirect('/');
    }

    public function edit($periode_id)
    {
        $periode = PeriodeModel
            ::find($periode_id);

        if (!$periode) {
            // Jika menggunakan if (!request()->ajax()) di atas, ini tidak akan dieksekusi jika request bukan AJAX
            // Jika view dipanggil langsung (tanpa AJAX), Anda mungkin ingin redirect atau abort
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['' => false, 'message' => 'Data periode semester tidak ditemukan.'], 404);
            }
            abort(404, 'Data periode semester tidak ditemukan.');
        }
        // Kirim data periode ke view yang akan dimuat di modal
        return view('periode.edit', compact('periode'));
    }

    public function update(Request $request, $periode_id)
    {
        if (!($request->ajax() || $request->wantsJson())) {
            return response()->json(['' => false, 'message' => 'Akses tidak diizinkan'], 403);
        }

        $periode = PeriodeModel
            ::find($periode_id);

        if (!$periode) {
            return response()->json([
                'status' => false,
                'message' => 'Data periode semester tidak ditemukan.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'semester' => 'required|string|max:10',
            'tahun_akademik' => 'required|string|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $periode->semester = $request->semester;
            $periode->tahun_akademik = $request->tahun_akademik;
            $periode->save();

            return response()->json([
                'status' => true,
                'message' => 'Data periode semester berhasil diperbarui',
                'data' => $periode // Opsional: kirim data yang diupdate
            ], 200); //  200 untuk OK

        } catch (Exception $e) {
            Log::error("Error updating periode ID {$periode_id}: " . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data periode semester. Terjadi kesalahan server.'
            ], 500);
        }
    }

    public function show($periode_id)
    {
        $periode = PeriodeModel
            ::find($periode_id); // Ganti 'periode_id' jika nama primary key berbeda

        if (!$periode) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['' => false, 'message' => 'Data periode semester tidak ditemukan.'], 404);
            }
            // Jika bukan AJAX, bisa redirect atau abort
            return abort(404, 'Data periode semester tidak ditemukan.');
        }

        // Kirim data periode ke view yang akan dimuat di modal
        return view('periode.show', compact('periode'));
    }

    public function confirm_ajax($id)
    {
        $periode = PeriodeModel::find($id);

        return view('periode.confirm_ajax', compact('periode'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $periode = PeriodeModel::find($id);

            if ($periode) {
                try {
                    $periode->delete(); // Hapus data periode 

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
                ], 404); // Kembalikan 404 jika periode tidak ditemukan
            }
        }

        // Jika bukan AJAX, redirect
        return redirect('/');
    }

    public function export_excel()
    {
        $periode = PeriodeModel::select('periode_id', 'semester', 'tahun_akademik')
            ->orderBy('tahun_akademik', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Semester');
        $sheet->setCellValue('C1', 'Tahun Akademik');

        $sheet->getStyle('A1:DC1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($periode as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->semester);
            $sheet->setCellValue('C' . $baris, $value->tahun_akademik);
            $baris++;
            $no++;
        }

        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Periode Semester');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Periode Semester ' . date('Y-m-d H_i_s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $periode = PeriodeModel::select('periode_id', 'semester', 'tahun_akademik')
            ->orderBy('tahun_akademik', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $pdf = Pdf::loadView('periode.export_pdf', ['periode' => $periode]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf->stream('Data Periode Semester ' . date('Y-m-d H_i_s') . '.pdf');
    }
}
