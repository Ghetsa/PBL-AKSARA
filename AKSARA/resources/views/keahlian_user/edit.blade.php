<div class="modal-header">
    <h5 class="modal-title">Edit Keahlian Saya</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="formEditKeahlianUser" enctype="multipart/form-data">
    <div class="modal-body">
        @csrf
        {{-- @method('PUT') tidak diperlukan jika dikirim via formData.append('_method', 'PUT') di JS --}}

        <div class="mb-3">
            <label for="bidang_id" class="form-label">Bidang Keahlian <span class="text-danger">*</span></label>
            <select name="bidang_id" id="bidang_id" class="form-select" required>
                <option value="">-- Pilih Keahlian --</option>
                @foreach ($bidang as $item)
                    <option value="{{ $item->bidang_id }}" {{ $data->bidang_id == $item->bidang_id ? 'selected' : '' }}>
                        {{ $item->bidang_nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="nama_sertifikat" class="form-label">Nama Sertifikat/Keahlian</label>
            <input type="text" name="nama_sertifikat" id="nama_sertifikat" class="form-control" value="{{ old('nama_sertifikat', $data->nama_sertifikat) }}" placeholder="Cth: Web Development Expert">
        </div>

        <div class="mb-3">
            <label for="lembaga_sertifikasi" class="form-label">Lembaga Penerbit (Jika ada)</label>
            <input type="text" name="lembaga_sertifikasi" id="lembaga_sertifikasi" class="form-control" value="{{ old('lembaga_sertifikasi', $data->lembaga_sertifikasi) }}" placeholder="Cth: Google, Dicoding">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="tanggal_perolehan_sertifikat" class="form-label">Tanggal Perolehan (Jika ada)</label>
                <input type="date" name="tanggal_perolehan_sertifikat" id="tanggal_perolehan_sertifikat" class="form-control" value="{{ old('tanggal_perolehan_sertifikat', $data->tanggal_perolehan_sertifikat ? $data->tanggal_perolehan_sertifikat->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label for="tanggal_kadaluarsa_sertifikat" class="form-label">Tanggal Kadaluarsa (Jika ada)</label>
                <input type="date" name="tanggal_kadaluarsa_sertifikat" id="tanggal_kadaluarsa_sertifikat" class="form-control" value="{{ old('tanggal_kadaluarsa_sertifikat', $data->tanggal_kadaluarsa_sertifikat ? $data->tanggal_kadaluarsa_sertifikat->format('Y-m-d') : '') }}">
            </div>
        </div>

        <div class="mb-3">
            <label for="sertifikasi" class="form-label">Upload File Bukti/Sertifikat (Opsional)</label>
            <input type="file" name="sertifikasi" id="sertifikasi" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Max: 2MB. Kosongkan jika tidak ingin mengubah file.</small>
            @if ($data->sertifikasi)
                <div class="mt-2">
                    <small class="form-text text-muted mt-1 d-block">File saat ini:
                        <a href="{{ asset('storage/' . $data->sertifikasi) }}" target="_blank">
                            Lihat File Lama
                        </a>
                    </small>
                </div>
            @endif
        </div>
         
        @if($data->status_verifikasi == 'ditolak' && $data->catatan_verifikasi)
            <div class="alert alert-warning">
                <strong>Catatan Verifikasi Sebelumnya:</strong><br>
                {{ $data->catatan_verifikasi }}
            </div>
        @endif
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>

<script>
     $('#formEditKeahlianUser').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('_method', 'PUT'); 

        $.ajax({
            url: "{{ route('keahlian_user.update', $data->keahlian_user_id) }}",
            method: 'POST', 
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('button[type="submit"]').attr('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
            },
            success: function (res) {
                if (res.status) {
                    Swal.fire('Berhasil', res.message, 'success');
                    $('#myModal').modal('hide');
                    $('#dataKeahlianUser').DataTable().ajax.reload();
                } else {
                    Swal.fire('Gagal', res.message || 'Gagal menyimpan data.', 'error');
                }
            },
            error: function (xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessages = '';
                if (errors) {
                    $.each(errors, function(key, value) {
                        errorMessages += value[0] + '<br>';
                    });
                } else {
                    errorMessages = xhr.responseJSON.message || 'Terjadi kesalahan.';
                }
                Swal.fire('Gagal Validasi', errorMessages, 'error');
            },
            complete: function () {
                $('button[type="submit"]').attr('disabled', false).text('Simpan Perubahan');
            }
        });
    });
</script>