<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserModel;
use Illuminate\Support\Facades\Storage;
use App\Models\MinatModel;
use App\Models\KeahlianModel;
use App\Models\PengalamanModel;
use App\Models\PrestasiModel;
use Illuminate\Support\Facades\DB;
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
    public function edit_ajax()
    {
        $user = Auth::user();

        if ($user->role === 'dosen') {
            $user->load(['dosen', 'keahlian', 'pengalaman', 'minat']);
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

        return view('profil._form_edit_ajax', compact('user'));
    }

    public function update_ajax(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction();

        try {
            // Validasi umum
            $request->validate([
                'nama' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username,' . $user->id,
                'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'keahlian_nama' => 'nullable|array',
                'minat_nama' => 'nullable|array',
                'pengalaman' => 'nullable|array',
                'prestasi' => 'nullable|array',
            ]);

            // Update data user
            $user->update([
                'nama' => $request->nama,
                'username' => $request->username,
            ]);

            // Update foto jika ada
            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo && Storage::exists('public/' . $user->profile_photo)) {
                    Storage::delete('public/' . $user->profile_photo);
                }
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                $user->update(['profile_photo' => $path]);
            }

            // Clear existing related data
            $user->keahlian()->delete();
            $user->minat()->delete();
            $user->pengalaman()->delete();
            if ($user->role === 'mahasiswa') {
                $user->mahasiswa->prestasi()->delete();
            }

            // Simpan keahlian
            foreach ($request->keahlian_nama ?? [] as $keahlian) {
                $user->keahlian()->create(['nama' => $keahlian]);
            }

            // Simpan minat
            foreach ($request->minat_nama ?? [] as $minat) {
                $user->minat()->create(['nama' => $minat]);
            }

            // Simpan pengalaman
            foreach ($request->pengalaman ?? [] as $pengalaman) {
                $user->pengalaman()->create(['deskripsi' => $pengalaman]);
            }

            // Simpan prestasi (hanya untuk mahasiswa)
            if ($user->role === 'mahasiswa') {
                foreach ($request->prestasi ?? [] as $prestasi) {
                    $user->mahasiswa->prestasi()->create(['deskripsi' => $prestasi]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Profil berhasil diperbarui.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memperbarui profil.', 'error' => $e->getMessage()], 500);
        }
    }
}
