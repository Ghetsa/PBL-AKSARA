<form id="formAdminLomba" action="{{ isset($lomba) ? route('admin.lomba.update', $lomba->lomba_id) : route('admin.lomba.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($lomba))
        @method('PUT')
    @endif

    <div class="modal-header bg-{{ isset($lomba) ? 'warning text-dark' : 'primary text-white' }}">
        <h5 class="modal-title">{{ isset($lomba) ? 'Edit Informasi Lomba' : 'Tambah Info Lomba Baru' }}</h5>
        <button type="button" class="btn-close {{ isset($lomba) ? '' : 'btn-close-white' }}" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="admin_nama_lomba" class="form-label">Nama Lomba <span class="text-danger">*</span></label>
                <input type="text" name="nama_lomba" id="admin_nama_lomba" class="form-control" value="{{ old('nama_lomba', $lomba->nama_lomba ?? '') }}" required>
                <span class="invalid-feedback error-nama_lomba"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="admin_pembukaan_pendaftaran" class="form-label">Pembukaan Pendaftaran <span class="text-danger">*</span></label>
                <input type="date" name="pembukaan_pendaftaran" id="admin_pembukaan_pendaftaran" class="form-control" value="{{ old('pembukaan_pendaftaran', isset($lomba->pembukaan_pendaftaran) ? $lomba->pembukaan_pendaftaran->format('Y-m-d') : '') }}" required>
                <span class="invalid-feedback error-pembukaan_pendaftaran"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="admin_batas_pendaftaran" class="form-label">Batas Pendaftaran <span class="text-danger">*</span></label>
                <input type="date" name="batas_pendaftaran" id="admin_batas_pendaftaran" class="form-control" value="{{ old('batas_pendaftaran', isset($lomba->batas_pendaftaran) ? $lomba->batas_pendaftaran->format('Y-m-d') : '') }}" required>
                <span class="invalid-feedback error-batas_pendaftaran"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="admin_kategori" class="form-label">Kategori Lomba <span class="text-danger">*</span></label>
                <select name="kategori" id="admin_kategori" class="form-select" required>
                    <option value="individu" {{ old('kategori', $lomba->kategori ?? '') == 'individu' ? 'selected' : '' }}>Individu</option>
                    <option value="kelompok" {{ old('kategori', $lomba->kategori ?? '') == 'kelompok' ? 'selected' : '' }}>Kelompok</option>
                </select>
                <span class="invalid-feedback error-kategori"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="admin_tingkat" class="form-label">Tingkat Lomba <span class="text-danger">*</span></label>
                <select name="tingkat" id="admin_tingkat" class="form-select" required>
                    <option value="lokal" {{ old('tingkat', $lomba->tingkat ?? '') == 'lokal' ? 'selected' : '' }}>Lokal/Daerah</option>
                    <option value="nasional" {{ old('tingkat', $lomba->tingkat ?? '') == 'nasional' ? 'selected' : '' }}>Nasional</option>
                    <option value="internasional" {{ old('tingkat', $lomba->tingkat ?? '') == 'internasional' ? 'selected' : '' }}>Internasional</option>
                </select>
                <span class="invalid-feedback error-tingkat"></span>
            </div>
             <div class="col-md-12 mb-3">
                <label for="admin_penyelenggara" class="form-label">Penyelenggara <span class="text-danger">*</span></label>
                <input type="text" name="penyelenggara" id="admin_penyelenggara" class="form-control" value="{{ old('penyelenggara', $lomba->penyelenggara ?? '') }}" required>
                <span class="invalid-feedback error-penyelenggara"></span>
            </div>
            <div class="col-md-12 mb-3">
                <label for="admin_bidang_keahlian" class="form-label">Bidang Keahlian Relevan <span class="text-danger">*</span></label>
                <input type="text" name="bidang_keahlian" id="admin_bidang_keahlian" class="form-control" value="{{ old('bidang_keahlian', $lomba->bidang_keahlian ?? '') }}" placeholder="Pisahkan dengan koma jika lebih dari satu" required>
                 <span class="invalid-feedback error-bidang_keahlian"></span>
            </div>
             <div class="col-md-12 mb-3">
                <label for="admin_biaya" class="form-label">Biaya Pendaftaran (Kosongkan jika gratis)</label>
                <input type="number" name="biaya" id="admin_biaya" class="form-control" value="{{ old('biaya', $lomba->biaya ?? '') }}" min="0" placeholder="Contoh: 50000">
                <span class="invalid-feedback error-biaya"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="admin_link_pendaftaran" class="form-label">Link Pendaftaran (Opsional)</label>
                <input type="url" name="link_pendaftaran" id="admin_link_pendaftaran" class="form-control" value="{{ old('link_pendaftaran', $lomba->link_pendaftaran ?? '') }}" placeholder="https://contoh.com/daftar">
                 <span class="invalid-feedback error-link_pendaftaran"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="admin_link_penyelenggara" class="form-label">Link Website Penyelenggara (Opsional)</label>
                <input type="url" name="link_penyelenggara" id="admin_link_penyelenggara" class="form-control" value="{{ old('link_penyelenggara', $lomba->link_penyelenggara ?? '') }}" placeholder="https://penyelenggara.com">
                <span class="invalid-feedback error-link_penyelenggara"></span>
            </div>
            <div class="col-md-12 mb-3">
                <label for="admin_poster" class="form-label">Poster Lomba (Opsional, Max 2MB)</label>
                <input type="file" name="poster" id="admin_poster" class="form-control" accept="image/jpeg,image/png,image/jpg">
                @if(isset($lomba) && $lomba->poster && Storage::disk('public')->exists($lomba->poster))
                    <small class="form-text text-muted mt-1 d-block">Poster saat ini: <a href="{{ asset('storage/'.$lomba->poster) }}" target="_blank">Lihat Poster</a>. Kosongkan jika tidak ingin mengubah.</small>
                @endif
                <span class="invalid-feedback error-poster"></span>
            </div>
            @if(isset($lomba)) {{-- Hanya tampilkan status verifikasi di form edit --}}
            <div class="col-md-12 mb-3">
                <label for="admin_status_verifikasi_edit" class="form-label">Status Verifikasi Lomba <span class="text-danger">*</span></label>
                <select name="status_verifikasi" id="admin_status_verifikasi_edit" class="form-select" required>
                    <option value="pending" {{ old('status_verifikasi', $lomba->status_verifikasi ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="disetujui" {{ old('status_verifikasi', $lomba->status_verifikasi ?? '') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ old('status_verifikasi', $lomba->status_verifikasi ?? '') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <span class="invalid-feedback error-status_verifikasi"></span>
            </div>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">{{ isset($lomba) ? 'Simpan Perubahan' : 'Tambah Lomba' }}</button>
    </div>
</form>
<script>
$(document).ready(function() {
    const formAdminLomba = $('#formAdminLomba');
    formAdminLomba.validate({
        // rules dan messages mirip dengan form pengajuan mahasiswa/dosen
        // Tambahkan rules untuk status_verifikasi jika di form edit
        rules: {
            nama_lomba: { required: true, maxlength: 255 },
            pembukaan_pendaftaran: { required: true, date: true },
            batas_pendaftaran: { required: true, date: true, afterDate: '#admin_pembukaan_pendaftaran' },
            kategori: { required: true },
            penyelenggara: { required: true, maxlength: 255 },
            tingkat: { required: true },
            bidang_keahlian: { required: true, maxlength: 255 },
            biaya: { number: true, min: 0 },
            link_pendaftaran: { url: true, maxlength: 255 },
            link_penyelenggara: { url: true, maxlength: 255 },
            poster: { extension: "jpg|jpeg|png", filesize: 2097152 /* 2MB */ },
            @if(isset($lomba))
            status_verifikasi: { required: true }
            @endif
        },
        messages: { /* sesuaikan pesan error */ 
            batas_pendaftaran: { afterDate: "Batas pendaftaran harus setelah tanggal pembukaan."}
        },
        errorElement: 'span',
        errorPlacement: function (error, element) { /* ... penempatan error Anda ... */ 
            error.addClass('invalid-feedback');
            element.closest('.mb-3').find('.invalid-feedback.error-' + element.attr('name')).html(error.html()).show();
            if (!element.closest('.mb-3').find('.invalid-feedback.error-' + element.attr('name')).length) {
                 element.closest('.mb-3').append(error);
            }
        },
        highlight: function (element) { $(element).addClass('is-invalid'); },
        unhighlight: function (element) { $(element).removeClass('is-invalid').addClass('is-valid'); },

        submitHandler: function(form) {
            let formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');
            const originalButtonText = submitButton.html();

            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
            $(form).find('.invalid-feedback').text('');
            $(form).find('.form-control, .form-select').removeClass('is-invalid is-valid');

            $.ajax({
                url: $(form).attr('action'),
                method: 'POST', // Laravel handle @method('PUT')
                data: formData,
                processData: false, contentType: false,
                success: function(response) {
                    if (response.status) {
                        $('#modalFormLombaAdmin').modal('hide');
                        Swal.fire('Berhasil', response.message, 'success');
                        if (typeof dataTableLombaAdmin !== 'undefined') { dataTableLombaAdmin.ajax.reload(null, false); }
                    } else {
                        Swal.fire('Gagal', response.message || 'Gagal menyimpan data.', 'error');
                    }
                },
                error: function(xhr) {
                     let errors = xhr.responseJSON.errors;
                     let errorMessages = '<ul>';
                    if (errors) {
                        $.each(errors, function(key, messages) {
                             errorMessages += `<li>${messages[0]}</li>`;
                            $('#admin_' + key).addClass('is-invalid').closest('.mb-3').find('.invalid-feedback.error-' + key).text(messages[0]).show();
                        });
                    }
                    errorMessages += '</ul>';
                    Swal.fire('Validasi Gagal', xhr.responseJSON.message + '<br>' + errorMessages , 'error');
                },
                complete: function() {
                    submitButton.prop('disabled', false).html(originalButtonText);
                }
            });
        }
    });
    // Custom rule untuk afterDate (jika jQuery validation tidak punya default)
    $.validator.addMethod("afterDate", function(value, element, params) {
        if (!/Invalid|NaN/.test(new Date(value))) {
            return new Date(value) > new Date($(params).val());
        }
        return isNaN(value) && isNaN($(params).val()) || (Number(value) > Number($(params).val()));
    },'Must be after start date.');
});
</script>