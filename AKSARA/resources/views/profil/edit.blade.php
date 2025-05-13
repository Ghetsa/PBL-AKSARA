<form id="formUpdateProfile" method="POST" action="{{ route('profile.update_ajax') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title">Ubah Profil</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Nama -->
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" value="{{ $user->nama }}" required>
                </div>

                <!-- Username -->
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                </div>

                <!-- NIM/NIP (tidak bisa diedit) -->
                <div class="form-group">
                    <label>{{ $user->role === 'mahasiswa' ? 'NIM' : 'NIP' }}</label>
                    <input type="text" class="form-control" value="{{ $user->nim ?? $user->nip }}" disabled>
                </div>

                <!-- Minat -->
                <div class="form-group">
                    <label>Minat</label>
                    <input type="text" name="minat" class="form-control" value="{{ $user->minat?->nama }}">
                </div>

                <!-- Pengalaman -->
                <div class="form-group">
                    <label>Pengalaman</label>
                    <textarea name="pengalaman" class="form-control" rows="3">{{ $user->pengalaman?->deskripsi }}</textarea>
                </div>

                <!-- Prestasi -->
                <div class="form-group">
                    <label>Prestasi</label>
                    <textarea name="prestasi" class="form-control" rows="3">{{ $user->prestasi?->deskripsi }}</textarea>
                </div>

                <!-- Keahlian -->
                <div class="form-group">
                    <label>Keahlian</label>
                    <select name="keahlian_id[]" class="form-control" multiple>
                        @foreach($keahlianList as $keahlian)
                            <option value="{{ $keahlian->id }}"
                                {{ in_array($keahlian->id, $selectedKeahlianIds ?? []) ? 'selected' : '' }}>
                                {{ $keahlian->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Foto Profil -->
                <div class="form-group">
                    <label>Foto Profil</label>
                    <input type="file" name="profile_photo" class="form-control-file">
                </div>
            </div>

            <div class="modal-footer justify-content-end">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $('#formUpdateProfile').submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                toastr.success('Profil berhasil diperbarui');
                $('#updateProfileModal').modal('hide');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function(err) {
                toastr.error('Gagal memperbarui profil');
            }
        });
    });
</script>
