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
        $breadcrumb = (object) [
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
            // Validasi
            $rules = [
                'nama' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

                // keahlian sebagai array keahlian_id
                'keahlian_id' => 'nullable|array',
                'keahlian_id.*' => ['integer', Rule::exists('keahlian', 'keahlian_id')],

                // sertifikasi keahlian optional, array dengan key keahlian_nama
                'keahlian_items' => 'nullable|array',
                'keahlian_items.*.nama' => 'required_with:keahlian_items|string|max:255',
                'keahlian_items.*.sertifikasi' => 'nullable|string|max:255',

                'minat_pilihan' => 'nullable|array',
                'minat_pilihan.*' => ['string', Rule::in(MinatModel::PILIHAN_MINAT)],

                'sertifikasi_file' => 'nullable|array',
                'sertifikasi_file.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

                'pengalaman_items' => 'nullable|array',
                'pengalaman_items.*.pengalaman_nama' => 'nullable|string|max:255',
                'pengalaman_items.*.pengalaman_kategori' => 'nullable|string|max:255',
            ];

            if ($user->role === 'dosen') {
                $rules['gelar'] = 'nullable|string|max:100';
                $rules['no_hp'] = 'nullable|string|max:20';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update data dasar user
            $userData = [
                'nama' => $request->nama,
                'email' => $request->email,
            ];

            // Update foto profil
            if ($request->hasFile('foto')) {
                if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                    Storage::disk('public')->delete($user->foto);
                }
                $file = $request->file('foto');
                $filename = 'foto_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_photos/' . $user->user_id, $filename, 'public');
                $userData['foto'] = $path;
            }

            $user->update($userData);

            // Update dosen data jika role = dosen
            if ($user->role === 'dosen' && $user->dosen) {
                $user->dosen->update($request->only(['gelar', 'no_hp']));
            }

            // --- Handle keahlian ---
        $selectedKeahlianIds = $request->input('keahlian_id', []);
        $keahlianSyncData = [];

        foreach ($selectedKeahlianIds as $keahlianId) {
            $keahlian = \App\Models\KeahlianModel::find($keahlianId);
            if (!$keahlian) continue;

            $keahlianSlug = Str::slug($keahlian->keahlian_nama, '_');
            $sertifikasiPath = null;

            if ($request->hasFile('sertifikasi_file') && isset($request->file('sertifikasi_file')[$keahlianSlug])) {
                $file = $request->file('sertifikasi_file')[$keahlianSlug];
                if ($file->isValid()) {
                    $sertifikasiPath = $file->store("sertifikasi_keahlian/{$user->user_id}", 'public');
                }
            } else {
                $pivot = $user->keahlian->firstWhere('keahlian_id', $keahlianId)?->pivot;
                $sertifikasiPath = $pivot?->sertifikasi;
            }

            $keahlianSyncData[$keahlianId] = ['sertifikasi' => $sertifikasiPath];
        }

        $user->keahlian()->sync($keahlianSyncData);

        // --- Handle minat ---
        $selectedMinatIds = $request->input('minat_id', []);
        $user->minat()->sync($selectedMinatIds);

            // --- Handle Pengalaman ---
            $user->pengalaman()->delete();

            $pengalamanItems = $request->input('pengalaman_items', []);
            $pengalamanToInsert = [];

            foreach ($pengalamanItems as $item) {
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

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update profil AJAX: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
