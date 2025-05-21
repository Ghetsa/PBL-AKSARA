<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

use App\Models\UserModel;
use App\Models\BidangModel;
use App\Models\PengalamanModel;
use App\Models\MinatUserModel; // Anda mungkin tidak perlu ini lagi jika relasi User ke Bidang (minat) sudah cukup

class ProfilController extends Controller
{
    public function index()
    {
        $activeMenu = "profil"; // Definisikan variabel $activeMenu
        $breadcrumb = (object) [
            'title' => 'Profil pengguna',
            'list' => ['Dashboard', 'Profil Saya']
        ];

        $user = Auth::user();
        $relationsToLoad = ['pengalaman']; // Relasi umum

        if ($user->role === 'dosen') {
            $relationsToLoad = array_merge($relationsToLoad, ['dosen', 'keahlian', 'minat']); // Gunakan 'keahlian' dan 'minat'
        } elseif ($user->role === 'mahasiswa') {
            $relationsToLoad = array_merge($relationsToLoad, [
                'mahasiswa.prodi',
                'mahasiswa.periode',
                'mahasiswa.prestasi', // Jika masih ada relasi ini di MahasiswaModel
                'keahlian',         // Gunakan 'keahlian'
                'minat'             // Gunakan 'minat'
            ]);
        } elseif ($user->role === 'admin') { // Jangan lupa handle role admin jika perlu
            $relationsToLoad[] = 'admin';
        }
        // Hanya load jika ada relasi yang didefinisikan
        if (!empty($relationsToLoad)) {
            $user->load($relationsToLoad);
        }

        return view('profil.index', compact('user', 'activeMenu', 'breadcrumb'));
    }

    public function edit_ajax()
    {
        $user = Auth::user()->load([
            'mahasiswa.prodi',
            'mahasiswa.periode',
            // 'mahasiswa.prestasi', // Jika prestasi tidak diedit di sini
            'dosen',
            'admin',
            'keahlian',         // <--- UBAH INI
            'minat',            // <--- UBAH INI
            'pengalaman',
        ]);

        $allBidangOptions = BidangModel::orderBy('bidang_nama')->get();

        // Mengambil data pivot dari relasi 'keahlian'
        $userKeahlian = $user->keahlian->mapWithKeys(function ($bidang) {
            return [$bidang->bidang_id => [
                'nama' => $bidang->bidang_nama,
                'sertifikasi' => $bidang->detailKeahlian->sertifikasi, // Menggunakan alias 'detailKeahlian'
                'sertifikasi_url' => $bidang->detailKeahlian->sertifikasi && Storage::disk('public')->exists($bidang->detailKeahlian->sertifikasi) ? Storage::url($bidang->detailKeahlian->sertifikasi) : null,
                'status_verifikasi' => $bidang->detailKeahlian->sertifikasi_status_verifikasi,
                'catatan_verifikasi' => $bidang->detailKeahlian->sertifikasi_catatan_verifikasi,
            ]];
        });

        // Mengambil data pivot dari relasi 'minat'
        $userMinatData = $user->minat->mapWithKeys(function ($bidang) {
            return [$bidang->bidang_id => [
                'nama' => $bidang->bidang_nama,
                'level' => $bidang->detailMinat->level ?? null // Menggunakan alias 'detailMinat'
            ]];
        });
        $userMinatIds = $user->minat->pluck('bidang_id')->toArray();


        $selectedPengalaman = $user->pengalaman;
        $minatLevelOptions = MinatUserModel::LEVEL_MINAT; // Pastikan MinatUserModel punya konstanta ini

        return view('profil.edit', compact(
            'user',
            'allBidangOptions',
            'userKeahlian',
            'userMinatData',
            'userMinatIds', // Ditambahkan untuk pre-check checkbox minat
            'minatLevelOptions',
            'selectedPengalaman'
        ));
    }

