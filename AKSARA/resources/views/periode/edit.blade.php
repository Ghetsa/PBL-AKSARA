{{-- resources/views/periode/edit_ajax_form.blade.php --}}
<form id="formEditPeriode" class="form-horizontal" method="POST" action="{{ route('periode.update', $periode->periode_id) }}"> 
    @csrf
    @method('PUT') 

    <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Edit Periode Semester</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body">
        {{-- Field Semester --}}
        <div class="form-group row mb-3">
            <label for="edit_semester" class="col-sm-3 col-form-label">Semester</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="edit_semester" name="semester" value="{{ $periode->semester }}" required>
                <span class="invalid-feedback error-text" id="error-edit-semester"></span>
            </div>
        </div>
        {{-- Field Tahun Akademik --}}
        <div class="form-group row mb-3">
            <label for="edit_tahun" class="col-sm-3 col-form-label">Tahun Akademik</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="edit_tahun" name="tahun_akademik" value="{{ $periode->tahun_akademik }}" required>
                <span class="invalid-feedback error-text" id="error-edit-tahun"></span>
            </div>
        </div>
    </div> 

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>

<script>
$(document).ready(function () {
    // Ambil form dengan ID yang benar
    const formEdit = $('#formEditPeriode');

    formEdit.validate({
        rules: {
            semester: { required: true, minlength: 3 }, // Sesuaikan dengan validasi controller
            tahun_akademik: { required: true, minlength: 5 }  // Sesuaikan dengan validasi controller
        },
        messages: {
            semester: {
                required: "Semester program studi tidak boleh kosong",
                minlength: "Semester program studi minimal 3 karakter" 
            },
            tahun_akademik: {
                required: "Tahun Akademik program studi tidak boleh kosong",
                minlength: "Tahun Akademik program studi minimal 5 karakter" 
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

                        if (typeof dataPeriode !== 'undefined' && dataPeriode.ajax) {
                            dataPeriode.ajax.reload(null, false); // Reload DataTable tanpa reset paging
                        } else {
                            console.error("DataTable object 'dataPeriode' not found or misconfigured. Attempting page reload.");
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
                        $('#edit_' + key).addClass('is-invalid'); // ID input harus unik, misal #edit_semester
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
            let errorSpan = $('#error-edit-' + element.attr('name')); // error-edit-semester, error-edit-tahun_akademik
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