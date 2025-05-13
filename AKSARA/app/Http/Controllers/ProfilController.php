<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProdiModel;
use App\Models\PeriodeModel;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        $prodi = ProdiModel::all();
        $periode = PeriodeModel::all();

        return view('profil.index', compact('user', 'role', 'prodi', 'periode'));
    }
}
