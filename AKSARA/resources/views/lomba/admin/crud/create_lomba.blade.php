{{-- Form untuk Tambah Lomba oleh Admin --}}
<form id="formAdminCreateLomba" action="{{ route('admin.lomba.crud.store_ajax') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Tambah Info Lomba Baru (Admin)</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
        {{-- Baris Nama Lomba --}}
        <div class="row mb-3">
            <label for="crud_create_nama_lomba" class="col-sm-3 col-form-label text-sm-end">Nama Lomba <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" name="nama_lomba" id="crud_create_nama_lomba" class="form-control" required>
                <span class="invalid-feedback error-nama_lomba"></span>
            </div>
        </div>

        {{-- Baris Tanggal Pendaftaran --}}
        <div class="row mb-3">
            <label for="crud_create_pembukaan_pendaftaran" class="col-sm-3 col-form-label text-sm-end">Pembukaan Pendaftaran <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="date" name="pembukaan_pendaftaran" id="crud_create_pembukaan_pendaftaran" class="form-control" required>
                <span class="invalid-feedback error-pembukaan_pendaftaran"></span>
            </div>
        </div>
        <div class="row mb-3">
            <label for="crud_create_batas_pendaftaran" class="col-sm-3 col-form-label text-sm-end">Batas Pendaftaran <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="date" name="batas_pendaftaran" id="crud_create_batas_pendaftaran" class="form-control" required>
                <span class="invalid-feedback error-batas_pendaftaran"></span>
            </div>
        </div>
        
        {{-- Baris Kategori & Tingkat --}}
        <div class="row mb-3">
            <label for="crud_create_kategori" class="col-sm-3 col-form-label text-sm-end">Kategori Lomba <span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <select name="kategori" id="crud_create_kategori" class="form-select" required>
                    <option value="individu">Individu</option>
                    <option value="kelompok">Kelompok</option>
                </select>
                <span class="invalid-feedback error-kategori"></span>
            </div>
            <label for="crud_create_tingkat" class="col-sm-3 col-form-label text-sm-end">Tingkat <span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <select name="tingkat" id="crud_create_tingkat" class="form-select" required>
                    <option value="lokal">Lokal/Daerah</option>
                    <option value="nasional">Nasional</option>
                    <option value="internasional">Internasional</option>
                </select>
                <span class="invalid-feedback error-tingkat"></span>
            </div>
        </div>

        {{-- Penyelenggara --}}
        <div class="row mb-3">
            <label for="crud_create_penyelenggara" class="col-sm-3 col-form-label text-sm-end">Penyelenggara <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" name="penyelenggara" id="crud_create_penyelenggara" class="form-control" required>
                <span class="invalid-feedback error-penyelenggara"></span>
            </div>
        </div>

        {{-- Bidang Keahlian --}}
        <div class="row mb-3">
            <label for="crud_create_bidang_keahlian" class="col-sm-3 col-form-label text-sm-end">Bidang Keahlian <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" name="bidang_keahlian" id="crud_create_bidang_keahlian" class="form-control" placeholder="Cth: Web Development, UI/UX" required>
                <span class="invalid-feedback error-bidang_keahlian"></span>
            </div>
        </div>
        
        {{-- Biaya --}}
        <div class="row mb-3">
            <label for="crud_create_biaya" class="col-sm-3 col-form-label text-sm-end">Biaya (Rp)</label>
            <div class="col-sm-9">
                <input type="number" name="biaya" id="crud_create_biaya" class="form-control" min="0" placeholder="Kosongkan jika gratis">
                <span class="invalid-feedback error-biaya"></span>
            </div>
        </div>

        {{-- Links --}}
        <div class="row mb-3">
            <label for="crud_create_link_pendaftaran" class="col-sm-3 col-form-label text-sm-end">Link Pendaftaran</label>
            <div class="col-sm-9">
                <input type="url" name="link_pendaftaran" id="crud_create_link_pendaftaran" class="form-control" placeholder="https://">
                <span class="invalid-feedback error-link_pendaftaran"></span>
            </div>
        </div>
        <div class="row mb-3">
            <label for="crud_create_link_penyelenggara" class="col-sm-3 col-form-label text-sm-end">Link Penyelenggara</label>
            <div class="col-sm-9">
                <input type="url" name="link_penyelenggara" id="crud_create_link_penyelenggara" class="form-control" placeholder="https://">
                <span class="invalid-feedback error-link_penyelenggara"></span>
            </div>
        </div>

        {{-- Poster --}}
        <div class="row mb-3">
            <label for="crud_create_poster" class="col-sm-3 col-form-label text-sm-end">Poster Lomba</label>
            <div class="col-sm-9">
                <input type="file" name="poster" id="crud_create_poster" class="form-control" accept="image/jpeg,image/png,image/jpg">
                <small class="form-text text-muted">Opsional. Max 2MB (JPG, PNG).</small>
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
// JavaScript untuk validasi dan AJAX submit (mirip dengan form edit, tapi action ke store)
$(document).ready(function() {
    const form = $('#formAdminCreateLomba');
    form.validate({
        // ... rules dan messages ...
        rules: { /* ... (lihat contoh form edit, sesuaikan field name jika berbeda) ... */
            nama_lomba: { required: true, maxlength: 255 },
            pembukaan_pendaftaran: { required: true, date: true },
            batas_pendaftaran: { required: true, date: true, afterDate: '#crud_create_pembukaan_pendaftaran' },
            kategori: { required: true },
            penyelenggara: { required: true, maxlength: 255 },
            tingkat: { required: true },
            bidang_keahlian: { required: true, maxlength: 255 },
            biaya: { number: true, min: 0 },
            link_pendaftaran: { url: true, maxlength: 255 },
            link_penyelenggara: { url: true, maxlength: 255 },
            poster: { extension: "jpg|jpeg|png", filesize: 2097152 /* 2MB */ }
        },
         errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            // Untuk input file, tempatkan error setelah elemen itu sendiri
            if (element.attr("type") == "file") {
                error.insertAfter(element.next("small")); // Atau setelah elemen itu sendiri jika tidak ada small
            } else if (element.is("select")) {
                 error.insertAfter(element);
            }
            else {
                element.closest('.col-sm-9').append(error);
            }
        },
        highlight: function (element) { $(element).addClass('is-invalid'); },
        unhighlight: function (element) { $(element).removeClass('is-invalid'); $(element).closest('.col-sm-9').find('.invalid-feedback').empty();},
        submitHandler: function(form) {
            let formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');
            const originalButtonText = submitButton.html();
            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menambah...');
            // Hapus error messages lama
             $(form).find('.invalid-feedback').text('');
             $(form).find('.form-control, .form-select').removeClass('is-invalid is-valid');

            $.ajax({
                url: $(form).attr('action'), method: 'POST', data: formData,
                processData: false, contentType: false, dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        $('#modalFormLombaAdminCrud').modal('hide'); // ID modal untuk CRUD Admin
                        Swal.fire('Berhasil!', response.message, 'success');
                        if (typeof dtLombaCrudAdmin !== 'undefined') { dtLombaCrudAdmin.ajax.reload(null, false); }
                    } else {
                        Swal.fire('Gagal!', response.message || 'Gagal menambah data.', 'error');
                    }
                },
                error: function(xhr) {
                    // ... (error handling Anda seperti di form edit) ...
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = '<ul>';
                    if (errors) {
                        $.each(errors, function(key, messages) {
                             errorMessages += `<li>${messages[0]}</li>`;
                            $('#crud_create_' + key).addClass('is-invalid').closest('.col-sm-9').find('.invalid-feedback.error-' + key).text(messages[0]).show();
                             if(!$('#crud_create_' + key).length) { // fallback for general errors
                                $(form).find('.modal-body').prepend(`<div class="alert alert-danger py-1 px-2 small">${key}: ${messages[0]}</div>`);
                            }
                        });
                    }
                     errorMessages += '</ul>';
                    Swal.fire('Validasi Gagal', xhr.responseJSON.message + '<br>' + errorMessages , 'error');
                },
                complete: function() { submitButton.prop('disabled', false).html(originalButtonText); }
            });
        }
    });
     $.validator.addMethod("afterDate", function(value, element, params) {
        if (!/Invalid|NaN/.test(new Date(value))) { return new Date(value) >= new Date($(params).val()); }
        return isNaN(value) && isNaN($(params).val()) || (Number(value) >= Number($(params).val()));
    },'Tanggal batas harus setelah atau sama dengan tanggal pembukaan.');
});
</script>