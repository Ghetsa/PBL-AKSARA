<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfilController extends Controller
{
    public function index()
    {
        $activeMenu = "";
        $breadcrumb = (object) [
            'title' => 'Profil pengguna',
            'list' => ['Dashboard', 'Profil']
        ];

        $user = Auth::user();

        if ($user->role === 'admin') {
            $user->load('admin');
        }

        if ($user->role === 'dosen') {
            $user->load([
                'dosen',
                'keahlian',
                'pengalaman',
                'minat'
            ]);
        }

        if ($user->role === 'mahasiswa') {
            $user->load([
                'mahasiswa.prodi',
                'mahasiswa.periode',
                'keahlian',
                'pengalaman',
                'minat',
                'mahasiswa.prestasi'
            ]);
        }

        return view('profil.index', compact('user', 'activeMenu', 'breadcrumb'));
    }
}
