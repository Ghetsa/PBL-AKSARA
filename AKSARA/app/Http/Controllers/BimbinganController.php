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
    // Ambil ID dosen yang sedang login
    $dosenId = Auth::user()->dosen->dosen_id;

    // Ambil semua mahasiswa yang dibimbing oleh dosen ini dan aktif
    $mahasiswaIds = MahasiswaBimbinganModel::where('dosen_id', $dosenId)
      ->where('aktif', true)
      ->pluck('mahasiswa_id');

    // Ambil semua prestasi dari mahasiswa-mahasiswa tersebut
    $prestasi = PrestasiModel::with(['mahasiswa', 'bidang'])
      ->whereIn('mahasiswa_id', $mahasiswaIds)
      ->get();
    $activeMenu = 'bimbingan';
    $breadcrumb = (object) [
            'title' => 'Mahasiswa Bimbingan',
            'list' => ['Mahasiswa Bimbingan']
        ];
    return view('bimbingan.index', compact('prestasi','activeMenu','breadcrumb'));
  }
}
