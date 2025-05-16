<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\UserModel;
use App\Models\MinatModel;
use App\Models\KeahlianModel;
use App\Models\PengalamanModel;

class ProfilController extends Controller
{
    public function index()
    {
        $activeMenu = "";
        $breadcrumb = (object)[
            'title' => 'Profil pengguna',
            'list' => ['Dashboard', 'Profil']
        ];

        $user = Auth::user();

        if ($user->role === 'admin') {
            $user->load('admin');
        } elseif ($user->role === 'dosen') {
            $user->load([
                'dosen',
                'keahlian',
                'pengalaman',
                'minat'
            ]);
        } elseif ($user->role === 'mahasiswa') {
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
        $user = Auth::user()->load([
            'mahasiswa.prodi',
            'mahasiswa.periode',
            'dosen',
            'admin',
            'keahlian',
            'minat',
            'pengalaman'
        ]);

        $allKeahlianOptions = KeahlianModel::PILIHAN_KEAHLIAN;
        $allMinatOptions = MinatModel::PILIHAN_MINAT;

        $selectedKeahlian = $user->keahlian->mapWithKeys(function ($item) {
            return [$item->keahlian_nama => $item->pivot->sertifikasi ?? null];
        });

        $selectedMinat = $user->minat->pluck('nama_minat')->toArray();

        $selectedPengalaman = $user->pengalaman;
        $selectedPrestasi = ($user->role === 'mahasiswa' && $user->mahasiswa) ? $user->mahasiswa->prestasi : collect();

        return view('profil.edit', compact(
            'user',
            'allKeahlianOptions',
            'allMinatOptions',
            'selectedKeahlian',
            'selectedMinat',
            'selectedPengalaman',
            'selectedPrestasi'
        ));
    }

    public function update_ajax(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'gelar' => $user->role === 'dosen' ? 'nullable|string|max:100' : '',
                'no_hp' => $user->role === 'dosen' ? 'nullable|string|max:20' : '',
                'keahlian_pilihan' => 'nullable|array',
                'keahlian_pilihan.*' => ['string', Rule::in(KeahlianModel::PILIHAN_KEAHLIAN)],
                'minat_pilihan' => 'nullable|array',
                'minat_pilihan.*' => ['string', Rule::in(MinatModel::PILIHAN_MINAT)],
                'sertifikasi_file' => 'nullable|array',
                'sertifikasi_file.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'pengalaman_items' => 'nullable|array',
                'pengalaman_items.*.pengalaman_nama' => 'required_with:pengalaman_items.*.pengalaman_kategori|string|max:255',
                'pengalaman_items.*.pengalaman_kategori' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
            }

            $userData = ['nama' => $request->nama, 'email' => $request->email];

            if ($request->hasFile('foto')) {
                if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                    Storage::disk('public')->delete($user->foto);
                }
                $file = $request->file('foto');
                $filename = 'foto_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_photos/' . $user->id, $filename, 'public');
                $userData['foto'] = $path;
            }

            $user->update($userData);

            if ($user->role === 'dosen' && $user->dosen) {
                $user->dosen->update($request->only(['gelar', 'no_hp']));
            }

            // --- Handle Keahlian (pivot with sertifikasi) ---
            $selectedKeahlian = $request->input('keahlian_pilihan', []);
            $keahlianSyncData = [];

            foreach ($selectedKeahlian as $keahlianNama) {
                $fileInputName = 'sertifikasi_file.' . Str::slug($keahlianNama, '_');
                $sertifikasiPath = null;

                if ($request->hasFile($fileInputName)) {
                    $file = $request->file($fileInputName);
                    $sertifikasiPath = $file->store('sertifikasi_keahlian/' . $user->id, 'public');
                } else {
                    $pivot = $user->keahlian->firstWhere('keahlian_nama', $keahlianNama)?->pivot;
                    $sertifikasiPath = $pivot?->sertifikasi;
                }

                $keahlianSyncData[$keahlianNama] = ['sertifikasi' => $sertifikasiPath];
            }

            $user->keahlian()->sync($keahlianSyncData);

            // --- Handle Minat (pivot) ---
            $selectedMinat = $request->input('minat_pilihan', []);
            $user->minat()->sync($selectedMinat);

            // --- Handle Pengalaman ---
            $user->pengalaman()->delete();
            if ($request->has('pengalaman_items')) {
                $pengalamanToInsert = [];
                foreach ($request->pengalaman_items as $item) {
                    if (!empty($item['pengalaman_nama'])) {
                        $pengalamanToInsert[] = [
                            'user_id' => $user->user_id,
                            'pengalaman_nama' => $item['pengalaman_nama'],
                            'pengalaman_kategori' => $item['pengalaman_kategori'] ?? null,
                        ];
                    }
                }
                if (!empty($pengalamanToInsert)) {
                    PengalamanModel::insert($pengalamanToInsert);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update profil AJAX: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui profil. Terjadi kesalahan server.', 'error' => $e->getMessage()], 500);
        }
    }
}
