{{-- profil/edit.blade.php --}}
<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <form id="formUpdateProfile" method="POST" action="{{ route('profile.update_ajax') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title">Perbarui Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">

                {{-- Bagian Info Dasar --}}
                <div id="section-dasar">
                    <h5><i class="ti ti-id me-2"></i>Info Dasar</h5>
                    <hr class="mt-1 mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="edit_nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                                <span class="invalid-feedback error-nama"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="edit_email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                 <span class="invalid-feedback error-email"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_foto" class="form-label">Foto Profil</label>
                        <input type="file" name="foto" id="edit_foto" class="form-control">
                        @if($user->foto && Storage::disk('public')->exists($user->foto))
                            <small class="form-text text-muted mt-1">
                                Foto saat ini: <a href="{{ Storage::url($user->foto) }}" target="_blank">lihat foto</a>.
                                <br>Kosongkan jika tidak ingin mengubah.
                            </small>
                        @elseif($user->foto)
                             <small class="form-text text-danger mt-1">File foto profil sebelumnya tidak ditemukan.</small>
                        @endif
                        <span class="invalid-feedback error-foto"></span>
                    </div>
                    @if ($user->role === 'dosen' && $user->dosen)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="gelar" class="form-label">Gelar</label>
                                    <input type="text" name="gelar" id="gelar" class="form-control"
                                        value="{{ old('gelar', $user->dosen->gelar ?? '') }}">
                                    <span class="invalid-feedback error-gelar"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="no_hp" class="form-label">No. HP</label>
                                    <input type="text" name="no_hp" id="no_hp" class="form-control"
                                        value="{{ old('no_hp', $user->dosen->no_hp ?? '') }}">
                                    <span class="invalid-feedback error-no_hp"></span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <hr class="my-4">

                {{-- Bagian Minat --}}
                @if ($user->role === 'dosen' || $user->role === 'mahasiswa')
                    <div id="section-minat" class="mt-4">
                        <h5><i class="ti ti-heart me-2"></i>Minat</h5>
                        <p class="text-muted">Pilih bidang yang Anda minati.</p>
                        <hr class="mt-1 mb-3">
                        <div class="row">
                            @foreach ($allMinatOptions as $minatOption)
                                @php
                                    $isChecked = $user->bidang->contains('bidang_id', $minatOption->bidang_id);
                                    $minatSlug = \Illuminate\Support\Str::slug($minatOption->bidang_nama, '_');
                                @endphp
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="bidang_id[]"
                                            value="{{ $minatOption->bidang_id }}" id="bidang_{{ $minatSlug }}" {{ $isChecked ? 'checked' : '' }}>
                                        <label class="form-check-label" for="minat_{{ $minatSlug }}">
                                            {{ $minatOption->bidang_nama }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <hr class="my-4">
                @endif

                {{-- Bagian Pengalaman --}}
                @if ($user->role === 'mahasiswa')
                    <div id="section-pengalaman" class="mt-4">
                        <h5><i class="ti ti-briefcase me-2"></i>Pengalaman</h5>
                        <hr class="mt-1 mb-3">
                        <div id="pengalaman-fields-container">
                            @forelse ($selectedPengalaman as $index => $pengalaman)
                                <div class="pengalaman-item border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-sm btn-danger remove-item-btn mb-2">Hapus
                                            Pengalaman Ini</button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Nama Pengalaman/Posisi</label>
                                            <input type="text" name="pengalaman_items[{{ $index }}][pengalaman_nama]"
                                                class="form-control" value="{{ $pengalaman->pengalaman_nama }}">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Kategori</label>
                                            <select name="pengalaman_items[{{ $index }}][pengalaman_kategori]"
                                                class="form-control">
                                                <option value="">-- Pilih Kategori --</option>
                                                <option value="Workshop" {{ $pengalaman->pengalaman_kategori == 'Workshop' ? 'selected' : '' }}>Workshop</option>
                                                <option value="Magang" {{ $pengalaman->pengalaman_kategori == 'Magang' ? 'selected' : '' }}>Magang</option>
                                                <option value="Proyek" {{ $pengalaman->pengalaman_kategori == 'Proyek' ? 'selected' : '' }}>Proyek</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="pengalaman-item border rounded p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Nama Pengalaman</label>
                                            <input type="text" name="pengalaman_items[0][pengalaman_nama]" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Kategori</label>
                                            <select name="pengalaman_items[0][pengalaman_kategori]" class="form-control">
                                                <option value="">-- Pilih Kategori --</option>
                                                <option value="Workshop">Workshop</option>
                                                <option value="Magang">Magang</option>
                                                <option value="Proyek">Proyek</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" id="add-pengalaman-btn" class="btn btn-sm btn-outline-primary mt-2">Tambah
                            Pengalaman</button>
                    </div>
                    <hr class="my-4">
                @endif

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('#formUpdateProfile').on('submit', function (e) {
            e.preventDefault();

            let form = $(this)[0];
            let formData = new FormData(form);

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    // Tutup modal terlebih dahulu
                    let modalElement = $(form).closest('.modal');
                    modalElement.modal('hide');

                    // Setelah animasi modal selesai (tunggu 300ms), tampilkan SweetAlert
                    setTimeout(function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect;
                        });
                    }, 300);
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $('.invalid-feedback').text('').hide();
                        $('input, select, textarea').removeClass('is-invalid');

                        $.each(errors, function (key, messages) {
                            const name = key.replace(/\./g, '_');
                            const input = $(`[name="${key}"], [name="${key}[]"]`);
                            input.addClass('is-invalid');
                            $(`.error-${name}`).text(messages[0]).show();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan. Silakan coba lagi.'
                        });
                    }
                }
            });
        });
    });
</script>