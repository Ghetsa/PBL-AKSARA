<?php
// app/Http/Controllers/LaporanController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrestasiModel;
use App\Models\LombaModel;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
// Untuk Export, Anda perlu install pustaka seperti Maatwebsite/Excel
// use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\PrestasiExport; // Buat class Export ini
// use App\Exports\LombaExport;   // Buat class Export ini

class LaporanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) ['title' => 'Laporan & Analisis', 'list' => ['Admin', 'Laporan']];
        $activeMenu = 'laporan_analisis';

        // Data untuk filter
        $tahunAkademikList = PrestasiModel::selectRaw('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
        // Anda mungkin perlu logika lebih kompleks untuk tahun akademik (misal: 2023/2024)

        $kategoriLombaList = PrestasiModel::distinct()->pluck('kategori'); // Atau dari tabel master jika ada
        $tingkatKompetisiList = PrestasiModel::distinct()->pluck('tingkat');

        // Data untuk Statistik Sederhana
        $totalPrestasiDisetujui = PrestasiModel::where('status_verifikasi', 'disetujui')->count();
        $totalLombaDisetujui = LombaModel::where('status_verifikasi', 'disetujui')->count();
        $mahasiswaBerprestasiCount = PrestasiModel::where('status_verifikasi', 'disetujui')
            ->distinct('mahasiswa_id')->count('mahasiswa_id');

        // Data untuk Chart (contoh: Prestasi per Tahun)
        $prestasiPerTahun = PrestasiModel::where('status_verifikasi', 'disetujui')
            ->selectRaw('tahun, count(*) as jumlah')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->pluck('jumlah', 'tahun');

        return view('laporan.index', compact(
            'breadcrumb',
            'activeMenu',
            'tahunAkademikList',
            'kategoriLombaList',
            'tingkatKompetisiList',
            'totalPrestasiDisetujui',
            'totalLombaDisetujui',
            'mahasiswaBerprestasiCount',
            'prestasiPerTahun'
        ));
    }

    public function getPrestasiData(Request $request)
    {
        if ($request->ajax()) {
            $data = PrestasiModel::with(['mahasiswa.user', 'mahasiswa.prodi', 'periode']) // Tambah relasi jika perlu
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
