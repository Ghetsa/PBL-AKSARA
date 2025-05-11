<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\UserModel;
use App\Models\MahasiswaModel;
use App\Models\DosenModel;
use App\Models\AdminModel;
use App\Models\PeriodeModel;
use App\Models\ProdiModel;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('user.index'); // ATAU redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $username = $request->input('username'); // bisa NIP/NIM
            $password = $request->input('password');

            // Coba cari berdasarkan nim di tabel mahasiswa
            $mahasiswa = MahasiswaModel::where('nim', $username)->first();
            if ($mahasiswa && Hash::check($password, $mahasiswa->user->password)) {
                Auth::login($mahasiswa->user);
                return response()->json([
                    'status' => true,
                    'message' => 'Login Mahasiswa Berhasil',
                    // Arahkan ke halaman user setelah login berhasil
                    'redirect' => route('user.index')
                ]);
            }

            // Coba cari berdasarkan nip di tabel dosen
            $dosen = DosenModel::where('nip', $username)->first();
            if ($dosen && Hash::check($password, $dosen->user->password)) {
                Auth::login($dosen->user);
                return response()->json([
                    'status' => true,
                    'message' => 'Login Dosen Berhasil',
                    // Arahkan ke halaman user setelah login berhasil
                    'redirect' => route('user.index')
                ]);
            }

            // Coba cari berdasarkan nip di tabel admin
            $admin = AdminModel::where('nip', $username)->first();
            if ($admin && Hash::check($password, $admin->user->password)) {
                Auth::login($admin->user);
                return response()->json([
                    'status' => true,
                    'message' => 'Login Admin Berhasil',
                    // Arahkan ke halaman user setelah login berhasil
                    'redirect' => route('user.index')
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'NIP/NIM atau Password salah'
            ]);
        }

        return redirect('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }

    public function register()
    {
        $prodi = ProdiModel::select('prodi_id', 'kode', 'nama')->get();
        $periode = PeriodeModel::select('periode_id', 'semester', 'tahun_akademik')->get();
        return view('auth.register', compact('prodi', 'periode'));
    }

    public function postregister(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'nama'     => 'required|string|max:255',
                'email'    => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:5|max:255',
                'role'     => 'required|in:admin,mahasiswa,dosen',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            UserModel::create([
                'nama'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
                'status'   => 'aktif',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Registrasi berhasil!',
                'redirect' => url('login')
            ]);
        }

        return redirect('register');
    }
}
