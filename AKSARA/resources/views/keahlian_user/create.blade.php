<div class="modal-header">
    <h5 class="modal-title">Tambah Keahlian Saya</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="formKeahlianUser" action="{{ route('keahlian_user.store') }}" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nama User</label>
            <input type="text" class="form-control" value="{{ auth()->user()->nama }}" disabled>
        </div>

        <div class="mb-3">
            <label for="bidang_id" class="form-label">Pilih Bidang Keahlian <span class="text-danger">*</span></label>
            <select name="bidang_id" id="bidang_id" class="form-select" required>
                <option value="">-- Pilih Bidang Keahlian --</option>
                @foreach($bidang as $b)
                    <option value="{{ $b->bidang_id }}">{{ $b->bidang_nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="nama_sertifikat" class="form-label">Nama Sertifikat/Keahlian</label>
            <input type="text" name="nama_sertifikat" id="nama_sertifikat" class="form-control" placeholder="Cth: Web Development Expert, Sertifikat TOEFL">
        </div>

        <div class="mb-3">
            <label for="lembaga_sertifikasi" class="form-label">Lembaga Penerbit</label>
            <input type="text" name="lembaga_sertifikasi" id="lembaga_sertifikasi" class="form-control" placeholder="Cth: Google, Dicoding, Universitas">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="tanggal_perolehan_sertifikat" class="form-label">Tanggal Perolehan</label>
                <input type="date" name="tanggal_perolehan_sertifikat" id="tanggal_perolehan_sertifikat" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label for="tanggal_kadaluarsa_sertifikat" class="form-label">Tanggal Kadaluarsa</label>
                <input type="date" name="tanggal_kadaluarsa_sertifikat" id="tanggal_kadaluarsa_sertifikat" class="form-control">
            </div>
        </div>
        
        <div class="mb-3">
            <label for="sertifikasi" class="form-label">Upload File Bukti/Sertifikat</label>
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
                $('button[type="submit"]').attr('disabled', false).text('Simpan');
            }
        });
    });
</script>