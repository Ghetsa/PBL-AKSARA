<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MahasiswaBimbinganModel;
use App\Models\PrestasiModel;
use Illuminate\Support\Facades\Auth;

class BimbinganController extends Controller
{
  public function index()
  {
    $activeMenu = 'bimbingan';
    $breadcrumb = (object) [
      'title' => 'Mahasiswa Bimbingan',
      'list' => ['Mahasiswa Bimbingan']
    ];

    return view('bimbingan.index', compact('activeMenu', 'breadcrumb'));
  }

  // DataTables list
  public function list()
  {
    $dosenId = Auth::user()->dosen->dosen_id;

    $mahasiswaIds = MahasiswaBimbinganModel::where('dosen_id', $dosenId)
      ->where('aktif', true)
      ->pluck('mahasiswa_id');

    $prestasi = PrestasiModel::with(['mahasiswa.user', 'bidang'])
      ->whereIn('mahasiswa_id', $mahasiswaIds)
      ->orderByRaw("FIELD(status_verifikasi, 'pending', 'disetujui', 'ditolak')")
      ->get();

    return datatables()->of($prestasi)
      ->addIndexColumn()
      ->addColumn('mahasiswa_nama', fn($item) => $item->mahasiswa->user->nama ?? '-')
      ->addColumn('bidang_nama', fn($item) => $item->bidang->bidang_nama ?? '-')
      ->editColumn('kategori', fn($item) => ucfirst($item->kategori))
      ->editColumn('tingkat', fn($item) => ucfirst($item->tingkat))
      ->editColumn('status_verifikasi', function ($item) {
        return match ($item->status_verifikasi) {
          'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
          'disetujui' => '<span class="badge bg-success">Disetujui</span>',
          'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
          default => '<span class="badge bg-secondary">' . ucfirst($item->status_verifikasi) . '</span>',
        };
      })
      ->addColumn('aksi', function ($item) {
        $verifyUrl = route('bimbingan.verify_form', $item->prestasi_id);
        return '<button type="button" class="btn btn-sm btn-info" onclick="modalAction(\'' . $verifyUrl . '\')">
                        <i class="fas fa-search-plus"></i> Verifikasi
                    </button>';
      })
      ->rawColumns(['status_verifikasi', 'aksi'])
      ->make(true);
  }


  // Show verify form (modal AJAX)
  public function showVerifyForm($id)
  {
    $prestasi = PrestasiModel::with(['mahasiswa.user', 'bidang'])->findOrFail($id);

    // Pastikan hanya dosen pembimbing yang bisa memverifikasi
    $dosenId = Auth::user()->dosen->dosen_id;
    $allowed = MahasiswaBimbinganModel::where('dosen_id', $dosenId)
      ->where('mahasiswa_id', $prestasi->mahasiswa_id)
      ->where('aktif', true)
      ->exists();

    if (!$allowed) {
      return response()->json(['message' => 'Akses ditolak.'], 403);
    }

    return view('bimbingan.verify_form', compact('prestasi'));
  }

  // Proses verifikasi
  public function processVerification(Request $request, $id)
  {
    $request->validate([
      'status_verifikasi' => 'required|in:disetujui,ditolak',
      'catatan' => 'nullable|string|max:255'
    ]);

    $prestasi = PrestasiModel::findOrFail($id);

    $dosenId = Auth::user()->dosen->dosen_id;
    $allowed = MahasiswaBimbinganModel::where('dosen_id', $dosenId)
      ->where('mahasiswa_id', $prestasi->mahasiswa_id)
      ->where('aktif', true)
      ->exists();

    if (!$allowed) {
      return response()->json(['message' => 'Akses ditolak.'], 403);
    }

    $prestasi->status_verifikasi = $request->status_verifikasi;
    $prestasi->catatan_verifikasi = $request->catatan;
    $prestasi->verified_by = $dosenId;
    $prestasi->verified_at = now();
    $prestasi->save();

    return response()->json(['message' => 'Verifikasi berhasil.']);
  }

  // Optional: show detail
  public function showAjax($id)
  {
    $prestasi = PrestasiModel::with(['mahasiswa.user', 'bidang'])->findOrFail($id);
    return view('bimbingan.partials.show', compact('prestasi'));
  }
}
