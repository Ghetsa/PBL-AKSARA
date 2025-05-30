<form id="formAdminEditLomba" action="{{ route('admin.lomba.crud.update_ajax', $lomba->lomba_id) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="modal-header">
        <h5 class="modal-title">Edit Informasi Lomba</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">
        {{-- Nama Lomba --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Nama Lomba <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" name="nama_lomba" class="form-control"
                    value="{{ old('nama_lomba', $lomba->nama_lomba) }}" required>
                <span class="invalid-feedback error-nama_lomba"></span>
            </div>
        </div>

        {{-- Tanggal Pendaftaran --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Pembukaan Pendaftaran <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="date" name="pembukaan_pendaftaran" class="form-control"
                    value="{{ old('pembukaan_pendaftaran', optional($lomba->pembukaan_pendaftaran)->format('Y-m-d')) }}"
                    required>
                <span class="invalid-feedback error-pembukaan_pendaftaran"></span>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Batas Pendaftaran <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="date" name="batas_pendaftaran" class="form-control"
                    value="{{ old('batas_pendaftaran', optional($lomba->batas_pendaftaran)->format('Y-m-d')) }}"
                    required>
                <span class="invalid-feedback error-batas_pendaftaran"></span>
            </div>
        </div>

        {{-- Kategori & Tingkat --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Kategori <span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <select name="kategori" class="form-select" required>
                    <option value="individu" {{ old('kategori', $lomba->kategori) == 'individu' ? 'selected' : '' }}>
                        Individu</option>
                    <option value="kelompok" {{ old('kategori', $lomba->kategori) == 'kelompok' ? 'selected' : '' }}>
                        Kelompok</option>
                </select>
                <span class="invalid-feedback error-kategori"></span>
            </div>

            <label class="col-sm-3 col-form-label text-end">Tingkat <span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <select name="tingkat" class="form-select" required>
                    <option value="lokal" {{ old('tingkat', $lomba->tingkat) == 'lokal' ? 'selected' : '' }}>Lokal
                    </option>
                    <option value="nasional" {{ old('tingkat', $lomba->tingkat) == 'nasional' ? 'selected' : '' }}>
                        Nasional</option>
                    <option value="internasional" {{ old('tingkat', $lomba->tingkat) == 'internasional' ? 'selected' : '' }}>Internasional</option>
                </select>
                <span class="invalid-feedback error-tingkat"></span>
            </div>
        </div>

        {{-- Penyelenggara --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Penyelenggara <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" name="penyelenggara" class="form-control"
                    value="{{ old('penyelenggara', $lomba->penyelenggara) }}" required>
                <span class="invalid-feedback error-penyelenggara"></span>
            </div>
        </div>

        {{-- Bidang --}}
        <div class="row mb-3">
            <h5><i class="ti ti-heart me-2"></i> Bidang</h5>
            <hr>
            <select name="bidang_id[]" class="form-control" multiple required>
                @foreach($allBidangOptions as $b)
                    <option value="{{ $b->bidang_id }}" {{ $lomba->detailBidang->contains('bidang_id', $b->bidang_id) ? 'selected' : '' }}>
                        {{ $b->nama_bidang }}
                    </option>
                @endforeach
            </select>
            <span class="invalid-feedback error-bidang_pilihan"></span>
        </div>

        {{-- Bidang Keahlian --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Bidang Keahlian <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" name="bidang_keahlian" class="form-control"
                    value="{{ old('bidang_keahlian', $lomba->bidang_keahlian) }}" required>
                <span class="invalid-feedback error-bidang_keahlian"></span>
            </div>
        </div>

        {{-- Biaya --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Biaya (Rp)</label>
            <div class="col-sm-9">
                <input type="number" name="biaya" class="form-control" min="0"
                    value="{{ old('biaya', $lomba->biaya) }}">
                <span class="invalid-feedback error-biaya"></span>
            </div>
        </div>

        {{-- Link Pendaftaran --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Link Pendaftaran</label>
            <div class="col-sm-9">
                <input type="url" name="link_pendaftaran" class="form-control"
                    value="{{ old('link_pendaftaran', $lomba->link_pendaftaran) }}">
                <span class="invalid-feedback error-link_pendaftaran"></span>
            </div>
        </div>

        {{-- Link Penyelenggara --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Link Penyelenggara</label>
            <div class="col-sm-9">
                <input type="url" name="link_penyelenggara" class="form-control"
                    value="{{ old('link_penyelenggara', $lomba->link_penyelenggara) }}">
                <span class="invalid-feedback error-link_penyelenggara"></span>
            </div>
        </div>

        {{-- Poster --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Poster Lomba</label>
            <div class="col-sm-9">
                <input type="file" name="poster" class="form-control" accept="image/*">
                @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
                    <small class="form-text text-muted mt-1 d-block">
                        Poster saat ini: <a href="{{ asset('storage/' . $lomba->poster) }}" target="_blank">Lihat
                            Poster</a>. Kosongkan jika tidak ingin mengubah.
                    </small>
                @endif
                <span class="invalid-feedback error-poster"></span>
            </div>
        </div>

        {{-- Status Verifikasi --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Status Verifikasi <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <select name="status_verifikasi" id="crud_edit_status_verifikasi" class="form-select" required>
                    <option value="pending" {{ old('status_verifikasi', $lomba->status_verifikasi) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="disetujui" {{ old('status_verifikasi', $lomba->status_verifikasi) == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ old('status_verifikasi', $lomba->status_verifikasi) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <span class="invalid-feedback error-status_verifikasi"></span>
            </div>
        </div>

        {{-- Catatan Verifikasi --}}
        <div class="row mb-3" id="crud_edit_catatan_verifikasi_wrapper"
            style="{{ ($lomba->status_verifikasi == 'ditolak' || old('status_verifikasi') == 'ditolak') ? '' : 'display:none;' }}">
            <label class="col-sm-3 col-form-label">Catatan Verifikasi</label>
            <div class="col-sm-9">
                <textarea name="catatan_verifikasi" id="crud_edit_catatan_verifikasi" class="form-control"
                    rows="2">{{ old('catatan_verifikasi', $lomba->catatan_verifikasi) }}</textarea>
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


@push('scripts')
    <script>
        $(document).ready(function () {
            const form = $('#formAdminEditLomba');
            const statusSelect = $('#crud_edit_status_verifikasi');
            const catatanWrapper = $('#crud_edit_catatan_verifikasi_wrapper');

            function toggleCatatan() {
                if (statusSelect.val() === 'ditolak') {
                    catatanWrapper.slideDown();
                } else {
                    catatanWrapper.slideUp();
                }
            }

            statusSelect.on('change', toggleCatatan);
            toggleCatatan(); // Jalankan saat load

            form.validate({
                rules: {
                    catatan_verifikasi: {
                        maxlength: 1000
                    }
                },
                submitHandler: function (formEl) {
                    let formData = new FormData(formEl);
                    const submitButton = $(formEl).find('button[type="submit"]');
                    const originalText = submitButton.html();

                    submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

                    $.ajax({
                        url: $(formEl).attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function (response) {
                            if (response.status) {
                                $('#modalFormLombaAdminCrud').modal('hide');
                                Swal.fire('Berhasil!', response.message, 'success');
                                if (typeof dtLombaCrudAdmin !== 'undefined') {
                                    dtLombaCrudAdmin.ajax.reload(null, false);
                                }
                            } else {
                                Swal.fire('Gagal!', response.message || 'Gagal menyimpan data.', 'error');
                            }
                        },
                        error: function (xhr) {
                            // Tambahkan error handler jika diperlukan
                        },
                        complete: function () {
                            submitButton.prop('disabled', false).html(originalText);
                        }
                    });
                }
            });
        });
    </script>
@endpush