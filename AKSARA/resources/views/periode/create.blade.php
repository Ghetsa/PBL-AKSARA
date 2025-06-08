<form id="formCreatePeriode" class="form-horizontal" method="POST" action="{{ route('periode.store_ajax') }}">
    @csrf

    <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Tambah Program Studi Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body">
        {{-- Field semester --}}
        <div class="form-group row mb-3">
            <label for="semester" class="col-sm-3 col-form-label">Semester</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="semester" name="semester" required>
                <span class="invalid-feedback error-text" id="error-semester"></span> 
            </div>
        </div>
        {{-- Field tahun akademik --}}
        <div class="form-group row mb-3">
            <label for="tahun_akademik" class="col-sm-3 col-form-label">Tahun Akademik</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="tahun_akademik" name="tahun_akademik" required>
                <span class="invalid-feedback error-text" id="error-tahun_akademik"></span> 
            </div>
        </div>
    </div> {{-- Akhir dari modal-body --}}

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>

</form>

<script>
    $(document).ready(function () {

        // Ambil form dengan ID yang benar
        const formCreate = $('#formCreatePeriode');

        // Inisialisasi jQuery Validation untuk FORM DI DALAM MODAL
        // Pastikan ignore hidden fields diaktifkan saat inisialisasi
        formCreate.validate({
            rules: {
                semester: { required: true, minlength: 5, maxlength: 10 },
                tahun_akademik: { required: true, minlength: 4, maxlength: 10 }
            },
            messages: {
                semester: { required: "Semester tidak boleh kosong", minlength: "Semester minimal 5 karakter", maxlength: "Semester maksimal 10 karakter" },
                tahun_akademik: { required: "Tahun akademik tidak boleh kosong", minlength: "Tahun akademik minimal 4 karakter", maxlength: "Tahun akademik maksimal 10 karakter" }
            },

            // --- AJAX Submission ---
            submitHandler: function (form) {
                // Reset tampilan error sebelum submit
                $('.error-text').text('');
                $('.is-invalid').removeClass('is-invalid');

                $.ajax({
                    url: $(form).attr('action'),
                    method: $(form).attr('method'),
                    data: $(form).serialize(),
                    dataType: 'json',
                    beforeSend: function () {
                        $(form).find('button[type="submit"]').prop('disabled', true).text('Menyimpan...');
                    },
                    success: function (response) {
                        // Tutup modal utama (#myModal)
                        $("#myModal").modal('hide');

                        // Tampilkan notifikasi sukses menggunakan SweetAlert2
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                            });
                        } else {
                            alert(response.message); // Fallback alert
                        }

                        // Reload DataTable
                        // Pastikan variabel dataPeriode dapat diakses (sudah dideklarasikan di index.blade.php)
                        if (typeof dataPeriode !== 'undefined' && dataPeriode.ajax) {
                            dataPeriode.ajax.reload();
                        } else {
                            console.error("DataTable object 'dataPeriode' not found or misconfigured.");
                            // Fallback: reload halaman jika DataTable tidak bisa di-reload
                            window.location.reload();
                        }

                        // Reset form setelah sukses
                        form.reset(); // Reset nilai field form
                        formCreate.validate().resetForm(); // Reset validasi jQuery
                        formCreate.find('.is-invalid').removeClass('is-invalid'); // Hapus class invalid
                        formCreate.find('.is-valid').removeClass('is-valid'); // Hapus class valid
                    },
                    error: function (xhr, status, error) {
                        // Aktifkan kembali tombol submit
                        $(form).find('button[type="submit"]').prop('disabled', false).text('Simpan');

                        // Tangani error (terutama validasi dari Laravel)
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                        let errors = {};

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            if (xhr.responseJSON.errors) {
                                errors = xhr.responseJSON.errors;
                            }
                        } else {
                            // Error selain JSON (misal 500 HTML response)
                            errorMessage = 'Error: ' + xhr.status + ' ' + xhr.statusText + '. Cek console browser untuk detail.';
                            console.error("AJAX Error Response:", xhr.responseText);
                        }

                        $('.error-text').text('');
                        // Tambahkan class is-invalid ke field yang error
                        $('.form-control, .form-select').removeClass('is-invalid').removeClass('is-valid'); // Hapus dulu semua state validasi
                        $.each(errors, function (key, value) {
                            $('#error-' + key).text(value[0]).show(); // Tampilkan pesan
                            $('#' + key).addClass('is-invalid'); // Tambahkan class is-invalid
                        });
                        if (Object.keys(errors).length === 0) { // Jika tidak ada error validasi spesifik
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: errorMessage,
                                });
                            } else {
                                alert(errorMessage); // Fallback alert
                            }
                        } else {
                            if (xhr.responseJSON.message && Object.keys(errors).length > 0) {
                                console.warn("General message alongside validation errors:", xhr.responseJSON.message);
                                // Opsional: tampilkan pesan umum di atas form atau di SweetAlert terpisah
                            }
                            // Biarkan pesan validasi di field terlihat oleh user
                        }
                    }
                });
            },
            // --- Validasi Styling ---
            errorElement: 'span', // Element yang digunakan untuk pesan error
            errorPlacement: function (error, element) {
                // Temukan span error-text yang terkait dengan element ini
                let errorSpan = $('#error-' + element.attr('name'));
                if (errorSpan.length) {
                    // Jika span error-text ada, masukkan pesan error ke sana
                    errorSpan.text(error.text()).show(); // Pastikan span terlihat
                } else {
                    // Jika tidak ada span error-text, fallback ke penempatan default Bootstrap
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                    console.warn("Missing span.error-text for field:", element.attr('name'));
                }
            },
            highlight: function (element, errorClass, validClass) {
                // Tambahkan class is-invalid ke element form yang error
                $(element).addClass('is-invalid');
                // Hapus class is-valid
                $(element).removeClass('is-valid');
                // Pastikan span error-text yang terkait terlihat (jika ada)
                $('#error-' + $(element).attr('name')).show();
            },
            unhighlight: function (element, errorClass, validClass) {
                // Hapus class is-invalid dari element form yang valid
                $(element).removeClass('is-invalid');
                // Tambahkan class is-valid (opsional, untuk feedback sukses)
                $(element).addClass('is-valid');
                // Kosongkan dan sembunyikan span error-text yang terkait
                $('#error-' + $(element).attr('name')).text('').hide();
            }
        });
    }); // End document ready
</script>