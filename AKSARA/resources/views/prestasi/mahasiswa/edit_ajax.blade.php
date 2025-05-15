{{-- resources/views/mahasiswa/prestasi/create_ajax.blade.php --}}
{{-- Jika ini juga digunakan untuk edit, Anda perlu variabel $prestasi --}}
@php
    $isEditMode = isset($prestasi);
@endphp

<form id="{{ $isEditMode ? 'formEditPrestasi' : 'formTambahPrestasi' }}" method="PUT"
      action="{{ $isEditMode ? route('prestasi.mahasiswa.update_ajax', $prestasi->prestasi_id) : route('prestasi.mahasiswa.store_ajax') }}"
      enctype="multipart/form-data">
    @csrf
    @if($isEditMode)
        @method('PUT')
    @endif

    <div class="modal-header">
        <h5 class="modal-title">{{ $isEditMode ? 'Edit Prestasi' : 'Upload Prestasi Terbaru Anda' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        {{-- Field Nama Prestasi --}}
        <div class="form-group row mb-3">
            <label for="nama_prestasi" class="col-sm-3 col-form-label">Nama Prestasi <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="nama_prestasi" name="nama_prestasi" value="{{ old('nama_prestasi', $prestasi->nama_prestasi ?? '') }}" required>
                <span class="invalid-feedback error-text" id="error-nama_prestasi"></span>
            </div>
        </div>

        {{-- Field Kategori --}}
        <div class="form-group row mb-3">
            <label for="kategori" class="col-sm-3 col-form-label">Kategori <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <select class="form-select" id="kategori" name="kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="akademik" {{ old('kategori', $prestasi->kategori ?? '') == 'akademik' ? 'selected' : '' }}>Akademik</option>
                    <option value="non-akademik" {{ old('kategori', $prestasi->kategori ?? '') == 'non-akademik' ? 'selected' : '' }}>Non-Akademik</option>
                </select>
                <span class="invalid-feedback error-text" id="error-kategori"></span>
            </div>
        </div>

        {{-- Field Penyelenggara --}}
        <div class="form-group row mb-3">
            <label for="penyelenggara" class="col-sm-3 col-form-label">Penyelenggara <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="penyelenggara" name="penyelenggara" value="{{ old('penyelenggara', $prestasi->penyelenggara ?? '') }}" required>
                <span class="invalid-feedback error-text" id="error-penyelenggara"></span>
            </div>
        </div>

        {{-- Field Tingkat --}}
        <div class="form-group row mb-3">
            <label for="tingkat" class="col-sm-3 col-form-label">Tingkat <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <select class="form-select" id="tingkat" name="tingkat" required>
                    <option value="">-- Pilih Tingkat --</option>
                    <option value="kota" {{ old('tingkat', $prestasi->tingkat ?? '') == 'kota' ? 'selected' : '' }}>Kota/Kabupaten</option>
                    <option value="provinsi" {{ old('tingkat', $prestasi->tingkat ?? '') == 'provinsi' ? 'selected' : '' }}>Provinsi</option>
                    <option value="nasional" {{ old('tingkat', $prestasi->tingkat ?? '') == 'nasional' ? 'selected' : '' }}>Nasional</option>
                    <option value="internasional" {{ old('tingkat', $prestasi->tingkat ?? '') == 'internasional' ? 'selected' : '' }}>Internasional</option>
                </select>
                <span class="invalid-feedback error-text" id="error-tingkat"></span>
            </div>
        </div>

        {{-- Field Tahun --}}
        <div class="form-group row mb-3">
            <label for="tahun" class="col-sm-3 col-form-label">Tahun <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="number" class="form-control" id="tahun" name="tahun" placeholder="YYYY" min="1900" max="{{ date('Y') + 1 }}" value="{{ old('tahun', $prestasi->tahun ?? date('Y')) }}" required>
                <span class="invalid-feedback error-text" id="error-tahun"></span>
            </div>
        </div>

        {{-- Field Dosen Pembimbing --}}
        <div class="form-group row mb-3">
            <label for="dosen_id" class="col-sm-3 col-form-label">Dosen Pembimbing</label>
            <div class="col-sm-9">
                <select class="form-select" id="dosen_id" name="dosen_id">
                    <option value="">-- Pilih Dosen --</option>
                    @if(isset($dosens) && $dosens->count() > 0)
                        @foreach($dosens as $dosen)
                            {{-- Asumsi $dosen memiliki ->id dan relasi ->user->nama atau ->nama_dosen --}}
                            <option value="{{ $dosen->id ?? $dosen->dosen_id }}"
                                    {{ old('dosen_id', $prestasi->dosen_id ?? '') == ($dosen->id ?? $dosen->dosen_id) ? 'selected' : '' }}>
                                {{ $dosen->user->nama ?? ($dosen->nama ?? 'Nama Dosen Tidak Tersedia') }}
                            </option>
                        @endforeach
                    @endif
                </select>
                <span class="invalid-feedback error-text" id="error-dosen_id"></span>
            </div>
        </div>

        {{-- Field File Bukti --}}
        <div class="form-group row mb-3">
            <label for="file_bukti" class="col-sm-3 col-form-label">Unggah Bukti {{ $isEditMode ? '' : '(Wajib)' }}</label>
            <div class="col-sm-9">
                <input type="file" class="form-control" id="file_bukti" name="file_bukti" accept="pdf, jpg, jpeg, png" {{ !$isEditMode ? 'required' : '' }}>
                <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Max: 2MB.</small>
                @if($isEditMode && $prestasi->file_bukti)
                <small class="form-text text-muted mt-1 d-block">File saat ini:
                    <a href="{{ Storage::url($prestasi->file_bukti) }}" target="_blank">Lihat Bukti</a>.
                    Kosongkan jika tidak ingin mengubah.
                </small>
                @endif
                <span class="invalid-feedback error-text" id="error-file_bukti"></span>
            </div>
        </div>
        @if($isEditMode && $prestasi->status_verifikasi == 'ditolak' && $prestasi->catatan_verifikasi)
        <div class="alert alert-warning">
            <strong>Catatan Verifikasi Sebelumnya:</strong><br>
            {{ $prestasi->catatan_verifikasi }}
        </div>
        @endif

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">{{ $isEditMode ? 'Update Prestasi' : 'Simpan Prestasi' }}</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk Dosen Pembimbing jika Anda ingin fitur search
    // $('#dosen_id').select2({
    //     dropdownParent: $('#myModal'), // Penting jika modal Bootstrap
    //     theme: 'bootstrap-5',
    //     placeholder: '-- Tidak Ada / Pilih Dosen --',
    //     allowClear: true
    // });


    // Validasi form (jQuery Validation)
    const formPrestasi = $('#{{ $isEditMode ? "formEditPrestasi" : "formTambahPrestasi" }}');
    formPrestasi.validate({
        rules: {
            nama_prestasi: { required: true, maxlength: 255 },
            kategori: { required: true },
            penyelenggara: { required: true, maxlength: 255 },
            tingkat: { required: true },
            tahun: { required: true, digits: true, min: 1900, max: {{ date('Y') + 5 }} },
            dosen_id: { required: false }, // Ubah jadi true jika wajib
            file_bukti: {
                required: {{ !$isEditMode ? 'true' : 'false' }}, // Wajib hanya saat create
                extension: "pdf|jpg|jpeg|png",
                filesize: 2097152 // 2MB
            }
        },
        messages: {
            file_bukti: {
                required: "File bukti wajib diunggah.",
                extension: "Format file harus PDF, JPG, JPEG, atau PNG.",
                filesize: "Ukuran file maksimal 2MB."
            }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            if (element.prop("type") === "file" || element.hasClass('form-select')) {
                error.insertAfter(element.nextAll("small").first().length ? element.nextAll("small").first() : element);
            } else {
                element.closest('.col-sm-9').append(error);
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid').removeClass('is-valid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid').addClass('is-valid');
        },
        submitHandler: function(form) {
            var formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');
            const originalButtonText = submitButton.text();

            $.ajax({
                url: $(form).attr('action'),
                method: $(form).attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
                    $('.error-text').text('');
                    $('.form-control, .form-select').removeClass('is-invalid');
                },
                success: function(response) {
                    if (response.status) {
                        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('myModal'));
                        if(modalInstance) modalInstance.hide();
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message });
                        if (typeof dataPrestasiMahasiswa !== 'undefined') {
                            dataPrestasiMahasiswa.ajax.reload();
                        }
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal!', text: response.message || 'Terjadi kesalahan.' });
                        if (response.errors) {
                            $.each(response.errors, function(key, value) {
                                $('#error-' + key).text(value[0]).show();
                                $('#' + key).addClass('is-invalid'); // Ganti create_ dengan id input yang sebenarnya
                            });
                        }
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan server. Silakan coba lagi.';
                     if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            // Penargetan error yang lebih baik, cocokkan dengan name attribute
                            let targetId = $('[name="'+key+'"]').attr('id');
                            if (targetId) {
                                $('#error-' + targetId).text(value[0]).show(); // Jika ada span error khusus
                                $('#' + targetId).addClass('is-invalid');
                            } else {
                                // fallback jika tidak ada id khusus
                                 $('[name="'+key+'"]').addClass('is-invalid').parent().append('<span class="invalid-feedback error-text" style="display:block;">'+value[0]+'</span>');
                            }
                        });
                        errorMessage = xhr.responseJSON.message || 'Periksa kembali isian Anda.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({ icon: 'error', title: 'Oops...', text: errorMessage });
                },
                complete: function() {
                    submitButton.prop('disabled', false).text(originalButtonText);
                }
            });
        }
    });

    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0] && element.files[0].size <= param);
    }, 'Ukuran file melebihi batas {0} bytes.');
});
</script>