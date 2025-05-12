<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) { // Jika user tidak login
            return redirect('login');
        }

        $user = Auth::user();
        foreach ($roles as $role) {
            if ($user->role == $role) { // Asumsi kolom 'role' ada di model User
                return $next($request);
            }
        }

        // Jika tidak ada peran yang cocok, bisa redirect atau abort
        // abort(403, 'Akses Ditolak: Anda tidak memiliki peran yang sesuai.');
        return redirect('/')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
    }
}
