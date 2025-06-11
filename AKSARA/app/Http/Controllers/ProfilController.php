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
            'minat',        // Ensure this is loaded for interests
            'pengalaman',   // Ensure this is loaded for experience
        ]);

        // Retrieve all bidangs (fields)
        $allBidangOptions = BidangModel::orderBy('bidang_nama')->get();

        // Collect user data for keahlian (skills) and minat (interests)
        $userKeahlian = $user->keahlian->mapWithKeys(function ($bidang) {
            return [$bidang->bidang_id => [
                'nama' => $bidang->bidang_nama,
                'sertifikasi' => $bidang->detailKeahlian->sertifikasi,
                'sertifikasi_url' => $bidang->detailKeahlian->sertifikasi && Storage::disk('public')->exists($bidang->detailKeahlian->sertifikasi) ? Storage::url($bidang->detailKeahlian->sertifikasi) : null,
                'status_verifikasi' => $bidang->detailKeahlian->status_verifikasi,
                'catatan_verifikasi' => $bidang->detailKeahlian->catatan_verifikasi,
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
            // Aturan validasi
            $validationRules = [
                'nama' => 'sometimes|string|max:50',
                'email' => [
                    'sometimes',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($user->user_id, 'user_id'),
                ],
                'no_telepon' => 'nullable|string|max:15|regex:/^[0-9\-\+\(\)\s]*$/', // Validasi nomor telepon sederhana
                'alamat' => 'nullable|string|max:100',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'minat_pilihan' => 'nullable|array',
                'minat_pilihan.*' => 'integer|exists:bidang,bidang_id',
                'sertifikasi_file' => 'nullable|array',
                'sertifikasi_file.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'minat_level' => 'nullable|array',
                'minat_level.*' => ['nullable', Rule::in(array_keys(MinatUserModel::LEVEL_MINAT))],
            ];

            $validator = Validator::make($request->all(), $validationRules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
            }

            // Update data dasar (nama dan email)
            $userData = [];
            if ($request->filled('nama')) {
                $userData['nama'] = $request->nama;
            }
            if ($request->filled('email')) {
                $userData['email'] = $request->email;
            }
            if ($request->filled('no_telepon')) {
                $userData['no_telepon'] = $request->no_telepon;
            }
            if ($request->filled('alamat')) {
                $userData['alamat'] = $request->alamat;
            }

            // Update foto profil
            if ($request->hasFile('foto')) {
                if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                    Storage::disk('public')->delete($user->foto);
                }
                $file = $request->file('foto');
                $filename = 'foto_' . $user->user_id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_photos/' . $user->user_id, $filename, 'public');
                $userData['foto'] = $path;
            }

            // Simpan data dasar jika ada yang berubah
            if (!empty($userData)) {
                $user->update($userData);
            }

            // Handle Minat - Perbarui atau buat baru jika ada perubahan
            $selectedBidangIdsForMinat = $request->input('minat_pilihan', []);
            $minatLevels = $request->input('minat_level', []);
            $dataToSyncMinat = [];

            foreach ($selectedBidangIdsForMinat as $bidangId) {
                $dataToSyncMinat[$bidangId] = ['level' => $minatLevels[$bidangId] ?? 'minat'];
            }

            // Update minat dengan sync (mengupdate data yang sudah ada tanpa menghapus yang tidak berubah)
            $user->minat()->sync($dataToSyncMinat);

            // Handle Pengalaman - Perbarui atau buat baru jika ada perubahan
            if ($request->has('pengalaman_items') && is_array($request->input('pengalaman_items'))) {
                foreach ($request->pengalaman_items as $item) {
                    if (!empty($item['pengalaman_nama'])) {
                        $pengalamanData = [
                            'user_id' => $user->user_id,
                            'pengalaman_nama' => $item['pengalaman_nama'],
                            'pengalaman_kategori' => $item['pengalaman_kategori'] ?? null,
                        ];

                        // Update atau buat pengalaman baru jika tidak ada pengalaman dengan ID yang sama
                        PengalamanModel::updateOrCreate(
                            ['user_id' => $user->user_id, 'pengalaman_nama' => $item['pengalaman_nama']], // Kondisi pencarian berdasarkan nama pengalaman
                            $pengalamanData
                        );
                    }
                }
            }

            // Commit transaction
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui.', 'redirect' => route('profile.index')]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
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
