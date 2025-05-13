<form id="formUpdateProfile" method="POST" action="{{ route('profile.update_ajax') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title">Ubah Profil</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </button>
            </div>

            <div class="modal-body">
                <!-- Nama -->
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" value="{{ $user->nama }}" required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>

                <!-- Role Tertampil (tidak bisa diedit) -->
                <div class="form-group">
                    <label>Role</label>
                    <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                </div>

                @if ($user->role === 'dosen')
                    <!-- NIP (tidak bisa diedit) -->
                    <div class="form-group">
                        <label>NIP</label>
                        <input type="text" class="form-control" value="{{ $user->dosen->nip }}" disabled>
                    </div>

                    <!-- Gelar -->
                    <div class="form-group">
                        <label>Gelar</label>
                        <input type="text" name="gelar" class="form-control" value="{{ $user->dosen->gelar }}">
                    </div>

                    <!-- No HP -->
                    <div class="form-group">
                        <label>No. HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ $user->dosen->no_hp }}">
                    </div>
                @elseif($user->role === 'mahasiswa')
                    <!-- NIM (tidak bisa diedit) -->
                    <div class="form-group">
                        <label>NIM</label>
                        <input type="text" class="form-control" value="{{ $user->mahasiswa->nim }}" disabled>
                    </div>

                    <!-- Program Studi -->
                    <div class="form-group">
                        <label>Program Studi</label>
                        <input type="text" class="form-control" value="{{ $user->mahasiswa->prodi->nama ?? '-' }}"
                            disabled>
                    </div>

                    <!-- Periode -->
                    <div class="form-group">
                        <label>Periode</label>
                        <input type="text" class="form-control"
                            value="{{ $user->mahasiswa->periode->tahun_akademik ?? '-' }}" disabled>
                    </div>
                @elseif($user->role === 'admin')
                    <!-- NIP Admin (tidak bisa diedit) -->
                    <div class="form-group">
                        <label>NIP</label>
                        <input type="text" class="form-control" value="{{ $user->admin->nip }}" disabled>
                    </div>
                @endif

                <!-- Minat -->
                <div class="form-group">
                    <label>Minat</label>
                    <input type="text" name="minat" class="form-control"
                        value="{{ $user->minat->first()->minat ?? '' }}">
                </div>

                <!-- Pengalaman -->
                <div class="form-group">
                    <label>Pengalaman</label>
                    <textarea name="pengalaman" class="form-control" rows="2">{{ $user->pengalaman->first()->pengalaman_nama ?? '' }}</textarea>
                </div>

                <!-- Prestasi (khusus mahasiswa) -->
                @if ($user->role === 'mahasiswa')
                    <div class="form-group">
                        <label>Prestasi</label>
                        <textarea name="prestasi" class="form-control" rows="2">{{ $user->mahasiswa->prestasi->first()->nama_prestasi ?? '' }}</textarea>
                    </div>
                @endif

                <!-- Keahlian -->
                <div class="form-group">
                    <label>Keahlian</label>
                    <select name="keahlian_id[]" class="form-control" multiple>
                        @foreach ($keahlianList as $keahlian)
                            <option value="{{ $keahlian->id }}"
                                {{ in_array($keahlian->id, $selectedKeahlianIds ?? []) ? 'selected' : '' }}>
                                {{ $keahlian->keahlian_nama }}
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
