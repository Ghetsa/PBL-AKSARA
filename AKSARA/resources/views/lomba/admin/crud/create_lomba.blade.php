{{-- Form untuk Tambah Lomba oleh Admin (dimuat di modal) --}}
<form id="formAdminCreateLomba" action="{{ route('admin.lomba.crud.store_ajax') }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title">Tambah Info Lomba Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body" style="max-height: 66vh; overflow-y: auto;">
        {{-- Baris Nama Lomba --}}
        <div class="form-group row mb-3">
            <label for="crud_c_nama_lomba" class="col-sm-3 col-form-label text-md">Nama Lomba <span
                    class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" name="nama_lomba" id="crud_c_nama_lomba" class="form-control" required>
                <span class="invalid-feedback error-nama_lomba"></span>
            </div>
        </div>

        {{-- Baris Tanggal Pendaftaran --}}
        <div class="form-group row mb-3">
            <label for="crud_c_pembukaan_pendaftaran" class="col-sm-3 col-form-label text-md">Pembukaan Pendaftaran
                <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="date" name="pembukaan_pendaftaran" id="crud_c_pembukaan_pendaftaran"
                    class="form-control" required>
                <span class="invalid-feedback error-pembukaan_pendaftaran"></span>
            </div>
        </div>
        <div class="form-group row mb-3">
            <label for="crud_c_batas_pendaftaran" class="col-sm-3 col-form-label text-md">Batas Pendaftaran <span
                    class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="date" name="batas_pendaftaran" id="crud_c_batas_pendaftaran" class="form-control"
                    required>
                <span class="invalid-feedback error-batas_pendaftaran"></span>
            </div>
        </div>

        {{-- Baris Kategori & Tingkat --}}
        <div class="form-group row mb-3">
            <label for="crud_c_kategori" class="col-sm-3 col-form-label text-md">Kategori Lomba <span
                    class="text-danger">*</span></label>
            <div class="col-sm-3"> {{-- Dibuat lebih sempit agar Tingkat bisa di sampingnya --}}
                <select name="kategori" id="crud_c_kategori" class="form-select" required>
                    <option value="individu">Individu</option>
                    <option value="kelompok">Kelompok</option>
                </select>
                <span class="invalid-feedback error-kategori"></span>
            </div>
            <label for="crud_c_tingkat" class="col-sm-2 col-form-label text-md-end">Tingkat <span
                    class="text-danger">*</span></label>
            <div class="col-sm-4"> {{-- Sisa lebar kolom --}}
                <select name="tingkat" id="crud_c_tingkat" class="form-select" required>
                    <option value="lokal">Lokal/Daerah</option>
                    <option value="nasional">Nasional</option>
                    <option value="internasional">Internasional</option>
                </select>
                <span class="invalid-feedback error-tingkat"></span>
            </div>
        </div>

        {{-- Penyelenggara --}}
        <div class="form-group row mb-3">
            <label for="crud_c_penyelenggara" class="col-sm-3 col-form-label text-md">Penyelenggara <span
                    class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" name="penyelenggara" id="crud_c_penyelenggara" class="form-control" required>
                <span class="invalid-feedback error-penyelenggara"></span>
            </div>
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label">Bidang Keahlian Relevan <span class="text-danger">*</span></label>
            <div class="row">
                @php
                    $bidang_terpilih = old(
                        'bidang_keahlian',
                        isset($lomba) ? $lomba->bidangKeahlian->pluck('bidang_id')->toArray() : [],
                    );
                @endphp

                @foreach ($bidangList as $bidang)
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bidang_keahlian[]"
                                value="{{ $bidang->bidang_id }}" id="bidang_{{ $bidang->bidang_id }}"
                                {{ in_array($bidang->bidang_id, $bidang_terpilih) ? 'checked' : '' }}>
                            <label class="form-check-label" for="bidang_{{ $bidang->bidang_id }}">
                                {{ $bidang->bidang_nama }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
            <span class="invalid-feedback error-bidang_keahlian d-block"></span>
        </div>

        {{-- Biaya --}}
        <div class="form-group row mb-3">
            <label for="crud_c_biaya" class="col-sm-3 col-form-label text-md">Biaya (Rp)</label>
            <div class="col-sm-9">
                <input type="number" name="biaya" id="crud_c_biaya" class="form-control" min="0"
                    placeholder="Kosongkan jika gratis">
                <span class="invalid-feedback error-biaya"></span>
            </div>
        </div>

        {{-- Links --}}
        <div class="form-group row mb-3">
            <label for="crud_c_link_pendaftaran" class="col-sm-3 col-form-label text-md">Link Pendaftaran</label>
            <div class="col-sm-9">
                <input type="url" name="link_pendaftaran" id="crud_c_link_pendaftaran" class="form-control"
                    placeholder="https://">
                <span class="invalid-feedback error-link_pendaftaran"></span>
            </div>
        </div>
        <div class="form-group row mb-3">
            <label for="crud_c_link_penyelenggara" class="col-sm-3 col-form-label text-md">Link Penyelenggara</label>
            <div class="col-sm-9">
                <input type="url" name="link_penyelenggara" id="crud_c_link_penyelenggara" class="form-control"
                    placeholder="https://">
                <span class="invalid-feedback error-link_penyelenggara"></span>
            </div>
        </div>

        {{-- Poster --}}
        <div class="form-group row mb-3">
            <label for="crud_c_poster" class="col-sm-3 col-form-label text-md">Poster Lomba</label>
            <div class="col-sm-9">
                <input type="file" name="poster" id="crud_c_poster" class="form-control"
                    accept="image/jpeg,image/png,image/jpg">
                <small class="form-text text-muted">Opsional. Max 2MB (JPG, PNG).</small>
                <span class="invalid-feedback error-poster"></span>
            </div>
        </div>
        {{-- Admin tidak perlu memilih status verifikasi saat create, karena default 'disetujui' --}}
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Tambah Lomba</button>
    </div>
</form>
<script>
    $(document).ready(function() {
        const formAdminCreateLomba = $('#formAdminCreateLomba'); // ID form yang benar

        // Custom rule untuk afterDate (jika jQuery validation tidak punya default)
        $.validator.addMethod("afterDate", function(value, element, params) {
            if (!value || !$(params)
                .val()) { // Jika salah satu kosong, anggap valid untuk tidak memblokir validasi lain
                return true;
            }
            if (!/Invalid|NaN/.test(new Date(value))) {
                return new Date(value) >= new Date($(params).val());
            }
            return isNaN(value) && isNaN($(params).val()) || (Number(value) >= Number($(params).val()));
        }, 'Tanggal batas harus setelah atau sama dengan tanggal pembukaan.');

        $.validator.addMethod("filesize", function(value, element, param) {
            return this.optional(element) || (element.files[0].size <= param);
        }, 'Ukuran file maksimal adalah {0} bytes.');


        formAdminCreateLomba.validate({
            rules: {
                nama_lomba: {
                    required: true,
                    maxlength: 255
                },
                pembukaan_pendaftaran: {
                    required: true,
                    dateISO: true
                }, // Gunakan dateISO untuk HTML5 date input
                batas_pendaftaran: {
                    required: true,
                    dateISO: true,
                    afterDate: '#crud_c_pembukaan_pendaftaran'
                },
                kategori: {
                    required: true
                },
                penyelenggara: {
                    required: true,
                    maxlength: 255
                },
                tingkat: {
                    required: true
                },
                'bidang_keahlian[]': {
                    required: true
                },
                biaya: {
                    number: true,
                    min: 0
                },
                link_pendaftaran: {
                    url: true,
                    maxlength: 255,
                    nullable: true
                }, // url dan nullable
                link_penyelenggara: {
                    url: true,
                    maxlength: 255,
                    nullable: true
                }, // url dan nullable
                poster: {
                    extension: "jpg|jpeg|png",
                    filesize: 2097152 /* 2MB */
                }
            },
            messages: {
                nama_lomba: {
                    required: "Nama lomba wajib diisi.",
                    maxlength: "Nama lomba maksimal 255 karakter."
                },
                pembukaan_pendaftaran: {
                    required: "Tanggal pembukaan wajib diisi.",
                    dateISO: "Format tanggal tidak valid."
                },
                batas_pendaftaran: {
                    required: "Tanggal batas pendaftaran wajib diisi.",
                    dateISO: "Format tanggal tidak valid.",
                    afterDate: "Batas pendaftaran harus setelah atau sama dengan tanggal pembukaan."
                },
                kategori: {
                    required: "Kategori lomba wajib dipilih."
                },
                penyelenggara: {
                    required: "Penyelenggara wajib diisi.",
                    maxlength: "Penyelenggara maksimal 255 karakter."
                },
                tingkat: {
                    required: "Tingkat lomba wajib dipilih."
                },
                'bidang_keahlian[]': {
                    required: "Minimal pilih satu bidang keahlian",
                },
                // 'bidang_keahlian[]': { required: "Pilih minimal satu bidang keahlian." },
                biaya: {
                    number: "Biaya harus berupa angka.",
                    min: "Biaya tidak boleh negatif."
                },
                link_pendaftaran: {
                    url: "Format URL tidak valid.",
                    maxlength: "Link pendaftaran maksimal 255 karakter."
                },
                link_penyelenggara: {
                    url: "Format URL tidak valid.",
                    maxlength: "Link penyelenggara maksimal 255 karakter."
                },
                poster: {
                    extension: "Format file poster tidak valid (jpg, jpeg, png).",
                    filesize: "Ukuran file poster maksimal 2MB."
                }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                // Menempatkan error message di dalam span.invalid-feedback yang sudah ada
                let errorContainer = element.closest('.col-sm-9').find('.invalid-feedback.error-' +
                    element.attr('name').replace(/\[\]/g, '')); // Hapus [] jika ada
                if (errorContainer.length) {
                    errorContainer.html(error.html()).show();
                } else {
                    // Fallback jika struktur sedikit berbeda atau span tidak ada
                    if (element.attr("type") == "file" || element.is("select")) {
                        error.insertAfter(element.next("small").length ? element.next("small") :
                            element);
                    } else {
                        element.closest('.col-sm-9').append(error);
                    }
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');
                $(element).closest('.col-sm-9').find('.invalid-feedback.error-' + $(element).attr(
                    'name').replace(/\[\]/g, '')).empty().hide();
            },
            submitHandler: function(form) {
                let formData = new FormData(form);
                const submitButton = $(form).find('button[type="submit"]');
                const originalButtonText = submitButton.html();

                submitButton.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menambah...'
                );
                $(form).find('.invalid-feedback').text('');
                $(form).find('.form-control, .form-select').removeClass('is-invalid is-valid');

                $.ajax({
                    url: $(form).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            $('#modalFormLombaAdminCrud').modal(
                                'hide'); // ID modal untuk CRUD Admin
                            Swal.fire('Berhasil!', response.message, 'success');
                            if (typeof dtLombaCrudAdmin !== 'undefined' &&
                                dtLombaCrudAdmin.ajax
                            ) { // Cek apakah DataTable terdefinisi
                                dtLombaCrudAdmin.ajax.reload(null, false);
                            }
                        } else {
                            Swal.fire('Gagal!', response.message ||
                                'Gagal menambah data.', 'error');
                            if (response.errors) {
                                $.each(response.errors, function(key, messages) {
                                    let inputElement = $(form).find(
                                        `[name="${key}"]`);
                                    let errorContainer = inputElement.closest(
                                        '.col-sm-9').find(
                                        '.invalid-feedback.error-' + key);
                                    inputElement.addClass('is-invalid');
                                    if (errorContainer.length) {
                                        errorContainer.text(messages[0]).show();
                                    } else {
                                        inputElement.after(
                                            '<span class="invalid-feedback d-block">' +
                                            messages[0] + '</span>');
                                    }
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan server.';
                        if (xhr.responseJSON) {
                            errorMessage = xhr.responseJSON.message || errorMessage;
                            if (xhr.responseJSON.errors) {
                                let errorMessagesList = '<ul>';
                                $.each(xhr.responseJSON.errors, function(key,
                                    messages) {
                                    errorMessagesList +=
                                        `<li>${messages[0]}</li>`;
                                    let inputElement = $(form).find(
                                        `[name="${key}"]`);
                                    let errorContainer = inputElement.closest(
                                        '.col-sm-9').find(
                                        '.invalid-feedback.error-' + key);
                                    inputElement.addClass('is-invalid');
                                    if (errorContainer.length) {
                                        errorContainer.text(messages[0]).show();
                                    } else {
                                        inputElement.after(
                                            '<span class="invalid-feedback d-block">' +
                                            messages[0] + '</span>');
                                    }
                                });
                                errorMessagesList += '</ul>';
                                errorMessage += '<br><small>Detail Error:</small>' +
                                    errorMessagesList;
                            }
                        }
                        Swal.fire('Error Validasi!', errorMessage, 'error');
                    },
                    complete: function() {
                        submitButton.prop('disabled', false).html(originalButtonText);
                    }
                });
            }
        });
    });
</script>
