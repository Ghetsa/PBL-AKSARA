<div class="modal-header">
    <h5 class="modal-title">Tambah Keahlian Saya</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="formKeahlianUser" action="{{ route('keahlian_user.store') }}" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
        @csrf

        {{-- Nama User (readonly) --}}
        <div class="mb-3">
            <label class="form-label">Nama User</label>
            <input type="text" class="form-control" value="{{ auth()->user()->nama }}" disabled>
        </div>

        {{-- Keahlian --}}
        <div class="mb-3">
            <label for="keahlian_id" class="form-label">Pilih Keahlian</label>
            <select name="keahlian_id" id="keahlian_id" class="form-select" required>
                <option value="">-- Pilih Keahlian --</option>
                @foreach($keahlianList as $keahlian)
                    <option value="{{ $keahlian->keahlian_id }}">{{ $keahlian->keahlian_nama }}</option>
                @endforeach
            </select>
        </div>

        {{-- Sertifikasi --}}
        <div class="mb-3">
            <label for="sertifikasi" class="form-label">Upload Sertifikasi (Opsional)</label>
            <input type="file" name="sertifikasi" id="sertifikasi" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            <small class="form-text text-muted">Format: PDF/JPG/PNG. Max 2MB.</small>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    $('#formKeahlianUser').on('submit', function (e) {
        e.preventDefault();

        let form = $(this)[0];
        let formData = new FormData(form);

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('button[type="submit"]').attr('disabled', true).text('Menyimpan...');
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
                let msg = xhr.responseJSON?.message ?? 'Terjadi kesalahan.';
                Swal.fire('Gagal', msg, 'error');
            },
            complete: function () {
                $('button[type="submit"]').attr('disabled', false).text('Simpan');
            }
        });
    });
</script>
