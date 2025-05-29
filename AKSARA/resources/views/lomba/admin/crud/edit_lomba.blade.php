{{-- Form untuk Edit Lomba oleh Admin --}}
<form id="formAdminEditLomba" action="{{ route('admin.lomba.crud.update_ajax', $lomba->lomba_id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">Edit Informasi Lomba (Admin)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
        {{-- Field-field sama seperti create_form_ajax, tapi dengan value dari $lomba --}}
        {{-- Contoh untuk Nama Lomba --}}
        <div class="row mb-3">
            <label for="crud_edit_nama_lomba" class="col-sm-3 col-form-label text-sm-end">Nama Lomba <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" name="nama_lomba" id="crud_edit_nama_lomba" class="form-control" value="{{ old('nama_lomba', $lomba->nama_lomba) }}" required>
                <span class="invalid-feedback error-nama_lomba"></span>
            </div>
        </div>
        {{-- ... (lanjutkan semua field seperti di form create, isi value dengan $lomba->nama_field) ... --}}

        <div class="row mb-3">
            <label for="crud_edit_pembukaan_pendaftaran" class="col-sm-3 col-form-label text-sm-end">Pembukaan Pendaftaran <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="date" name="pembukaan_pendaftaran" id="crud_edit_pembukaan_pendaftaran" class="form-control" value="{{ old('pembukaan_pendaftaran', $lomba->pembukaan_pendaftaran ? $lomba->pembukaan_pendaftaran->format('Y-m-d') : '') }}" required>
                <span class="invalid-feedback error-pembukaan_pendaftaran"></span>
            </div>
        </div>
        <div class="row mb-3">
            <label for="crud_edit_batas_pendaftaran" class="col-sm-3 col-form-label text-sm-end">Batas Pendaftaran <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="date" name="batas_pendaftaran" id="crud_edit_batas_pendaftaran" class="form-control" value="{{ old('batas_pendaftaran', $lomba->batas_pendaftaran ? $lomba->batas_pendaftaran->format('Y-m-d') : '') }}" required>
                <span class="invalid-feedback error-batas_pendaftaran"></span>
            </div>
        </div>
        <div class="row mb-3">
            <label for="crud_edit_kategori" class="col-sm-3 col-form-label text-sm-end">Kategori <span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <select name="kategori" id="crud_edit_kategori" class="form-select" required>
                    <option value="individu" {{ old('kategori', $lomba->kategori) == 'individu' ? 'selected' : '' }}>Individu</option>
                    <option value="kelompok" {{ old('kategori', $lomba->kategori) == 'kelompok' ? 'selected' : '' }}>Kelompok</option>
                </select>
                <span class="invalid-feedback error-kategori"></span>
            </div>
             <label for="crud_edit_tingkat" class="col-sm-3 col-form-label text-sm-end">Tingkat <span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <select name="tingkat" id="crud_edit_tingkat" class="form-select" required>
                    <option value="lokal" {{ old('tingkat', $lomba->tingkat) == 'lokal' ? 'selected' : '' }}>Lokal</option>
                    <option value="nasional" {{ old('tingkat', $lomba->tingkat) == 'nasional' ? 'selected' : '' }}>Nasional</option>
                    <option value="internasional" {{ old('tingkat', $lomba->tingkat) == 'internasional' ? 'selected' : '' }}>Internasional</option>
                </select>
                <span class="invalid-feedback error-tingkat"></span>
            </div>
        </div>
        <div class="row mb-3">
            <label for="crud_edit_penyelenggara" class="col-sm-3 col-form-label text-sm-end">Penyelenggara <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" name="penyelenggara" id="crud_edit_penyelenggara" class="form-control" value="{{ old('penyelenggara', $lomba->penyelenggara) }}" required>
                 <span class="invalid-feedback error-penyelenggara"></span>
            </div>
        </div>
        <div class="row mb-3">
            <label for="crud_edit_bidang_keahlian" class="col-sm-3 col-form-label text-sm-end">Bidang Keahlian <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" name="bidang_keahlian" id="crud_edit_bidang_keahlian" class="form-control" value="{{ old('bidang_keahlian', $lomba->bidang_keahlian) }}" required>
                <span class="invalid-feedback error-bidang_keahlian"></span>
            </div>
        </div>
        <div class="row mb-3">
            <label for="crud_edit_biaya" class="col-sm-3 col-form-label text-sm-end">Biaya (Rp)</label>
            <div class="col-sm-9">
                <input type="number" name="biaya" id="crud_edit_biaya" class="form-control" value="{{ old('biaya', $lomba->biaya) }}" min="0">
                <span class="invalid-feedback error-biaya"></span>
            </div>
        </div>
        <div class="row mb-3">
            <label for="crud_edit_link_pendaftaran" class="col-sm-3 col-form-label text-sm-end">Link Pendaftaran</label>
            <div class="col-sm-9">
                <input type="url" name="link_pendaftaran" id="crud_edit_link_pendaftaran" class="form-control" value="{{ old('link_pendaftaran', $lomba->link_pendaftaran) }}">
                 <span class="invalid-feedback error-link_pendaftaran"></span>
            </div>
        </div>
         <div class="row mb-3">
            <label for="crud_edit_link_penyelenggara" class="col-sm-3 col-form-label text-sm-end">Link Penyelenggara</label>
            <div class="col-sm-9">
                <input type="url" name="link_penyelenggara" id="crud_edit_link_penyelenggara" class="form-control" value="{{ old('link_penyelenggara', $lomba->link_penyelenggara) }}">
                <span class="invalid-feedback error-link_penyelenggara"></span>
            </div>
        </div>
        <div class="row mb-3">
            <label for="crud_edit_poster" class="col-sm-3 col-form-label text-sm-end">Poster Lomba</label>
            <div class="col-sm-9">
                <input type="file" name="poster" id="crud_edit_poster" class="form-control" accept="image/*">
                @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
                    <small class="form-text text-muted mt-1 d-block">Poster saat ini: <a href="{{ asset('storage/'.$lomba->poster) }}" target="_blank">Lihat Poster</a>. Kosongkan jika tidak ingin mengubah.</small>
                @endif
                 <span class="invalid-feedback error-poster"></span>
            </div>
        </div>
        <div class="row mb-3">
            <label for="crud_edit_status_verifikasi" class="col-sm-3 col-form-label text-sm-end">Status Verifikasi <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <select name="status_verifikasi" id="crud_edit_status_verifikasi" class="form-select" required>
                    <option value="pending" {{ old('status_verifikasi', $lomba->status_verifikasi) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="disetujui" {{ old('status_verifikasi', $lomba->status_verifikasi) == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ old('status_verifikasi', $lomba->status_verifikasi) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <span class="invalid-feedback error-status_verifikasi"></span>
            </div>
        </div>
        <div class="row mb-3" id="crud_edit_catatan_verifikasi_wrapper" style="{{ ($lomba->status_verifikasi == 'ditolak' || old('status_verifikasi') == 'ditolak') ? '' : 'display:none;' }}">
            <label for="crud_edit_catatan_verifikasi" class="col-sm-3 col-form-label text-sm-end">Catatan Verifikasi</label>
            <div class="col-sm-9">
                <textarea name="catatan_verifikasi" id="crud_edit_catatan_verifikasi" class="form-control" rows="2">{{ old('catatan_verifikasi', $lomba->catatan_verifikasi ?? '') }}</textarea>
                <small class="form-text text-muted">Wajib diisi jika status "Ditolak".</small>
                <span class="invalid-feedback error-catatan_verifikasi"></span>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>
<script>
$(document).ready(function() {
    const formAdminEditLomba = $('#formAdminEditLomba');
    const statusSelectEdit = $('#crud_edit_status_verifikasi');
    const catatanWrapperEdit = $('#crud_edit_catatan_verifikasi_wrapper');
    const catatanTextareaEdit = $('#crud_edit_catatan_verifikasi');

    function toggleCatatanEdit() {
        if (statusSelectEdit.val() === 'ditolak') {
            catatanWrapperEdit.slideDown();
            // catatanTextareaEdit.prop('required', true); // Optional: validasi required via JS
        } else {
            catatanWrapperEdit.slideUp();
            // catatanTextareaEdit.prop('required', false);
        }
    }
    statusSelectEdit.on('change', toggleCatatanEdit);
    toggleCatatanEdit(); // Panggil saat load

    formAdminEditLomba.validate({
        // ... rules dan messages ...
        rules: {
            // ... (rules sama seperti form create)
            catatan_verifikasi: {
                // required: function() { return statusSelectEdit.val() === 'ditolak'; }, // jQuery validate dinamis
                maxlength: 1000
            }
        },
        // ... (messages, errorPlacement, highlight, unhighlight)
        submitHandler: function(form) {
            let formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');
            const originalButtonText = submitButton.html();
            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
            
            $.ajax({
                url: $(form).attr('action'), method: 'POST', data: formData,
                processData: false, contentType: false, dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        $('#modalFormLombaAdminCrud').modal('hide');
                        Swal.fire('Berhasil!', response.message, 'success');
                        if (typeof dtLombaCrudAdmin !== 'undefined') { dtLombaCrudAdmin.ajax.reload(null, false); }
                    } else {
                        Swal.fire('Gagal!', response.message || 'Gagal menyimpan data.', 'error');
                    }
                },
                error: function(xhr) { /* ... */ },
                complete: function() { submitButton.prop('disabled', false).html(originalButtonText); }
            });
        }
    });
});
</script>