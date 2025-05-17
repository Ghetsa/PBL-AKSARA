<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserModel;
use Illuminate\Support\Facades\Storage;
use App\Models\MinatModel;
use App\Models\KeahlianModel;
use App\Models\PengalamanModel;
// use App\Models\PrestasiModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ProfilController extends Controller
{
    public function index()
    {
        $activeMenu = "profil";
        $breadcrumb = (object) [
            'title' => 'Profil Pengguna',
            'list' => ['Dashboard', 'Profil Saya']
        ];
        $user = Auth::user();
        $loadRelations = ['keahlian', 'minat', 'pengalaman']; // Relasi umum

        if ($user->role === 'admin') {
            $loadRelations[] = 'admin';
        } elseif ($user->role === 'dosen') {
            $loadRelations[] = 'dosen';
        } elseif ($user->role === 'mahasiswa') {
            $loadRelations = array_merge($loadRelations, ['mahasiswa.prodi', 'mahasiswa.periode', 'mahasiswa.prestasi']);
        }
        $user->load($loadRelations);

        return view('profil.index', compact('user', 'activeMenu', 'breadcrumb'));
    }

    public function edit_ajax()
    {
        $user = Auth::user()->load(['keahlian', 'minat', 'pengalaman', 'admin', 'dosen', 'mahasiswa.prodi', 'mahasiswa.periode', 'mahasiswa.prestasi']);

        $allKeahlianOptions = KeahlianModel::orderBy('keahlian_nama')->get(); // Ambil semua dari master
        $allMinatOptions = MinatModel::orderBy('minat_nama')->get();       // Ambil semua dari master

        // Keahlian yang sudah dimiliki user beserta data pivotnya
        $userKeahlian = $user->keahlian->mapWithKeys(function ($item) {
            return [$item->keahlian_id => [ // Gunakan keahlian_id sebagai key
                'nama' => $item->keahlian_nama,
                'sertifikasi' => $item->pivot->sertifikasi,
                'sertifikasi_url' => $item->pivot->sertifikasi && Storage::disk('public')->exists($item->pivot->sertifikasi) ? Storage::url($item->pivot->sertifikasi) : null,
                'status_verifikasi' => $item->pivot->sertifikasi_status_verifikasi,
                'catatan_verifikasi' => $item->pivot->sertifikasi_catatan_verifikasi,
            ]];
        });

        // Minat yang sudah dimiliki user (hanya ID)
        $userMinatIds = $user->minat->pluck('minat_id')->toArray();

        $selectedPengalaman = $user->pengalaman; // Tetap sama
        $selectedPrestasi = ($user->role === 'mahasiswa' && $user->mahasiswa) ? $user->mahasiswa->prestasi : collect();

        return view('profil.edit', compact(
            'user',
            'allKeahlianOptions',
            'allMinatOptions',
            'userKeahlian', // Kirim data keahlian user yang sudah diproses
            'userMinatIds',
            'selectedPengalaman',
            'selectedPrestasi'
        ));
    }

    public function update_ajax(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction();

        try {
            $validationRules = [
                'nama' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Pastikan nama input di form adalah 'foto'
                // Keahlian dan Minat akan divalidasi sebagai array ID
                'keahlian_id' => 'nullable|array',
                'keahlian_id.*' => 'integer|exists:keahlian,keahlian_id',
                'minat_id' => 'nullable|array',
                'minat_id.*' => 'integer|exists:minat,minat_id',
                // Validasi untuk file sertifikasi (array of files, key adalah keahlian_id)
                'sertifikasi_file' => 'nullable|array',
                'sertifikasi_file.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Max 2MB per file
            ];
            if ($user->role === 'dosen') {
                $validationRules['gelar'] = 'nullable|string|max:100';
                $validationRules['no_hp'] = 'nullable|string|max:20';
            }

            $validator = Validator::make($request->all(), $validationRules, [
                'sertifikasi_file.*.mimes' => 'File sertifikasi harus berupa: pdf, jpg, jpeg, png.',
                'sertifikasi_file.*.max' => 'Ukuran file sertifikasi maksimal 2MB.',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
            }

            $userData = $request->only(['nama', 'email']);
            if ($request->hasFile('foto')) {
                if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                    Storage::disk('public')->delete($user->foto);
                }
                $path = $request->file('foto')->store('profile_photos/' . $user->user_id, 'public');
                $userData['foto'] = $path;
            }
            $user->update($userData);

            if ($user->role === 'dosen' && $user->dosen) {
                $user->dosen->update($request->only(['gelar', 'no_hp']));
            }

            // --- Handle Keahlian dengan file sertifikasi & verifikasi ---
            $selectedKeahlianIds = $request->input('keahlian_id', []);
            $dataToSyncKeahlian = [];
            $userKeahlianSaatIni = $user->keahlian->keyBy('keahlian_id'); // Untuk cek sertifikat lama

            foreach ($selectedKeahlianIds as $keahlianId) {
                $pivotData = [
                    'sertifikasi' => $userKeahlianSaatIni[$keahlianId]->pivot->sertifikasi ?? null,
                    'sertifikasi_status_verifikasi' => $userKeahlianSaatIni[$keahlianId]->pivot->sertifikasi_status_verifikasi ?? 'pending',
                    'sertifikasi_catatan_verifikasi' => $userKeahlianSaatIni[$keahlianId]->pivot->sertifikasi_catatan_verifikasi ?? null,
                ];

                if ($request->hasFile("sertifikasi_file.{$keahlianId}")) {
                    $file = $request->file("sertifikasi_file.{$keahlianId}");
                    // Hapus file sertifikat lama jika ada
                    if ($pivotData['sertifikasi'] && Storage::disk('public')->exists($pivotData['sertifikasi'])) {
                        Storage::disk('public')->delete($pivotData['sertifikasi']);
                    }
                    // Simpan file baru
                    $keahlianNamaSlug = Str::slug(KeahlianModel::find($keahlianId)->keahlian_nama);
                    $fileName = $user->user_id . '_' . $keahlianId . '_' . $keahlianNamaSlug . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $pivotData['sertifikasi'] = $file->storeAs('sertifikasi_keahlian/' . $user->user_id, $fileName, 'public');
                    $pivotData['sertifikasi_status_verifikasi'] = 'pending'; // Status jadi pending untuk file baru/diubah
                    $pivotData['sertifikasi_catatan_verifikasi'] = null;   // Hapus catatan lama
                } elseif (!$userKeahlianSaatIni->has($keahlianId) || !$userKeahlianSaatIni[$keahlianId]->pivot->sertifikasi) {
                    // Keahlian baru dipilih tanpa file, atau keahlian lama tanpa file tetap dipilih
                    $pivotData['sertifikasi'] = null;
                    $pivotData['sertifikasi_status_verifikasi'] = 'pending'; // Atau null jika tanpa file tidak perlu verifikasi
                }
                // Jika tidak ada file baru dan sudah ada file lama, pivotData sudah berisi data lama.

                $dataToSyncKeahlian[$keahlianId] = $pivotData;
            }

            // Hapus file sertifikat untuk keahlian yang tidak dipilih lagi
            foreach ($userKeahlianSaatIni as $id => $keahlian) {
                if (!in_array($id, $selectedKeahlianIds) && $keahlian->pivot->sertifikasi && Storage::disk('public')->exists($keahlian->pivot->sertifikasi)) {
                    Storage::disk('public')->delete($keahlian->pivot->sertifikasi);
                }
            }
            $user->keahlian()->sync($dataToSyncKeahlian);


            // --- Handle Minat ---
            $selectedMinatIds = $request->input('minat_id', []);
            $user->minat()->sync($selectedMinatIds);


            // --- Handle Pengalaman (tetap sama seperti sebelumnya) ---
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

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Validation\Rule;
// use App\Models\UserModel;
// use App\Models\MinatModel;
// use App\Models\KeahlianModel;
// use App\Models\PengalamanModel;

// class ProfilController extends Controller
// {
//     public function index()
//     {
//         $activeMenu = "";
//         $breadcrumb = (object) [
//             'title' => 'Profil pengguna',
//             'list' => ['Dashboard', 'Profil']
//         ];

//         $user = Auth::user();

//         if ($user->role === 'admin') {
//             $user->load('admin');
//         } elseif ($user->role === 'dosen') {
//             $user->load([
//                 'dosen',
//                 'keahlian',
//                 'pengalaman',
//                 'minat'
//             ]);
//         } elseif ($user->role === 'mahasiswa') {
//             $user->load([
//                 'mahasiswa.prodi',
//                 'mahasiswa.periode',
//                 'keahlian',
//                 'pengalaman',
//                 'minat',
//                 'mahasiswa.prestasi'
//             ]);
//         }

//         return view('profil.index', compact('user', 'activeMenu', 'breadcrumb'));
//     }

//     public function edit_ajax()
//     {
//         $user = Auth::user()->load([
//             'mahasiswa.prodi',
//             'mahasiswa.periode',
//             'dosen',
//             'admin',
//             'keahlian',
//             'minat',
//             'pengalaman'
//         ]);

//         $allKeahlianOptions = KeahlianModel::PILIHAN_KEAHLIAN;
//         $allMinatOptions = MinatModel::PILIHAN_MINAT;

//         $selectedKeahlian = $user->keahlian->mapWithKeys(function ($item) {
//             return [$item->keahlian_nama => $item->pivot->sertifikasi ?? null];
//         });

//         $selectedMinat = $user->minat->pluck('nama_minat')->toArray();

//         $selectedPengalaman = $user->pengalaman;
//         $selectedPrestasi = ($user->role === 'mahasiswa' && $user->mahasiswa) ? $user->mahasiswa->prestasi : collect();

//         return view('profil.edit', compact(
//             'user',
//             'allKeahlianOptions',
//             'allMinatOptions',
//             'selectedKeahlian',
//             'selectedMinat',
//             'selectedPengalaman',
//             'selectedPrestasi'
//         ));
//     }

//     public function update_ajax(Request $request)
//     {
//         $user = Auth::user();
//         DB::beginTransaction();

//         try {
//             // Validasi
//             $rules = [
//                 'nama' => 'required|string|max:255',
//                 'email' => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
//                 'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

//                 // keahlian sebagai array keahlian_id
//                 'keahlian_id' => 'nullable|array',
//                 'keahlian_id.*' => ['integer', Rule::exists('keahlian', 'keahlian_id')],

//                 // sertifikasi keahlian optional, array dengan key keahlian_nama
//                 'keahlian_items' => 'nullable|array',
//                 'keahlian_items.*.nama' => 'required_with:keahlian_items|string|max:255',
//                 'keahlian_items.*.sertifikasi' => 'nullable|string|max:255',

//                 'minat_pilihan' => 'nullable|array',
//                 'minat_pilihan.*' => ['string', Rule::in(MinatModel::PILIHAN_MINAT)],

//                 'sertifikasi_file' => 'nullable|array',
//                 'sertifikasi_file.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

//                 'pengalaman_items' => 'nullable|array',
//                 'pengalaman_items.*.pengalaman_nama' => 'nullable|string|max:255',
//                 'pengalaman_items.*.pengalaman_kategori' => 'nullable|string|max:255',
//             ];

//             if ($user->role === 'dosen') {
//                 $rules['gelar'] = 'nullable|string|max:100';
//                 $rules['no_hp'] = 'nullable|string|max:20';
//             }

//             $validator = Validator::make($request->all(), $rules);

//             if ($validator->fails()) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Validasi gagal.',
//                     'errors' => $validator->errors()
//                 ], 422);
//             }

//             // Update data dasar user
//             $userData = [
//                 'nama' => $request->nama,
//                 'email' => $request->email,
//             ];

//             // Update foto profil
//             if ($request->hasFile('foto')) {
//                 if ($user->foto && Storage::disk('public')->exists($user->foto)) {
//                     Storage::disk('public')->delete($user->foto);
//                 }
//                 $file = $request->file('foto');
//                 $filename = 'foto_' . time() . '.' . $file->getClientOriginalExtension();
//                 $path = $file->storeAs('profile_photos/' . $user->user_id, $filename, 'public');
//                 $userData['foto'] = $path;
//             }

//             $user->update($userData);

//             // Update dosen data jika role = dosen
//             if ($user->role === 'dosen' && $user->dosen) {
//                 $user->dosen->update($request->only(['gelar', 'no_hp']));
//             }

//             // --- Handle keahlian ---
//         $selectedKeahlianIds = $request->input('keahlian_id', []);
//         $keahlianSyncData = [];

//         foreach ($selectedKeahlianIds as $keahlianId) {
//             $keahlian = \App\Models\KeahlianModel::find($keahlianId);
//             if (!$keahlian) continue;

//             $keahlianSlug = Str::slug($keahlian->keahlian_nama, '_');
//             $sertifikasiPath = null;

//             if ($request->hasFile('sertifikasi_file') && isset($request->file('sertifikasi_file')[$keahlianSlug])) {
//                 $file = $request->file('sertifikasi_file')[$keahlianSlug];
//                 if ($file->isValid()) {
//                     $sertifikasiPath = $file->store("sertifikasi_keahlian/{$user->user_id}", 'public');
//                 }
//             } else {
//                 $pivot = $user->keahlian->firstWhere('keahlian_id', $keahlianId)?->pivot;
//                 $sertifikasiPath = $pivot?->sertifikasi;
//             }

//             $keahlianSyncData[$keahlianId] = ['sertifikasi' => $sertifikasiPath];
//         }

//         $user->keahlian()->sync($keahlianSyncData);

//         // --- Handle minat ---
//         $selectedMinatIds = $request->input('minat_id', []);
//         $user->minat()->sync($selectedMinatIds);
//             // --- Handle Pengalaman ---
//             $user->pengalaman()->delete();

//             $pengalamanItems = $request->input('pengalaman_items', []);
//             $pengalamanToInsert = [];

//             foreach ($pengalamanItems as $item) {
//                 if (!empty($item['pengalaman_nama'])) {
//                     $pengalamanToInsert[] = [
//                         'user_id' => $user->user_id,
//                         'pengalaman_nama' => $item['pengalaman_nama'],
//                         'pengalaman_kategori' => $item['pengalaman_kategori'] ?? null,
//                     ];
//                 }
//             }

//             if (!empty($pengalamanToInsert)) {
//                 PengalamanModel::insert($pengalamanToInsert);
//             }

//             DB::commit();

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Profil berhasil diperbarui.'
//             ]);
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error('Error update profil AJAX: ' . $e->getMessage(), [
//                 'stack' => $e->getTraceAsString()
//             ]);

//             return response()->json([
//                 'success' => false,
//                 'message' => 'Terjadi kesalahan server.',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }
// }
