{{-- Jika $lomba ada, ini mode edit, jika tidak, mode create --}}
<form id="formAdminCrudLomba" action="{{ isset($lomba) ? route('admin.lomba.crud.update_ajax', $lomba->lomba_id) : route('admin.lomba.crud.store_ajax') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($lomba))
        @method('PUT')
    @endif

    <div class="modal-header bg-{{ isset($lomba) ? 'warning text-dark' : 'primary text-white' }}">
        <h5 class="modal-title">{{ isset($lomba) ? 'Edit Informasi Lomba (Admin)' : 'Tambah Info Lomba Baru (Admin)' }}</h5>
        <button type="button" class="btn-close {{ isset($lomba) ? '' : 'btn-close-white' }}" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
        {{-- Semua field form lomba seperti di lomba.user_submission_form_ajax.blade.php --}}
        {{-- Bedanya, jika admin, status_verifikasi bisa langsung diset 'disetujui' atau ada pilihan status --}}
        {{-- Pastikan value diisi jika $lomba ada (mode edit) --}}
        
        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="crud_nama_lomba" class="form-label">Nama Lomba <span class="text-danger">*</span></label>
                <input type="text" name="nama_lomba" id="crud_nama_lomba" class="form-control" value="{{ old('nama_lomba', $lomba->nama_lomba ?? '') }}" required>
                <span class="invalid-feedback error-nama_lomba"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="crud_pembukaan_pendaftaran" class="form-label">Pembukaan Pendaftaran <span class="text-danger">*</span></label>
                <input type="date" name="pembukaan_pendaftaran" id="crud_pembukaan_pendaftaran" class="form-control" value="{{ old('pembukaan_pendaftaran', isset($lomba->pembukaan_pendaftaran) ? $lomba->pembukaan_pendaftaran->format('Y-m-d') : '') }}" required>
                <span class="invalid-feedback error-pembukaan_pendaftaran"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="crud_batas_pendaftaran" class="form-label">Batas Pendaftaran <span class="text-danger">*</span></label>
                <input type="date" name="batas_pendaftaran" id="crud_batas_pendaftaran" class="form-control" value="{{ old('batas_pendaftaran', isset($lomba->batas_pendaftaran) ? $lomba->batas_pendaftaran->format('Y-m-d') : '') }}" required>
                <span class="invalid-feedback error-batas_pendaftaran"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="crud_kategori" class="form-label">Kategori Lomba <span class="text-danger">*</span></label>
                <select name="kategori" id="crud_kategori" class="form-select" required>
                    <option value="individu" {{ old('kategori', $lomba->kategori ?? '') == 'individu' ? 'selected' : '' }}>Individu</option>
                    <option value="kelompok" {{ old('kategori', $lomba->kategori ?? '') == 'kelompok' ? 'selected' : '' }}>Kelompok</option>
                </select>
                <span class="invalid-feedback error-kategori"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="crud_tingkat" class="form-label">Tingkat Lomba <span class="text-danger">*</span></label>
                <select name="tingkat" id="crud_tingkat" class="form-select" required>
                    <option value="lokal" {{ old('tingkat', $lomba->tingkat ?? '') == 'lokal' ? 'selected' : '' }}>Lokal/Daerah</option>
                    <option value="nasional" {{ old('tingkat', $lomba->tingkat ?? '') == 'nasional' ? 'selected' : '' }}>Nasional</option>
                    <option value="internasional" {{ old('tingkat', $lomba->tingkat ?? '') == 'internasional' ? 'selected' : '' }}>Internasional</option>
                </select>
                <span class="invalid-feedback error-tingkat"></span>
            </div>
            <div class="col-md-12 mb-3">
                <label for="crud_penyelenggara" class="form-label">Penyelenggara <span class="text-danger">*</span></label>
                <input type="text" name="penyelenggara" id="crud_penyelenggara" class="form-control" value="{{ old('penyelenggara', $lomba->penyelenggara ?? '') }}" required>
                <span class="invalid-feedback error-penyelenggara"></span>
            </div>
            <div class="col-md-12 mb-3">
                <label for="crud_bidang_keahlian" class="form-label">Bidang Keahlian Relevan <span class="text-danger">*</span></label>
                <input type="text" name="bidang_keahlian" id="crud_bidang_keahlian" class="form-control" value="{{ old('bidang_keahlian', $lomba->bidang_keahlian ?? '') }}" placeholder="Pisahkan dengan koma jika lebih dari satu" required>
                <span class="invalid-feedback error-bidang_keahlian"></span>
            </div>
            <div class="col-md-12 mb-3">
                <label for="crud_biaya" class="form-label">Biaya Pendaftaran (Kosongkan jika gratis)</label>
                <input type="number" name="biaya" id="crud_biaya" class="form-control" value="{{ old('biaya', $lomba->biaya ?? '') }}" min="0" placeholder="Contoh: 50000">
                <span class="invalid-feedback error-biaya"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="crud_link_pendaftaran" class="form-label">Link Pendaftaran (Opsional)</label>
                <input type="url" name="link_pendaftaran" id="crud_link_pendaftaran" class="form-control" value="{{ old('link_pendaftaran', $lomba->link_pendaftaran ?? '') }}" placeholder="https://contoh.com/daftar">
                <span class="invalid-feedback error-link_pendaftaran"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="crud_link_penyelenggara" class="form-label">Link Website Penyelenggara (Opsional)</label>
                <input type="url" name="link_penyelenggara" id="crud_link_penyelenggara" class="form-control" value="{{ old('link_penyelenggara', $lomba->link_penyelenggara ?? '') }}" placeholder="https://penyelenggara.com">
                <span class="invalid-feedback error-link_penyelenggara"></span>
            </div>
            <div class="col-md-12 mb-3">
                <label for="crud_poster" class="form-label">Poster Lomba (Opsional, Max 2MB)</label>
                <input type="file" name="poster" id="crud_poster" class="form-control" accept="image/jpeg,image/png,image/jpg">
                @if(isset($lomba) && $lomba->poster && Storage::disk('public')->exists($lomba->poster))
                    <small class="form-text text-muted mt-1 d-block">Poster saat ini: <a href="{{ asset('storage/'.$lomba->poster) }}" target="_blank">Lihat Poster</a>. Kosongkan jika tidak ingin mengubah.</small>
                @endif
                <span class="invalid-feedback error-poster"></span>
            </div>
            {{-- Admin bisa set status langsung --}}
            <div class="col-md-12 mb-3">
                <label for="crud_status_verifikasi" class="form-label">Status Verifikasi <span class="text-danger">*</span></label>
                <select name="status_verifikasi" id="crud_status_verifikasi" class="form-select" required>
                    <option value="disetujui" {{ old('status_verifikasi', $lomba->status_verifikasi ?? 'disetujui') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="pending" {{ old('status_verifikasi', $lomba->status_verifikasi ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="ditolak" {{ old('status_verifikasi', $lomba->status_verifikasi ?? '') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <span class="invalid-feedback error-status_verifikasi"></span>
            </div>
             @if(isset($lomba) && $lomba->status_verifikasi == 'ditolak')
            <div class="col-md-12 mb-3">
                <label for="crud_catatan_verifikasi" class="form-label">Catatan Verifikasi (Jika Ditolak)</label>
                <textarea name="catatan_verifikasi" id="crud_catatan_verifikasi" class="form-control" rows="2">{{ old('catatan_verifikasi', $lomba->catatan_verifikasi ?? '') }}</textarea>
                <span class="invalid-feedback error-catatan_verifikasi"></span>
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
    const formAdminCrudLomba = $('#formAdminCrudLomba');
    // Inisialisasi jQuery Validate (mirip dengan form lain, sesuaikan rules dan messages)
    formAdminCrudLomba.validate({
        // ... rules & messages ...
        rules: {
            nama_lomba: { required: true, maxlength: 255 },
            // ... rules lainnya ...
            status_verifikasi: {required: true}
        },
        submitHandler: function(form) {
            let formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');
            const originalButtonText = submitButton.html();
            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
            
            $.ajax({
                url: $(form).attr('action'),
                method: 'POST', data: formData, processData: false, contentType: false,
                success: function(response) {
                    if (response.status) {
                        $('#modalFormLombaAdminCrud').modal('hide'); // Sesuaikan ID modal
                        Swal.fire('Berhasil', response.message, 'success');
                        if (typeof dtLombaCrudAdmin !== 'undefined') { dtLombaCrudAdmin.ajax.reload(null, false); }
                    } else {
                        Swal.fire('Gagal', response.message || 'Gagal menyimpan data.', 'error');
                    }
                },
                error: function(xhr) { /* ... error handling ... */ },
                complete: function() { submitButton.prop('disabled', false).html(originalButtonText); }
            });
        }
        // ... errorPlacement, highlight, unhighlight ...
    });
});
</script>