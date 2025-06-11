<?php
// app/Http/Controllers/LaporanController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\PrestasiModel;
use App\Models\LombaModel;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

// Untuk Export, Anda perlu install pustaka seperti Maatwebsite/Excel
// use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\PrestasiExport; // Buat class Export ini
// use App\Exports\LombaExport;   // Buat class Export ini

class LaporanController extends Controller
{
public function index()
{
    $breadcrumb = (object) [
        'title' => 'Laporan & Analisis',
        'list' => ['Prestasi Mahasiswa', 'Laporan & Analisis']
    ];
    $activeMenu = 'laporan_analisis';

    // --- Data untuk filter di view ---
    $tahunAkademikList = PrestasiModel::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
    $kategoriLombaList = PrestasiModel::distinct()->pluck('kategori');
    $tingkatKompetisiList = PrestasiModel::distinct()->pluck('tingkat');

    // --- Statistik sederhana ---
    $totalPrestasiDisetujui = PrestasiModel::where('status_verifikasi', 'disetujui')->count();
    $totalLombaDisetujui = LombaModel::where('status_verifikasi', 'disetujui')->count();
    $mahasiswaBerprestasiCount = PrestasiModel::where('status_verifikasi', 'disetujui')
        ->distinct('mahasiswa_id')->count('mahasiswa_id');

    // --- Data untuk Chart: Prestasi per Tahun ---
    $prestasiPerTahun = PrestasiModel::where('status_verifikasi', 'disetujui')
        ->select(DB::raw('tahun'), DB::raw('COUNT(*) as total'))
        ->groupBy(DB::raw('tahun'))
        ->orderBy(DB::raw('tahun'))
        ->pluck('total', 'tahun');

    // --- Data untuk Chart: Distribusi Lomba per Bulan (hanya bulan yang ada datanya) ---
    $lombaPeriode = LombaModel::where('status_verifikasi', 'disetujui')
        ->selectRaw("DATE_FORMAT(pembukaan_pendaftaran, '%Y-%m') as periode, COUNT(*) as total")
        ->groupBy('periode')
        ->orderBy('periode')
        ->get();

    $labelsBulan = [];
    $dataBulan = [];

    foreach ($lombaPeriode as $row) {
        $date = \Carbon\Carbon::createFromFormat('Y-m', $row->periode);
        $labelsBulan[] = $date->format('M Y'); // Contoh: Jan 2025
        $dataBulan[] = $row->total;
    }

    return view('laporan.index', compact(
        'breadcrumb',
        'activeMenu',
        'tahunAkademikList',
        'kategoriLombaList',
        'tingkatKompetisiList',
        'totalPrestasiDisetujui',
        'totalLombaDisetujui',
        'mahasiswaBerprestasiCount',
        'prestasiPerTahun',
        'labelsBulan',
        'dataBulan'
    ));
}



    public function getPrestasiData(Request $request)
    {
        if ($request->ajax()) {
            $data = PrestasiModel::with(['mahasiswa.user', 'mahasiswa.prodi', 'dosen.user']) // Tambah relasi jika perlu
                ->where('status_verifikasi', 'disetujui'); // Hanya yang disetujui

            if ($request->filled('filter_tahun_akademik')) {
                $data->whereYear('tahun', $request->filter_tahun_akademik);
            }
            if ($request->filled('filter_kategori_lomba')) {
                $data->where('kategori', $request->filter_kategori_lomba);
            }
            if ($request->filled('filter_tingkat_kompetisi')) {
                $data->where('tingkat', $request->filter_tingkat_kompetisi);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('mahasiswa', function ($row) {
                    return ($row->mahasiswa->user->nama ?? 'N/A') . '<br><small class="text-muted">(' . ($row->mahasiswa->nim ?? '-') . ' - ' . ($row->mahasiswa->prodi->nama_prodi ?? '-') . ')</small>';
                })
                ->editColumn('tahun', function ($row) {
                    return Carbon::parse($row->tahun)->isoFormat('D MMM YYYY');
                })
                ->rawColumns(['mahasiswa'])
                ->make(true);
        }
        return abort(403);
    }

    public function getLombaData(Request $request)
    {
        if ($request->ajax()) {
            $data = LombaModel::where('status_verifikasi', 'disetujui'); // Hanya yang disetujui

            if ($request->filled('filter_tahun_lomba')) { // Asumsi ada filter tahun untuk lomba
                // Ini perlu disesuaikan, mungkin berdasarkan created_at atau tanggal pelaksanaan jika ada
                $data->whereYear('created_at', $request->filter_tahun_lomba);
            }
            if ($request->filled('filter_kategori_lomba_main')) {
                $data->where('kategori', $request->filter_kategori_lomba_main);
            }
            if ($request->filled('filter_tingkat_lomba_main')) {
                $data->where('tingkat', $request->filter_tingkat_lomba_main);
            }


            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('batas_pendaftaran', function ($row) {
                    return Carbon::parse($row->batas_pendaftaran)->isoFormat('D MMM YYYY');
                })
                ->make(true);
        }
        return abort(403);
    }

    // Implementasi export memerlukan pustaka seperti Maatwebsite/Excel
    // public function exportPrestasi(Request $request)
    // {
    //     // Logika filter sama seperti getPrestasiData
    //     $filters = $request->only(['filter_tahun_akademik', 'filter_kategori_lomba', 'filter_tingkat_kompetisi']);
    //     return Excel::download(new PrestasiExport($filters), 'laporan_prestasi.xlsx');
    // }

    // public function exportLomba(Request $request)
    // {
    //     // Logika filter sama seperti getLombaData
    //      $filters = $request->only(['filter_tahun_lomba', 'filter_kategori_lomba_main', 'filter_tingkat_lomba_main']);
    //     return Excel::download(new LombaExport($filters), 'laporan_lomba.xlsx');
    // }
}
