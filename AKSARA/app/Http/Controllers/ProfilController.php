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

        if ($user->role === 'dosen') {
            $user->load([
                'dosen',
                'pengalaman',
                'keahlianUser.bidang',
                'minatUser.bidang',
            ]);
        } elseif ($user->role === 'mahasiswa') {
            $user->load([
                'mahasiswa.prodi',
                'mahasiswa.periode',
                'mahasiswa.prestasi',
                'pengalaman',
                'keahlianUser.bidang',
                'minatUser.bidang',
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
            'keahlianUser.bidang',
            'minatUser.bidang',
            'pengalaman',
        ]);

        $allBidangOptions = BidangModel::orderBy('bidang_nama')->get();
        $selectedMinat = $user->minatUser->pluck('bidang_id')->toArray();
        $selectedPengalaman = $user->pengalaman;
        $selectedPrestasi = ($user->role === 'mahasiswa' && $user->mahasiswa) ? $user->mahasiswa->prestasi : collect();
        $keahlianUser = $user->keahlianUser;
        $minatUser = $user->minatUser;

        return view('profil.edit', compact(
            'user',
            'allBidangOptions',
            'selectedMinat',
            'selectedPengalaman',
            'selectedPrestasi',
            'keahlianUser',
            'minatUser'
        ));
    }

    public function update_ajax(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction();

        try {
            $rules = [
                'nama' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($user->user_id, 'user_id'),
                ],
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'keahlian_items' => 'nullable|array',
                'keahlian_items.*.nama' => 'required_with:keahlian_items|string|max:255',
                'keahlian_items.*.sertifikasi' => 'nullable|string|max:255',
                'minat_pilihan' => 'nullable|array',
                'minat_pilihan.*' => 'integer|exists:bidang,bidang_id',
                'sertifikasi_file' => 'nullable|array',
                'sertifikasi_file.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'pengalaman_items' => 'nullable|array',
                'pengalaman_items.*.pengalaman_nama' => 'nullable|string|max:255',
                'pengalaman_items.*.pengalaman_kategori' => 'nullable|string|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userData = [
                'nama' => $request->nama,
                'email' => $request->email,
            ];

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

            if ($user->role === 'dosen' && $user->dosen) {
                $user->dosen->update($request->only(['gelar', 'no_hp']));
            }

            // Update Minat
            $selectedMinatIds = $request->input('minat_pilihan', []);
            $user->minatUser()->sync($selectedMinatIds);

            // Update Pengalaman
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
                'message' => 'Profil berhasil diperbarui.',
                'redirect' => route('profile.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update profil AJAX: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui profil.'
            ], 500);
        }
    }
}
