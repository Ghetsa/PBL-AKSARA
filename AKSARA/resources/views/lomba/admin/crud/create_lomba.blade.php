<form id="formAdminCreateLomba" action="{{ route('admin.lomba.crud.store_ajax') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title">Tambah Info Lomba Baru</h5>
        <button type="button" class="btn-close btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">
        {{-- Baris Nama Lomba --}}
        <div class="form-group row mb-3">
            <label for="crud_c_nama_lomba" class="col-sm-3 col-form-label">Nama Lomba</label>
            <div class="col-sm-9">
                <input type="text" name="nama_lomba" id="crud_c_nama_lomba" class="form-control">
                <span class="invalid-feedback error-nama_lomba"></span>
            </div>
        </div>

        {{-- Baris Tanggal Pendaftaran --}}
        <div class="form-group row mb-3">
            <label for="crud_c_pembukaan_pendaftaran" class="col-sm-3 col-form-label">Pembukaan Pendaftaran <span class="text-danger"></span></label>
            <div class="col-sm-9">
                <input type="date" name="pembukaan_pendaftaran" id="crud_c_pembukaan_pendaftaran" class="form-control">
                <span class="invalid-feedback error-pembukaan_pendaftaran"></span>
            </div>
        </div>
        <div class="form-group row mb-3">
            <label for="crud_c_batas_pendaftaran" class="col-sm-3 col-form-label">Batas Pendaftaran</label>
            <div class="col-sm-9">
                <input type="date" name="batas_pendaftaran" id="crud_c_batas_pendaftaran" class="form-control">
                <span class="invalid-feedback error-batas_pendaftaran"></span>
            </div>
        </div>

        {{-- Baris Kategori & Tingkat --}}
        <div class="form-group row mb-3">
            <label for="crud_c_kategori" class="col-sm-3 col-form-label">Kategori Peserta</label>
            <div class="col-sm-3">
                <select name="kategori" id="crud_c_kategori" class="form-select">
                    <option value="">-- Pilih --</option>
                    <option value="individu">Individu</option>
                    <option value="kelompok">Kelompok</option>
                </select>
                <span class="invalid-feedback error-kategori"></span>
            </div>
            <label for="crud_c_tingkat" class="col-sm-2 col-form-label text-sm-end mt-3 mt-sm-0">Tingkat</label>
            <div class="col-sm-4">
                <select name="tingkat" id="crud_c_tingkat" class="form-select">
                    <option value="">-- Pilih --</option>
                    <option value="lokal">Lokal/Daerah</option>
                    <option value="nasional">Nasional</option>
                    <option value="internasional">Internasional</option>
                </select>
                <span class="invalid-feedback error-tingkat"></span>
            </div>
        </div>

        {{-- Penyelenggara --}}
        <div class="form-group row mb-3">
            <label for="crud_c_penyelenggara" class="col-sm-3 col-form-label">Penyelenggara</label>
            <div class="col-sm-9">
                <input type="text" name="penyelenggara" id="crud_c_penyelenggara" class="form-control">
                <span class="invalid-feedback error-penyelenggara"></span>
            </div>
        </div>

        {{-- Bidang Keahlian --}}
        <div class="col-md-12 mb-3 px-0"> 
            <label class="form-label d-block mb-2 text">Bidang Keahlian Lomba</label>
            <div class="row ps-2">
                @if(isset($bidangList) && $bidangList->count() > 0)
                    @foreach ($bidangList as $bidang)
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="bidang_keahlian[]" value="{{ $bidang->bidang_id }}" id="crud_c_bidang_{{ $bidang->bidang_id }}">
                                <label class="form-check-label" for="crud_c_bidang_{{ $bidang->bidang_id }}">
                                    {{ $bidang->bidang_nama }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">Tidak ada data bidang keahlian.</p>
                @endif
            </div>
            <span class="invalid-feedback error-bidang_keahlian d-block mt-1 ps-2"></span>
        </div>

        {{-- Biaya --}}
        <div class="form-group row mb-3">
            <label for="crud_c_biaya" class="col-sm-3 col-form-label">Biaya (Rp)</label>
            <div class="col-sm-9">
                <input type="number" name="biaya" id="crud_c_biaya" class="form-control" min="0" placeholder="Kosongkan jika gratis">
                <span class="invalid-feedback error-biaya"></span>
            </div>
        </div>

        {{-- Input Hadiah Dinamis --}}
        <div class="form-group row mb-3">
            <label class="col-sm-3 col-form-label">Hadiah Lomba</label>
            <div class="col-sm-9">
                <div id="hadiahInputsContainerCreate">
                    {{-- Input hadiah pertama --}}
                    <div class="input-group mb-2 hadiah-input-group">
                        <input type="text" name="hadiah[]" class="form-control form-control-sm" placeholder="Contoh: Uang Tunai Rp 1.000.000">
                        <button type="button" class="btn btn-sm btn-danger remove-hadiah-btn-create"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                <button type="button" id="addHadiahBtnCreate" class="btn btn-sm btn-outline-success mt-1"><i class="fas fa-plus"></i> Tambah Hadiah</button>
                <span class="invalid-feedback error-hadiah d-block"></span>
            </div>
        </div>

        {{-- Links --}}
        <div class="form-group row mb-3">
            <label for="crud_c_link_pendaftaran" class="col-sm-3 col-form-label">Link Pendaftaran</label>
            <div class="col-sm-9">
                <input type="url" name="link_pendaftaran" id="crud_c_link_pendaftaran" class="form-control" placeholder="https://contoh.com/daftar">
                <span class="invalid-feedback error-link_pendaftaran"></span>
            </div>
        </div>
        <div class="form-group row mb-3">
            <label for="crud_c_link_penyelenggara" class="col-sm-3 col-form-label">Link Penyelenggara</label>
            <div class="col-sm-9">
                <input type="url" name="link_penyelenggara" id="crud_c_link_penyelenggara" class="form-control" placeholder="https://contoh.com/info">
                <span class="invalid-feedback error-link_penyelenggara"></span>
            </div>
        </div>

        {{-- Poster --}}
        <div class="form-group row mb-3">
            <label for="crud_c_poster" class="col-sm-3 col-form-label">Poster Lomba</label>
            <div class="col-sm-9">
                <input type="file" name="poster" id="crud_c_poster" class="form-control" accept="image/jpeg,image/png,image/jpg">
                <small class="form-text text-muted">Opsional. Max 2MB (JPG, JPEG, PNG).</small>
                <span class="invalid-feedback error-poster"></span>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Tambah Lomba</button>
    </div>
</form>

<script>
$(document).ready(function() {
    const formAdminCreateLomba = $('#formAdminCreateLomba');

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

    formAdminCreateLomba.validate({
        rules: {
            nama_lomba: { required: true, maxlength: 50, minlength: 5 },
            pembukaan_pendaftaran: { required: true, dateISO: true },
            batas_pendaftaran: { required: true, dateISO: true, afterDate: '#crud_c_pembukaan_pendaftaran' },
            kategori: { required: true },
            penyelenggara: { required: true, maxlength: 50, minlength: 2 },
            tingkat: { required: true },
            'bidang_keahlian[]': { required: true, minlength: 1 },
            biaya: { number: true, min: 0 },
            link_pendaftaran: { nullableUrl: true, maxlength: 150 },
            link_penyelenggara: { nullableUrl: true, maxlength: 150 },
            poster: { extension: "jpg|jpeg|png", filesize: 2097152 },
            'hadiah[]': { maxlength: 20 } // Validasi untuk setiap item hadiah
        },
        messages: {
            nama_lomba: { required: "Nama lomba wajib diisi.", maxlength: "Nama lomba maksimal 50 karakter.", minlength: "Nama lomba minimal 5 karakter." },
            pembukaan_pendaftaran: { required: "Tanggal pembukaan wajib diisi.", dateISO: "Format tanggal tidak valid." },
            batas_pendaftaran: { required: "Batas pendaftaran wajib diisi.", dateISO: "Format tanggal tidak valid.", afterDate: "Batas pendaftaran harus setelah atau sama dengan tanggal pembukaan." },
            kategori: { required: "Kategori peserta wajib dipilih." },
            penyelenggara: { required: "Penyelenggara wajib diisi.", maxlength: "Penyelenggara maksimal 50 karakter.", minlength: "Penyelenggara minimal 2 karakter." },
            tingkat: { required: "Tingkat lomba wajib dipilih." },
            'bidang_keahlian[]': { required: "Pilih minimal satu bidang keahlian.", minlength: "Pilih minimal satu bidang keahlian." },
            biaya: { number: "Biaya harus berupa angka.", min: "Biaya tidak boleh negatif." },
            link_pendaftaran: { nullableUrl: "Format URL pendaftaran tidak valid.", maxlength: "Link pendaftaran maksimal 150 karakter." },
            link_penyelenggara: { nullableUrl: "Format URL penyelenggara tidak valid.", maxlength: "Link penyelenggara maksimal 150 karakter." },
            poster: { extension: "Format file poster tidak valid (hanya JPG, JPEG, PNG).", filesize: "Ukuran file poster maksimal 2MB." },
            'hadiah[]': { maxlength: "Deskripsi hadiah maksimal 20 karakter."}
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            let fieldName = element.attr('name');

            if (fieldName === "bidang_keahlian[]") {
                element.closest('.col-md-12.mb-3').find('span.error-bidang_keahlian').html(error.html()).show();
            } else if (fieldName === "hadiah[]") {
                $('#hadiahInputsContainerCreate').closest('.col-sm-9').find('span.error-hadiah').html(error.html()).show();
                 // Jika ingin error per input hadiah:
                 // element.closest('.input-group').after(error);
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
            } else if (fieldName === "hadiah[]") {
                 if (!$('#hadiahInputsContainerCreate').find('input[name="hadiah[]"].is-invalid').length) {
                    $('#hadiahInputsContainerCreate').closest('.col-sm-9').find('span.error-hadiah').empty().hide();
                 }
            } else if ($(element).closest('.col-sm-9').length) {
                $(element).closest('.col-sm-9').find('.invalid-feedback.error-' + fieldName.replace(/\[\]/g, '')).empty().hide();
            }
        },
        submitHandler: function(form) {
            let formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');
            const originalButtonText = submitButton.html();

            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menambah...');
            
            $(form).find('.invalid-feedback').text('').hide();
            $(form).find('.form-control, .form-select, .form-check-input').removeClass('is-invalid is-valid');
            $(form).find('.form-check.is-invalid-item').removeClass('is-invalid-item');

            $.ajax({
                url: $(form).attr('action'),
                method: 'POST',
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
                            icon: 'success',
                        });
                        if (typeof dtLombaCrudAdmin !== 'undefined' && dtLombaCrudAdmin.ajax) {
                            dtLombaCrudAdmin.ajax.reload(null, false); 
                        }
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message || 'Gagal menambah data. Periksa kembali isian Anda.',
                            icon: 'error'
                        });
                        if (response.errors) {
                            $.each(response.errors, function(key, messages) {
                                let inputName = key.replace(/\./g, '_'); 
                                let inputElement = $(form).find(`[name^="${inputName.split('_')[0]}"]`); 
                                if (key === 'bidang_keahlian' || key.startsWith('bidang_keahlian.')) {
                                     inputElement = $(form).find(`[name="bidang_keahlian[]"]`).first(); 
                                     let errorContainer = inputElement.closest('.col-md-12.mb-3').find('span.error-bidang_keahlian');
                                     errorContainer.html(messages[0]).show();
                                     inputElement.closest('.col-md-12.mb-3').find('input[name="bidang_keahlian[]"]').addClass('is-invalid');
                                } else if (key === 'hadiah' || key.startsWith('hadiah.')) {
                                    let errorContainer = $('#hadiahInputsContainerCreate').closest('.col-sm-9').find('span.error-hadiah');
                                    errorContainer.html(messages[0]).show(); 
                                    $('#hadiahInputsContainerCreate').find('input[name="hadiah[]"]').addClass('is-invalid');
                                } else if (inputElement.length) {
                                    inputElement.addClass('is-invalid');
                                    let errorContainer = inputElement.closest('.col-sm-9').find('.invalid-feedback.error-' + inputName);
                                    if (errorContainer.length) {
                                        errorContainer.text(messages[0]).show();
                                    } else {
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

    // Logika untuk menambah/menghapus input hadiah
    $('#addHadiahBtnCreate').on('click', function() {
        const newHadiahInput = `
            <div class="input-group mb-2 hadiah-input-group">
                <input type="text" name="hadiah[]" class="form-control form-control-sm" placeholder="Deskripsi hadiah lainnya...">
                <button type="button" class="btn btn-sm btn-danger remove-hadiah-btn-create"><i class="fas fa-trash"></i></button>
            </div>
        `;
        $('#hadiahInputsContainerCreate').append(newHadiahInput);
    });

    $('#hadiahInputsContainerCreate').on('click', '.remove-hadiah-btn-create', function() {
        if ($('#hadiahInputsContainerCreate .hadiah-input-group').length > 1) {
            $(this).closest('.hadiah-input-group').remove();
        } else {
            $(this).closest('.hadiah-input-group').find('input[name="hadiah[]"]').val('');
        }
    });
    
    // if (typeof Modernizr !== 'undefined' && !Modernizr.inputtypes.date) { 
    //     $('input[type=date]').datepicker({ dateFormat: 'yy-mm-dd' });
    // }
});
</script>
