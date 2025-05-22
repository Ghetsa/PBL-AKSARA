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
use App\Models\MinatUserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    // ... (method index() dan edit_ajax() tetap sama seperti jawaban sebelumnya) ...
    public function index()
    {
        $activeMenu = "profil";
        $breadcrumb = (object) [
            'title' => 'Profil pengguna',
            'list' => ['Dashboard', 'Profil Saya']
        ];

        $user = Auth::user();
        $relationsToLoad = ['pengalaman'];

        if ($user->role === 'dosen') {
            $relationsToLoad = array_merge($relationsToLoad, ['dosen', 'keahlian', 'minat']);
        } elseif ($user->role === 'mahasiswa') {
            $relationsToLoad = array_merge($relationsToLoad, [
                'mahasiswa.prodi',
                'mahasiswa.periode',
                'mahasiswa.prestasi',
                'keahlian',
                'minat'
            ]);
        } elseif ($user->role === 'admin') {
            $relationsToLoad[] = 'admin';
        }
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
            'dosen',
            'admin',
            'keahlian',
            'minat',
            'pengalaman',
        ]);

        $allBidangOptions = BidangModel::orderBy('bidang_nama')->get();

        $userKeahlian = $user->keahlian->mapWithKeys(function ($bidang) {
            return [$bidang->bidang_id => [
                'nama' => $bidang->bidang_nama,
                'sertifikasi' => $bidang->detailKeahlian->sertifikasi,
                'sertifikasi_url' => $bidang->detailKeahlian->sertifikasi && Storage::disk('public')->exists($bidang->detailKeahlian->sertifikasi) ? Storage::url($bidang->detailKeahlian->sertifikasi) : null,
                'status_verifikasi' => $bidang->detailKeahlian->sertifikasi_status_verifikasi,
                'catatan_verifikasi' => $bidang->detailKeahlian->sertifikasi_catatan_verifikasi,
            ]];
        });

        $userMinatData = $user->minat->mapWithKeys(function ($bidang) {
            return [$bidang->bidang_id => [
                'nama' => $bidang->bidang_nama,
                'level' => $bidang->detailMinat->level ?? null
            ]];
        });
        $userMinatIds = $user->minat->pluck('bidang_id')->toArray();

        $selectedPengalaman = $user->pengalaman;
        $minatLevelOptions = MinatUserModel::LEVEL_MINAT;

        return view('profil.edit', compact(
            'user',
            'allBidangOptions',
            'userKeahlian',
            'userMinatData',
            'userMinatIds',
            'minatLevelOptions',
            'selectedPengalaman'
        ));
    }

    public function update_ajax(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction();

        try {
            // Aturan validasi diubah: 'nama' dan 'email' tidak lagi 'required' secara default
            // Mereka akan diupdate hanya jika ada nilainya di request.
            $validationRules = [
                'nama' => 'sometimes|string|max:255', // 'sometimes' berarti validasi jika ada
                'email' => [
                    'sometimes', // Validasi jika ada
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($user->user_id, 'user_id'),
                ],
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'keahlian_pilihan' => 'nullable|array',
                'keahlian_pilihan.*' => 'integer|exists:bidang,bidang_id',
                'minat_pilihan' => 'nullable|array',
                'minat_pilihan.*' => 'integer|exists:bidang,bidang_id',
                'sertifikasi_file' => 'nullable|array',
                'sertifikasi_file.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'minat_level' => 'nullable|array',
                'minat_level.*' => ['nullable', Rule::in(array_keys(MinatUserModel::LEVEL_MINAT))],
            ];

            if ($user->role === 'dosen') {
                // Untuk dosen, gelar dan no_hp juga opsional jika tidak ingin diubah
                $validationRules['gelar'] = 'sometimes|nullable|string|max:100';
                $validationRules['no_hp'] = 'sometimes|nullable|string|max:20';
            }

            if ($request->has('pengalaman_items') && is_array($request->input('pengalaman_items'))) {
                foreach ($request->input('pengalaman_items') as $key => $value) {
                    if (!empty($value['pengalaman_nama']) || !empty($value['pengalaman_kategori'])) { // Validasi jika salah satu field item pengalaman diisi
                        $validationRules["pengalaman_items.{$key}.pengalaman_nama"] = 'required|string|max:255';
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

            // Hanya ambil data yang ada di request untuk update user dasar
            $userData = [];
            if ($request->filled('nama')) {
                $userData['nama'] = $request->nama;
            }
            if ($request->filled('email')) {
                $userData['email'] = $request->email;
            }

            if ($request->hasFile('foto')) {
                if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                    Storage::disk('public')->delete($user->foto);
                }
                $file = $request->file('foto');
                $filename = 'foto_' . $user->user_id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_photos/' . $user->user_id, $filename, 'public');
                $userData['foto'] = $path;
            }

            // Hanya update jika ada data yang diubah
            if (!empty($userData)) {
                $user->update($userData);
            }

            if ($user->role === 'dosen' && $user->dosen) {
                $dosenDataToUpdate = [];
                if ($request->filled('gelar')) {
                    $dosenDataToUpdate['gelar'] = $request->gelar;
                }
                if ($request->filled('no_hp')) {
                    $dosenDataToUpdate['no_hp'] = $request->no_hp;
                }
                if (!empty($dosenDataToUpdate)) {
                    $user->dosen->update($dosenDataToUpdate);
                }
            }

            // --- Handle Keahlian ---
            // (Logika keahlian tetap sama, karena ini tentang memilih/tidak memilih dan mengunggah file)
            // Jika checkbox tidak dipilih, relasi akan dihapus oleh sync. Jika dipilih, data pivot akan diupdate/dibuat.
            $selectedBidangIdsForKeahlian = $request->input('keahlian_pilihan', []);
            $dataToSyncKeahlian = [];
            $userKeahlianSaatIni = $user->keahlian()->get()->keyBy('bidang_id');

            foreach ($allBidangOptions = BidangModel::all() as $bidang) { // Iterasi semua master bidang
                $bidangId = $bidang->bidang_id;
                if (in_array($bidangId, $selectedBidangIdsForKeahlian)) {
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
                        $bidangNamaSlug = Str::slug($bidang->bidang_nama);
                        $fileName = 'sertifikat_' . $user->user_id . '_' . $bidangId . '_' . $bidangNamaSlug . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $pivotData['sertifikasi'] = $file->storeAs('sertifikasi_keahlian/' . $user->user_id, $fileName, 'public');
                        $pivotData['status_verifikasi'] = 'pending';
                        $pivotData['catatan_verifikasi'] = null;
                    } elseif (!$existingPivot || is_null($existingPivot->sertifikasi)) {
                        $pivotData['sertifikasi'] = null;
                        $pivotData['status_verifikasi'] = $existingPivot->status_verifikasi ?? 'pending';
                    }
                    $dataToSyncKeahlian[$bidangId] = $pivotData;
                } else {
                    if (isset($userKeahlianSaatIni[$bidangId]) && $userKeahlianSaatIni[$bidangId]->detailKeahlian->sertifikasi && Storage::disk('public')->exists($userKeahlianSaatIni[$bidangId]->detailKeahlian->sertifikasi)) {
                        Storage::disk('public')->delete($userKeahlianSaatIni[$bidangId]->detailKeahlian->sertifikasi);
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
                    if (!empty($item['pengalaman_nama'])) {
                        $pengalamanToInsert[] = [
                            'user_id' => $user->user_id,
                            'pengalaman_nama' => $item['pengalaman_nama'],
                            'pengalaman_kategori' => $item['pengalaman_kategori'] ?? null,
                            'created_at' => now(),
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

    public function showChangePasswordFormAjax()
    {
        return view('profil.change_password');
    }

    public function updatePasswordAjax(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Password lama tidak sesuai.');
                }
            }],
            'new_password' => [
                'required',
                'string',
                Password::min(6) // Minimal 6 karakter
                    ->mixedCase()    // Harus ada huruf besar dan kecil
                    ->numbers()      // Harus ada angka
                    ->symbols(),     // Harus ada simbol
                'confirmed'
            ],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal  karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'new_password.mixed_case' => 'Password baru harus mengandung setidaknya satu huruf besar dan satu huruf kecil.',
            'new_password.numbers' => 'Password baru harus mengandung setidakny6a satu angka.',
            'new_password.symbols' => 'Password baru harus mengandung setidaknya satu simbol (contoh: !@#$%^&*).',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        try {
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Opsional: Logout user dari session lain jika diinginkan setelah ganti password
            // Auth::logoutOtherDevices($request->current_password);

            return response()->json(['success' => true, 'message' => 'Password berhasil diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Error update password AJAX: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui password. Terjadi kesalahan server.'], 500);
        }
    }
}