    public function update_ajax(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction();

        try {
            $validationRules = [
                'nama' => 'required|string|max:255',
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'keahlian_pilihan' => 'nullable|array', // Ini akan berisi bidang_id dari checkbox
                'keahlian_pilihan.*' => 'integer|exists:bidang,bidang_id',
                'minat_pilihan' => 'nullable|array',   // Ini akan berisi bidang_id dari checkbox
                'minat_pilihan.*' => 'integer|exists:bidang,bidang_id',
                'sertifikasi_file' => 'nullable|array',
                'sertifikasi_file.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'minat_level' => 'nullable|array',
                'minat_level.*' => ['nullable', Rule::in(array_keys(MinatUserModel::LEVEL_MINAT))],
            ];

            if ($user->role === 'dosen') {
                $validationRules['gelar'] = 'nullable|string|max:100';
                $validationRules['no_hp'] = 'nullable|string|max:20';
            }

            // Hanya validasi pengalaman_items jika ada input
            if ($request->has('pengalaman_items') && is_array($request->input('pengalaman_items'))) {
                foreach ($request->input('pengalaman_items') as $key => $value) {
                    // Hanya validasi jika nama pengalaman diisi (artinya item tersebut dianggap ada)
                    if (!empty($value['pengalaman_nama'])) {
                        $validationRules["pengalaman_items.{$key}.pengalaman_nama"] = 'required|string|max:255';
                        $validationRules["pengalaman_items.{$key}.pengalaman_kategori"] = 'nullable|string|max:255|in:Workshop,Magang,Proyek,Organisasi,Pekerjaan'; // Sesuaikan dengan ENUM atau pilihan Anda
                    } else {
                        // Jika nama kosong, kategori juga tidak wajib
                        $validationRules["pengalaman_items.{$key}.pengalaman_kategori"] = 'nullable|string|max:255|in:Workshop,Magang,Proyek,Organisasi,Pekerjaan';
                    }
                }
            }


            $validator = Validator::make($request->all(), $validationRules, [
                'sertifikasi_file.*.mimes' => 'File sertifikasi harus berupa: pdf, jpg, jpeg, png.',
                'sertifikasi_file.*.max' => 'Ukuran file sertifikasi maksimal 2MB.',
                'minat_level.*.in' => 'Level minat tidak valid.',
                'pengalaman_items.*.pengalaman_nama.required' => 'Nama pengalaman wajib diisi jika Anda menambahkan item pengalaman.',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
            }

            $userData = $request->only(['nama', 'email']);
            if ($request->hasFile('foto')) {
                if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                    Storage::disk('public')->delete($user->foto);
                }
                $file = $request->file('foto');
                $filename = 'foto_' . $user->user_id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_photos/' . $user->user_id, $filename, 'public');
                $userData['foto'] = $path;
            }
            $user->update($userData);

            if ($user->role === 'dosen' && $user->dosen) {
                $user->dosen->update($request->only(['gelar', 'no_hp']));
            }

            // --- Handle Keahlian ---
            $selectedBidangIdsForKeahlian = $request->input('keahlian_pilihan', []);
            $dataToSyncKeahlian = [];
            // Ambil keahlian user yang sudah ada untuk perbandingan file dan status
            $userKeahlianSaatIni = $user->keahlian()->get()->keyBy('bidang_id');


            foreach ($selectedBidangIdsForKeahlian as $bidangId) {
                $existingPivot = $userKeahlianSaatIni->get($bidangId)->detailKeahlian ?? null;
                $pivotData = [
                    'sertifikasi' => $existingPivot->sertifikasi ?? null,
                    'status_verifikasi' => $existingPivot->status_verifikasi ?? 'pending',
                    'catatan_verifikasi' => $existingPivot->catatan_verifikasi ?? null,
                ];

                if ($request->hasFile("sertifikasi_file.{$bidangId}")) {
                    $file = $request->file("sertifikasi_file.{$bidangId}");
                    if ($pivotData['sertifikasi'] && Storage::disk('public')->exists($pivotData['sertifikasi'])) {
                        Storage::disk('public')->delete($pivotData['sertifikasi']);
                    }
                    $bidangModel = BidangModel::find($bidangId);
                    $bidangNamaSlug = $bidangModel ? Str::slug($bidangModel->bidang_nama) : $bidangId;
                    $fileName = 'sertifikat_' . $user->user_id . '_' . $bidangId . '_' . $bidangNamaSlug . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $pivotData['sertifikasi'] = $file->storeAs('sertifikasi_keahlian/' . $user->user_id, $fileName, 'public');
                    $pivotData['status_verifikasi'] = 'pending';
                    $pivotData['catatan_verifikasi'] = null;
                } elseif (!$existingPivot || is_null($existingPivot->sertifikasi)) {
                    $pivotData['sertifikasi'] = null;
                    $pivotData['status_verifikasi'] = $existingPivot->status_verifikasi ?? 'pending';
                }
                $dataToSyncKeahlian[$bidangId] = $pivotData;
            }

            // Hapus file sertifikat untuk keahlian yang tidak dipilih lagi
            foreach ($userKeahlianSaatIni as $bidang_id_lama => $keahlianPivotLama) {
                if (!in_array($bidang_id_lama, $selectedBidangIdsForKeahlian)) {
                    if ($keahlianPivotLama->detailKeahlian->sertifikasi && Storage::disk('public')->exists($keahlianPivotLama->detailKeahlian->sertifikasi)) {
                        Storage::disk('public')->delete($keahlianPivotLama->detailKeahlian->sertifikasi);
                    }
                }
            }
            $user->keahlian()->sync($dataToSyncKeahlian);


            // --- Handle Minat ---
            $selectedBidangIdsForMinat = $request->input('minat_pilihan', []);
            $minatLevels = $request->input('minat_level', []);
            $dataToSyncMinat = [];
            foreach ($selectedBidangIdsForMinat as $bidangId) {
                $dataToSyncMinat[$bidangId] = ['level' => $minatLevels[$bidangId] ?? 'minat'];
            }
            $user->minat()->sync($dataToSyncMinat);


            // --- Handle Pengalaman ---
            $user->pengalaman()->delete();
            if ($request->has('pengalaman_items') && is_array($request->input('pengalaman_items'))) {
                $pengalamanToInsert = [];
                foreach ($request->pengalaman_items as $item) {
                    // Hanya proses jika nama pengalaman diisi
                    if (!empty($item['pengalaman_nama'])) {
                        $pengalamanToInsert[] = [
                            'user_id' => $user->user_id,
                            // 'bidang_id' => $item['bidang_id'] ?? null, // Sesuaikan jika pengalaman ada bidang_id
                            'pengalaman_nama' => $item['pengalaman_nama'],
                            'pengalaman_kategori' => $item['pengalaman_kategori'] ?? null,
                            'created_at' => now(), // Tambahkan manual jika model pengalaman tidak auto-timestamp
                            'updated_at' => now(),
                        ];
                    }
                }
                if (!empty($pengalamanToInsert)) {
                    PengalamanModel::insert($pengalamanToInsert);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui.', 'redirect' => route('profile.index')]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Kesalahan validasi update profil: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update profil AJAX: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui profil. Terjadi kesalahan server.'], 500);
        }
    }
}
