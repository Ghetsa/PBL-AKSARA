<div class="modal-header">
    <h5 class="modal-title">Edit Keahlian Saya</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="formEditKeahlianUser" enctype="multipart/form-data">
    <div class="modal-body">
        @csrf
        @method('PUT')

        {{-- Nama User (readonly) --}}
        <div class="mb-3">
            <label class="form-label">Nama User</label>
            <input type="text" class="form-control" value="{{ auth()->user()->nama }}" disabled>
        </div>

        {{-- Keahlian --}}
        <div class="mb-3">
            <label for="keahlian_id" class="form-label">Keahlian</label>
            <select name="keahlian_id" id="keahlian_id" class="form-select" required>
                <option value="">-- Pilih Keahlian --</option>
                @foreach ($keahlian as $item)
                    <option value="{{ $item->keahlian_id }}" {{ $data->keahlian_id == $item->keahlian_id ? 'selected' : '' }}>
                        {{ $item->keahlian_nama }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Sertifikasi --}}
        <div class="mb-3">
            <label for="sertifikasi" class="form-label">Sertifikasi (Opsional)</label>
            <input type="file" name="sertifikasi" id="sertifikasi" class="form-control" accept="pdf, jpg, jpeg, png">
                <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Max: 2MB.</small>
            @if ($data->sertifikasi)
                <div class="mt-2">
                <small class="form-text text-muted mt-1 d-block">File saat ini:
                    <a href="{{ asset('storage/' . $data->sertifikasi) }}" target="_blank" style="">
                        Lihat Sertifikasi Lama
                    </a>
                    Kosongkan jika tidak ingin mengubah.
                    </small>
                </div>
            @endif
                <span class="invalid-feedback error-text" id="error-sertifikasi"></span>
        </div>
        
         {{-- Catatan Verifikasi --}}
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
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                Swal.fire('Berhasil', res.message, 'success');
                $('#myModal').modal('hide');
                $('#dataKeahlianUser').DataTable().ajax.reload();
            },
            error: function (xhr) {
                let errMsg = 'Gagal memperbarui data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error', errMsg, 'error');
                console.error(xhr.responseText);
            }
        });
    });

</script>