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
    // public function edit_ajax()
    // {
    //     $user = Auth::user();
    //     $activeMenu = "";
    //     $breadcrumb = (object) [
    //         'title' => 'Profil pengguna',
    //         'list' => ['Dashboard', 'Profil']
    //     ];
    //     if ($user->role === 'dosen') {
    //         $user->load(['dosen', 'keahlian', 'pengalaman', 'minat']);
    //     }

    //     if ($user->role === 'mahasiswa') {
    //         $user->load([
    //             'mahasiswa.prodi',
    //             'mahasiswa.periode',
    //             'keahlian',
    //             'pengalaman',
    //             'minat',
    //             'mahasiswa.prestasi'
    //         ]);
    //     }

    //     return view('profil.edit', compact('user', 'activeMenu', 'breadcrumb'));
    // }

    // public function edit_ajax()
    // {
    //     $user = Auth::user()->load([
    //         'mahasiswa.prodi',
    //         'mahasiswa.periode',
    //         'mahasiswa.prestasi',
    //         'dosen',
    //         'admin',
    //         'keahlian',
    //         'minat',
    //         'pengalaman'
    //     ]);

    //     $keahlianList = KeahlianModel::all();
    //     $pengalamanList = PengalamanModel::all();
    //     $minatList = MinatModel::all();
    //     $selectedKeahlianIds = $user->keahlian->pluck('keahlian_id')->toArray();
    //     $selectedPengalamanIds = $user->pengalaman->pluck('pengalaman_id')->toArray();
    //     $selectedMinatIds = $user->minat->pluck('minat_id')->toArray();

    //     return view('profil.edit', compact('user', 'keahlianList', 'selectedKeahlianIds','selectedMinatIds','selectedPengalamanIds'));
    // }


    // public function update_ajax(Request $request)
    // {
    //     $user = Auth::user();
    //     DB::beginTransaction();

    //     try {
    //         // Validasi umum
    //         $request->validate([
    //             'nama' => 'required|string|max:255',
    //             'username' => 'required|string|max:255|unique:users,username,' . $user->id,
    //             'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //             'keahlian_nama' => 'nullable|array',
    //             'minat_nama' => 'nullable|array',
    //             'pengalaman' => 'nullable|array',
    //             'prestasi' => 'nullable|array',
    //         ]);

    //         // Update data user
    //         $user->update([
    //             'nama' => $request->nama,
    //             'username' => $request->username,
    //         ]);

    //         // Update foto jika ada
    //         if ($request->hasFile('profile_photo')) {
    //             if ($user->profile_photo && Storage::exists('public/' . $user->profile_photo)) {
    //                 Storage::delete('public/' . $user->profile_photo);
    //             }
    //             $path = $request->file('profile_photo')->store('profile_photos', 'public');
    //             $user->update(['profile_photo' => $path]);
    //         }

    //         // Clear existing related data
    //         $user->keahlian()->delete();
    //         $user->minat()->delete();
    //         $user->pengalaman()->delete();
    //         if ($user->role === 'mahasiswa') {
    //             $user->mahasiswa->prestasi()->delete();
    //         }

    //         // Simpan keahlian
    //         foreach ($request->keahlian_nama ?? [] as $keahlian) {
    //             $user->keahlian()->create(['nama' => $keahlian]);
    //         }

    //         // Simpan minat
    //         foreach ($request->minat_nama ?? [] as $minat) {
    //             $user->minat()->create(['nama' => $minat]);
    //         }

    //         // Simpan pengalaman
    //         foreach ($request->pengalaman ?? [] as $pengalaman) {
    //             $user->pengalaman()->create(['deskripsi' => $pengalaman]);
    //         }

    //         // Simpan prestasi (hanya untuk mahasiswa)
    //         if ($user->role === 'mahasiswa') {
    //             foreach ($request->prestasi ?? [] as $prestasi) {
    //                 $user->mahasiswa->prestasi()->create(['deskripsi' => $prestasi]);
    //             }
    //         }

    //         DB::commit();
    //         return response()->json(['message' => 'Profil berhasil diperbarui.']);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['message' => 'Gagal memperbarui profil.', 'error' => $e->getMessage()], 500);
    //     }
    // }

    public function edit_ajax()
    {
        $user = Auth::user()->load([
            'mahasiswa.prodi',
            'mahasiswa.periode',
            'mahasiswa.prestasi',
            'dosen',
            'admin',
            'keahlian',
            'minat',
            'pengalaman'
        ]);

        // Anda tidak perlu mengambil semua list jika inputnya berupa text dinamis
        // $keahlianList = KeahlianModel::all();
        // $pengalamanList = PengalamanModel::all(); // Ini mungkin tidak relevan untuk form edit dinamis
        // $minatList = MinatModel::all();

        // Yang Anda perlukan adalah data yang sudah dimiliki user
        $selectedKeahlian = $user->keahlian; // Collection dari KeahlianModel terkait user
        $selectedMinat = $user->minat;         // Collection dari MinatModel
        $selectedPengalaman = $user->pengalaman; // Collection dari PengalamanModel

        // Jika mahasiswa, ambil juga prestasi
        $selectedPrestasi = collect(); // default empty collection
        if ($user->role === 'mahasiswa' && $user->mahasiswa) {
            $selectedPrestasi = $user->mahasiswa->prestasi;
        }


        return view('profil.edit', compact(
            'user',
            'selectedKeahlian',
            'selectedMinat',
            'selectedPengalaman',
            'selectedPrestasi'
            // Anda tidak perlu mengirim $keahlianList, $minatList, $pengalamanList
            // kecuali jika Anda ingin dropdown pre-defined, tapi UI LinkedIn lebih ke arah input teks dinamis.
        ));
    }


    public function update_ajax(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction();

        try {
            // Validasi Data Dasar User
            $validator = Validator::make($request->only(['nama', 'email', 'profile_photo']), [
                'nama' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // tambahkan gif jika perlu
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
            }

            $userData = ['nama' => $request->nama, 'email' => $request->email];

            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                $userData['profile_photo'] = $path;
            }
            $user->update($userData);

            // Update Detail Role-Specific (NIP, Gelar, No HP, dll.)
            if ($user->role === 'dosen' && $user->dosen) {
                $user->dosen->update($request->only(['gelar', 'no_hp'])); // Pastikan field ini ada di request dan fillable DosenModel
            }
            // Untuk mahasiswa, NIP, Prodi, Periode biasanya tidak diubah dari profil, tapi dari data akademik.

            // --- Handle Keahlian ---
            $user->keahlian()->delete(); // Hapus yang lama
            if ($request->has('keahlian_items')) {
                foreach ($request->keahlian_items as $item) {
                    if (!empty($item['nama'])) { // Hanya simpan jika nama keahlian diisi
                        $user->keahlian()->create([
                            'keahlian_nama' => $item['nama'],
                            'sertifikasi' => $item['sertifikasi'] ?? null, // Ambil sertifikasi jika ada
                        ]);
                    }
                }
            }

            // --- Handle Minat ---
            $user->minat()->delete(); // Hapus yang lama
            if ($request->has('minat_items')) {
                foreach ($request->minat_items as $namaMinat) {
                    if (!empty($namaMinat)) { // Hanya simpan jika nama minat diisi
                        $user->minat()->create(['nama_minat' => $namaMinat]); // Sesuaikan nama kolom di model
                    }
                }
            }

            // --- Handle Pengalaman ---
            $user->pengalaman()->delete(); // Hapus yang lama
            if ($request->has('pengalaman_items')) {
                foreach ($request->pengalaman_items as $item) {
                    // Validasi minimal untuk setiap item pengalaman
                    if (!empty($item['pengalaman_nama'])) {
                        $user->pengalaman()->create([
                            'pengalaman_nama' => $item['pengalaman_nama'],
                            'pengalaman_kategori' => $item['pengalaman_kategori'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui.']);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error $e->getMessage()
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui profil. Terjadi kesalahan server.', 'error' => $e->getMessage()], 500);
        }
    }
}
