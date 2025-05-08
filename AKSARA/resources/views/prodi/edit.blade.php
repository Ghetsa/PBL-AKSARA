{{-- resources/views/prodi/edit_ajax_form.blade.php --}}
<form id="formEditProdi" class="form-horizontal" method="POST" action="{{ route('prodi.update', $prodi->prodi_id) }}"> {{-- Ganti 'prodi_id' jika PK berbeda --}}
    @csrf
    @method('PUT') {{-- Penting untuk method update --}}

    <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Edit Program Studi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body">
        {{-- Field Kode Prodi --}}
        <div class="form-group row mb-3">
            <label for="edit_kode" class="col-sm-2 col-form-label">Kode Prodi</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="edit_kode" name="kode" value="{{ $prodi->kode }}" required>
                <span class="invalid-feedback error-text" id="error-edit-kode"></span>
            </div>
        </div>
        {{-- Field Nama Prodi --}}
        <div class="form-group row mb-3">
            <label for="edit_nama" class="col-sm-2 col-form-label">Nama Prodi</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="edit_nama" name="nama" value="{{ $prodi->nama }}" required>
                <span class="invalid-feedback error-text" id="error-edit-nama"></span>
            </div>
        </div>
    </div> {{-- Akhir dari modal-body --}}

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>

<script>
$(document).ready(function () {
    // Ambil form dengan ID yang benar
    const formEdit = $('#formEditProdi');

    formEdit.validate({
        rules: {
            kode: { required: true, minlength: 3 }, // Sesuaikan dengan validasi controller
            nama: { required: true, minlength: 5 }  // Sesuaikan dengan validasi controller
        },
        messages: {
            kode: {
                required: "Kode program studi tidak boleh kosong",
                minlength: "Kode program studi minimal 3 karakter" // Sesuaikan
            },
            nama: {
                required: "Nama program studi tidak boleh kosong",
                minlength: "Nama program studi minimal 5 karakter" // Sesuaikan
            }
        },
        submitHandler: function (form) {
            $('.error-text').text(''); // Clear previous specific errors
            $('.is-invalid').removeClass('is-invalid'); // Clear previous invalid states

            $.ajax({
                url: $(form).attr('action'),
                method: 'POST', // Form method is POST, _method:PUT handles the actual verb
                data: $(form).serialize(), // Mengirim _method:PUT juga
                dataType: 'json',
                beforeSend: function () {
                    $(form).find('button[type="submit"]').prop('disabled', true).text('Menyimpan...');
                },
                success: function (response) {
                    if (response.status) {
                        $("#myModal").modal('hide'); // Tutup modal utama

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                            });
                        } else {
                            alert(response.message);
                        }

                        if (typeof dataProdi !== 'undefined' && dataProdi.ajax) {
                            dataProdi.ajax.reload(null, false); // Reload DataTable tanpa reset paging
                        } else {
                            console.error("DataTable object 'dataProdi' not found or misconfigured. Attempting page reload.");
                            window.location.reload();
                        }
                    } else {
                        // Handle error status false dari server (meskipun sukses AJAX)
                        // Ini biasanya untuk validasi yang tidak tertangkap validator.fails() tapi dicek manual
                        let errorMessage = response.message || 'Gagal memperbarui data.';
                         if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: errorMessage });
                        } else {
                            alert(errorMessage);
                        }
                        // Jika ada error fields spesifik dari server meski status false
                        if (response.errors) {
                             $.each(response.errors, function (key, value) {
                                $('#error-edit-' + key).text(value[0]).show();
                                $('#edit_' + key).addClass('is-invalid');
                            });
                        }
                    }
                },
                error: function (xhr, status, error) {
                    $(form).find('button[type="submit"]').prop('disabled', false).text('Simpan Perubahan');

                    let errorMessage = 'Terjadi kesalahan saat memperbarui data.';
                    let errors = {};

                    if (xhr.responseJSON) {
                        errorMessage = xhr.responseJSON.message || errorMessage;
                        errors = xhr.responseJSON.errors || {};
                    } else {
                        errorMessage = 'Error: ' + xhr.status + ' ' + xhr.statusText + '. Cek console.';
                        console.error("AJAX Error Response:", xhr.responseText);
                    }

                    $('.error-text').text(''); // Clear previous specific errors
                    $('.form-control').removeClass('is-invalid').removeClass('is-valid');

                    $.each(errors, function (key, value) {
                        // Sesuaikan ID error span dan input field jika berbeda
                        $('#error-edit-' + key).text(value[0]).show();
                        $('#edit_' + key).addClass('is-invalid'); // ID input harus unik, misal #edit_kode
                    });

                    if (Object.keys(errors).length === 0) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: 'Oops...', text: errorMessage });
                        } else {
                            alert(errorMessage);
                        }
                    }
                },
                complete: function() {
                     // Pastikan tombol submit selalu aktif kembali jika tidak dihandle di success/error secara spesifik
                    $(form).find('button[type="submit"]').prop('disabled', false).text('Simpan Perubahan');
                }
            });
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            let errorSpan = $('#error-edit-' + element.attr('name')); // error-edit-kode, error-edit-nama
            if (errorSpan.length) {
                errorSpan.text(error.text()).show();
            } else {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid').removeClass('is-valid');
            $('#error-edit-' + $(element).attr('name')).show();
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid').addClass('is-valid');
            $('#error-edit-' + $(element).attr('name')).text('').hide();
        }
    });
});
</script>