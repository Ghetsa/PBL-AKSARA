{{-- Form untuk Edit Lomba oleh Admin (dimuat di modal) --}}
{{-- Pastikan variabel $lomba dan $bidangList sudah di-pass ke view ini dari controller --}}
<form id="formAdminEditLomba" action="{{ route('admin.lomba.crud.update_ajax', $lomba->lomba_id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="modal-header">
        <h5 class="modal-title">Edit Informasi Lomba</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body" style="max-height: 68vh; overflow-y: auto;">
        {{-- Nama Lomba --}}
        <div class="form-group row mb-3">
            <label for="crud_e_nama_lomba" class="col-sm-3 col-form-label">Nama Lomba <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" name="nama_lomba" id="crud_e_nama_lomba" class="form-control" value="{{ old('nama_lomba', $lomba->nama_lomba) }}">
                <span class="invalid-feedback error-nama_lomba"></span>
            </div>
        </div>

        {{-- Tanggal Pendaftaran --}}
        <div class="form-group row mb-3">
            <label for="crud_e_pembukaan_pendaftaran" class="col-sm-3 col-form-label">Pembukaan Pendaftaran <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="date" name="pembukaan_pendaftaran" id="crud_e_pembukaan_pendaftaran" class="form-control" value="{{ old('pembukaan_pendaftaran', optional($lomba->pembukaan_pendaftaran)->format('Y-m-d')) }}">
                <span class="invalid-feedback error-pembukaan_pendaftaran"></span>
            </div>
        </div>

        <div class="form-group row mb-3">
            <label for="crud_e_batas_pendaftaran" class="col-sm-3 col-form-label">Batas Pendaftaran <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="date" name="batas_pendaftaran" id="crud_e_batas_pendaftaran" class="form-control" value="{{ old('batas_pendaftaran', optional($lomba->batas_pendaftaran)->format('Y-m-d')) }}">
                <span class="invalid-feedback error-batas_pendaftaran"></span>
            </div>
        </div>

        {{-- Kategori & Tingkat --}}
        <div class="form-group row mb-3">
            <label for="crud_e_kategori" class="col-sm-3 col-form-label">Kategori <span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <select name="kategori" id="crud_e_kategori" class="form-select">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="individu" {{ old('kategori', $lomba->kategori) == 'individu' ? 'selected' : '' }}>Individu</option>
                    <option value="kelompok" {{ old('kategori', $lomba->kategori) == 'kelompok' ? 'selected' : '' }}>Kelompok</option>
                </select>
                <span class="invalid-feedback error-kategori"></span>
            </div>

            <label for="crud_e_tingkat" class="col-sm-2 col-form-label ps-0">Tingkat <span class="text-danger">*</span></label>
            <div class="col-sm-4">
                <select name="tingkat" id="crud_e_tingkat" class="form-select">
                     <option value="">-- Pilih Tingkat --</option>
                    <option value="lokal" {{ old('tingkat', $lomba->tingkat) == 'lokal' ? 'selected' : '' }}>Lokal/Daerah</option>
                    <option value="nasional" {{ old('tingkat', $lomba->tingkat) == 'nasional' ? 'selected' : '' }}>Nasional</option>
                    <option value="internasional" {{ old('tingkat', $lomba->tingkat) == 'internasional' ? 'selected' : '' }}>Internasional</option>
                </select>
                <span class="invalid-feedback error-tingkat"></span>
            </div>
        </div>

        {{-- Penyelenggara --}}
        <div class="form-group row mb-3">
            <label for="crud_e_penyelenggara" class="col-sm-3 col-form-label">Penyelenggara <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" name="penyelenggara" id="crud_e_penyelenggara" class="form-control" value="{{ old('penyelenggara', $lomba->penyelenggara) }}">
                <span class="invalid-feedback error-penyelenggara"></span>
            </div>
        </div>

        {{-- Bidang Keahlian --}}
        <div class="col-md-12 mb-3">
            <label class="form-label d-block mb-2 text-sm-start">Bidang Keahlian Relevan <span class="text-danger">*</span></label>
            <div class="row ps-2">
                @php
                    // Pastikan $lomba->bidangKeahlian adalah collection atau array yang bisa di-pluck
                    $bidang_terpilih = old('bidang_keahlian', ($lomba->bidangKeahlian && method_exists($lomba->bidangKeahlian, 'pluck')) ? $lomba->bidangKeahlian->pluck('bidang_id')->toArray() : ($lomba->bidang_keahlian ?? []));
                    if (!is_array($bidang_terpilih) && is_string($bidang_terpilih)) { // Jika bidang_keahlian disimpan sebagai string CSV di model lama
                        $bidang_terpilih = explode(',', $bidang_terpilih);
                    } elseif (!is_array($bidang_terpilih)) {
                        $bidang_terpilih = [];
                    }
                @endphp
                @if(isset($bidangList) && $bidangList->count() > 0)
                    @foreach ($bidangList as $bidang)
                        @php
                            $inputIdSlug = Str::slug($bidang->bidang_nama) . '_edit_' . $bidang->bidang_id; // ID unik untuk edit
                            $isChecked = in_array($bidang->bidang_id, $bidang_terpilih);
                        @endphp
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="bidang_keahlian[]" value="{{ $bidang->bidang_id }}" id="{{ $inputIdSlug }}" {{ $isChecked ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $inputIdSlug }}">
                                    {{ $bidang->bidang_nama }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                @else
                     <p class="text-muted">Tidak ada data bidang keahlian.</p>
                @endif
            </div>
            <span class="invalid-feedback error-bidang_keahlian d-block mt-1"></span>
        </div>


        {{-- Biaya --}}
        <div class="form-group row mb-3">
            <label for="crud_e_biaya" class="col-sm-3 col-form-label">Biaya (Rp)</label>
            <div class="col-sm-9">
                <input type="number" name="biaya" id="crud_e_biaya" class="form-control" min="0" value="{{ old('biaya', $lomba->biaya) }}">
                <span class="invalid-feedback error-biaya"></span>
            </div>
        </div>

        {{-- Link Pendaftaran --}}
        <div class="form-group row mb-3">
            <label for="crud_e_link_pendaftaran" class="col-sm-3 col-form-label">Link Pendaftaran</label>
            <div class="col-sm-9">
                <input type="url" name="link_pendaftaran" id="crud_e_link_pendaftaran" class="form-control" value="{{ old('link_pendaftaran', $lomba->link_pendaftaran) }}">
                <span class="invalid-feedback error-link_pendaftaran"></span>
            </div>
        </div>

        {{-- Link Penyelenggara --}}
        <div class="form-group row mb-3">
            <label for="crud_e_link_penyelenggara" class="col-sm-3 col-form-label">Link Penyelenggara</label>
            <div class="col-sm-9">
                <input type="url" name="link_penyelenggara" id="crud_e_link_penyelenggara" class="form-control" value="{{ old('link_penyelenggara', $lomba->link_penyelenggara) }}">
                <span class="invalid-feedback error-link_penyelenggara"></span>
            </div>
        </div>

        {{-- Poster --}}
        <div class="form-group row mb-3">
            <label for="crud_e_poster" class="col-sm-3 col-form-label">Poster Lomba</label>
            <div class="col-sm-9">
                <input type="file" name="poster" id="crud_e_poster" class="form-control" accept="image/jpeg,image/png,image/jpg">
                @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
                    <small class="form-text text-muted mt-1 d-block">
                        Poster saat ini: <a href="{{ asset('storage/' . $lomba->poster) }}" target="_blank">Lihat Poster</a>. Kosongkan jika tidak ingin mengubah.
                    </small>
                @endif
                <span class="invalid-feedback error-poster"></span>
            </div>
        </div>
         {{-- Status Verifikasi & Catatan --}}
        @if(isset($isAdmin) && $isAdmin) {{-- Hanya tampilkan untuk admin --}}
            <div class="form-group row mb-3">
                <label for="crud_edit_status_verifikasi" class="col-sm-3 col-form-label">Status Verifikasi <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <select name="status_verifikasi" id="crud_edit_status_verifikasi" class="form-select">
                        <option value="pending" {{ old('status_verifikasi', $lomba->status_verifikasi) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="disetujui" {{ old('status_verifikasi', $lomba->status_verifikasi) == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ old('status_verifikasi', $lomba->status_verifikasi) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    <span class="invalid-feedback error-status_verifikasi"></span>
                </div>
            </div>
            <div class="form-group row mb-3" id="crud_edit_catatan_verifikasi_wrapper" style="{{ (old('status_verifikasi', $lomba->status_verifikasi) == 'ditolak') ? '' : 'display:none;' }}">
                <label for="crud_edit_catatan_verifikasi" class="col-sm-3 col-form-label">Catatan Verifikasi</label>
                <div class="col-sm-9">
                    <textarea name="catatan_verifikasi" id="crud_edit_catatan_verifikasi" class="form-control" rows="2">{{ old('catatan_verifikasi', $lomba->catatan_verifikasi) }}</textarea>
                    <small class="form-text text-muted">Wajib diisi jika status "Ditolak".</small>
                    <span class="invalid-feedback error-catatan_verifikasi"></span>
                </div>
            </div>
        @else {{-- Untuk user, status tidak bisa diubah, hanya ditampilkan atau tidak sama sekali --}}
            <input type="hidden" name="status_verifikasi" value="{{ $lomba->status_verifikasi }}">
        @endif


    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>

<script>
$(document).ready(function () {
    const formEditLomba = $('#formAdminEditLomba');
    // Variabel untuk status verifikasi hanya relevan jika ditampilkan untuk admin
    const statusSelectEdit = $('#crud_edit_status_verifikasi'); // Mungkin tidak ada jika bukan admin
    const catatanWrapperEdit = $('#crud_edit_catatan_verifikasi_wrapper');
    const catatanTextareaEdit = $('#crud_edit_catatan_verifikasi');

    if (statusSelectEdit.length) { // Cek apakah elemen ada (form admin)
        function toggleCatatanEdit() {
            if (statusSelectEdit.val() === 'ditolak') {
                catatanWrapperEdit.slideDown();
            } else {
                catatanWrapperEdit.slideUp();
                catatanTextareaEdit.removeClass('is-invalid').next('.invalid-feedback.error-catatan_verifikasi').empty().hide();
            }
        }
        statusSelectEdit.on('change', toggleCatatanEdit);
        toggleCatatanEdit();
    }


    $.validator.addMethod("afterDate", function(value, element, params) {
        if (!value || !$(params).val()) { return true; }
        return new Date(value) >= new Date($(params).val());
    }, 'Tanggal batas harus setelah atau sama dengan tanggal pembukaan.');

    $.validator.addMethod("filesize", function(value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    }, 'Ukuran file maksimal adalah 2MB.');

    $.validator.addMethod("nullableUrl", function(value, element) {
        if (value === "") return true;
        return /^(ftp|http|https):\/\/[^ "]+$/.test(value);
    }, "Format URL tidak valid.");

    let validationRules = {
        nama_lomba: { required: true, maxlength: 255 },
        pembukaan_pendaftaran: { required: true, dateISO: true },
        batas_pendaftaran: { required: true, dateISO: true, afterDate: '#crud_e_pembukaan_pendaftaran' },
        kategori: { required: true },
        penyelenggara: { required: true, maxlength: 255 },
        tingkat: { required: true },
        'bidang_keahlian[]': { required: true, minlength: 1 },
        biaya: { number: true, min: 0 },
        link_pendaftaran: { nullableUrl: true, maxlength: 255 },
        link_penyelenggara: { nullableUrl: true, maxlength: 255 },
        poster: { extension: "jpg|jpeg|png", filesize: 2097152 }
    };

    let validationMessages = {
        nama_lomba: { required: "Nama lomba wajib diisi.", maxlength: "Nama lomba maksimal 255 karakter." },
        pembukaan_pendaftaran: { required: "Tanggal pembukaan wajib diisi.", dateISO: "Format tanggal tidak valid." },
        batas_pendaftaran: { required: "Batas pendaftaran wajib diisi.", dateISO: "Format tanggal tidak valid.", afterDate: "Batas pendaftaran harus setelah atau sama dengan tanggal pembukaan." },
        kategori: { required: "Kategori lomba wajib dipilih." },
        penyelenggara: { required: "Penyelenggara wajib diisi.", maxlength: "Penyelenggara maksimal 255 karakter." },
        tingkat: { required: "Tingkat lomba wajib dipilih." },
        'bidang_keahlian[]': { required: "Pilih minimal satu bidang keahlian.", minlength: "Pilih minimal satu bidang keahlian." },
        biaya: { number: "Biaya harus berupa angka.", min: "Biaya tidak boleh negatif." },
        link_pendaftaran: { nullableUrl: "Format URL pendaftaran tidak valid.", maxlength: "Link pendaftaran maksimal 255 karakter." },
        link_penyelenggara: { nullableUrl: "Format URL penyelenggara tidak valid.", maxlength: "Link penyelenggara maksimal 255 karakter." },
        poster: { extension: "Format file poster tidak valid (hanya JPG, JPEG, PNG).", filesize: "Ukuran file poster maksimal 2MB." }
    };

    // Tambahkan aturan validasi untuk status & catatan jika form admin
    if (statusSelectEdit.length) {
        validationRules.status_verifikasi = { required: true };
        validationRules.catatan_verifikasi = {
            required: function() { return statusSelectEdit.val() === 'ditolak'; },
            maxlength: 1000
        };
        validationMessages.status_verifikasi = { required: "Status verifikasi wajib dipilih." };
        validationMessages.catatan_verifikasi = {
            required: "Catatan verifikasi wajib diisi jika status ditolak.",
            maxlength: "Catatan verifikasi maksimal 1000 karakter."
        };
    }


    formEditLomba.validate({
        rules: validationRules,
        messages: validationMessages,
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            let fieldName = element.attr('name');

            if (fieldName === "bidang_keahlian[]") {
                element.closest('.col-md-12.mb-3').find('span.error-bidang_keahlian').html(error.html()).show();
            } else if (element.closest('.col-sm-9').length) {
                let errorContainer = element.closest('.col-sm-9').find('.invalid-feedback.error-' + fieldName.replace(/\[\]/g, ''));
                if (errorContainer.length) {
                    errorContainer.html(error.html()).show();
                } else {
                    if (element.next("small.form-text").length) {
                         error.insertAfter(element.next("small.form-text"));
                    } else {
                        element.after(error);
                    }
                }
            } else {
                element.after(error);
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid').removeClass('is-valid');
            if ($(element).attr("name") === "bidang_keahlian[]") {
                $(element).closest('.form-check').addClass('is-invalid-item');
            }
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid').addClass('is-valid');
            let fieldName = $(element).attr('name');
            if (fieldName === "bidang_keahlian[]") {
                $(element).closest('.form-check').removeClass('is-invalid-item');
                if (!$(element).closest('.col-md-12.mb-3').find('input[name="bidang_keahlian[]"].is-invalid').length) {
                    $(element).closest('.col-md-12.mb-3').find('span.error-bidang_keahlian').empty().hide();
                }
            } else if ($(element).closest('.col-sm-9').length) {
                $(element).closest('.col-sm-9').find('.invalid-feedback.error-' + fieldName.replace(/\[\]/g, '')).empty().hide();
            }
        },
        submitHandler: function(form) {
            let formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');
            const originalButtonText = submitButton.html();

            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
            
            $(form).find('.invalid-feedback').text('').hide();
            $(form).find('.form-control, .form-select, .form-check-input').removeClass('is-invalid is-valid');
            $(form).find('.form-check.is-invalid-item').removeClass('is-invalid-item');

            $.ajax({
                url: $(form).attr('action'),
                method: 'POST', 
                headers: { // Tambahkan header ini
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), // Jika belum di-setup global
                    'X-HTTP-Method-Override': 'PUT' // Ini kuncinya
                },
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        $('#modalFormLombaAdminCrud').modal('hide'); 
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success'
                        });
                        if (typeof dtLombaCrudAdmin !== 'undefined' && dtLombaCrudAdmin.ajax) {
                            dtLombaCrudAdmin.ajax.reload(null, false);
                        }
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message || 'Gagal menyimpan data. Periksa kembali isian Anda.',
                            icon: 'error'
                        });
                        if (response.errors) {
                            $.each(response.errors, function(key, messages) {
                                let inputName = key.replace(/\./g, '_');
                                let inputElement = $(form).find(`[name^="${inputName.split('_')[0]}"]`); // Cari berdasarkan name dasar
                                if (key === 'bidang_keahlian' || key.startsWith('bidang_keahlian.')) {
                                     inputElement = $(form).find(`[name="bidang_keahlian[]"]`).first(); // Targetkan elemen pertama dari grup checkbox
                                     let errorContainer = inputElement.closest('.col-md-12.mb-3').find('span.error-bidang_keahlian');
                                     errorContainer.html(messages[0]).show();
                                     // Tandai semua checkbox di grup sebagai invalid untuk konsistensi visual
                                     inputElement.closest('.col-md-12.mb-3').find('input[name="bidang_keahlian[]"]').addClass('is-invalid');
                                } else if (inputElement.length) {
                                    inputElement.addClass('is-invalid');
                                    let errorContainer = inputElement.closest('.col-sm-9').find('.invalid-feedback.error-' + inputName);
                                    if (errorContainer.length) {
                                        errorContainer.text(messages[0]).show();
                                    } else { // Fallback jika tidak ada span .error-FIELDNAME
                                        inputElement.after('<span class="invalid-feedback d-block">' + messages[0] + '</span>');
                                    }
                                }
                            });
                        }
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
                     if (xhr.responseJSON) {
                        errorMessage = xhr.responseJSON.message || (xhr.responseJSON.errors ? 'Terjadi kesalahan validasi.' : errorMessage);
                        if (xhr.responseJSON.errors) {
                             let errorMessagesList = '<ul class="text-start ps-3">';
                            $.each(xhr.responseJSON.errors, function(key, messages) {
                                 errorMessagesList += `<li>${messages[0]}</li>`;
                            });
                             errorMessagesList += '</ul>';
                             errorMessage = '<strong>Detail Kesalahan:</strong>' + errorMessagesList;
                        }
                    }
                    Swal.fire({
                        title: 'Error!',
                        html: errorMessage,
                        icon: 'error'
                    });
                },
                complete: function() {
                    submitButton.prop('disabled', false).html(originalButtonText);
                }
            });
        }
    });
});
</script>