{{-- Simpan sebagai resources/views/user/import_excel_ajax.blade.php (atau nama yang telah disesuaikan) --}}
{{-- Ini adalah KONTEN MODAL, untuk dimasukkan ke dalam <div class="modal-content"> --}}

<form action="{{ url('/user/import_ajax') }}" method="POST" id="form-import-user" enctype="multipart/form-data">
    @csrf
    {{-- Struktur modal-dialog dan modal-content biasanya ada di view utama yang memanggil modal ini --}}
    {{-- Jika file ini adalah modal penuh, uncomment bagian di bawah dan hapus dari pemanggil --}}
    {{-- <div class="modal-dialog modal-lg" role="document"> --}}
    {{-- <div class="modal-content"> --}}
    <div class="modal-header">
        <h5 class="modal-title" id="importUserModalLabel">Impor Data User dari Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-info-circle me-2"></i>Petunjuk Format Kolom Excel:</strong>
            <ol class="mb-0 ps-3 mt-2">
                <li>Kolom A: <strong>Username</strong> (Wajib, Unik)</li>
                <li>Kolom B: <strong>Nama Lengkap</strong> (Wajib)</li>
                <li>Kolom C: <strong>Email</strong> (Wajib, Unik)</li>
                <li>Kolom D: <strong>Password</strong> (Wajib, akan di-hash)</li>
                <li>Kolom E: <strong>Role</strong> (Wajib: "admin", "dosen", atau "mahasiswa")</li>
                <li>Kolom F: <strong>NIP/NIM</strong> (Wajib jika Role adalah admin/dosen/mahasiswa)</li>
                <li>Kolom G: <strong>No Telepon</strong> (Opsional)</li>
                <li>Kolom H: <strong>Alamat</strong> (Opsional)</li>
                <li>Kolom I: <strong>Kode Prodi</strong> (Opsional, Wajib jika Role "dosen" atau "mahasiswa")</li>
                <li>Kolom J: <strong>Tahun Angkatan</strong> (Opsional, Wajib jika Role "mahasiswa", format YYYY)</li>
            </ol>
            <p class="mt-2 mb-0"><small>Baris pertama pada file Excel akan dianggap sebagai header dan akan dilewati.</small></p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <div class="mb-3">
            <label for="templateDownload" class="form-label">Download Template Excel</label><br>
            <a href="{{ asset('templates/template_user.xlsx') }}" class="btn btn-outline-primary btn-sm" download id="templateDownload">
                <i class="fas fa-file-excel me-1"></i> Download Template
            </a>
            {{-- Pastikan file template_user_import.xlsx ada di folder public/templates/ --}}
            <div id="error-template" class="invalid-feedback d-block"></div> {{-- Untuk pesan error jika ada --}}
        </div>

        <div class="mb-3">
            <label for="file_user_excel" class="form-label">Pilih File Excel <span class="text-danger">*</span></label>
            <input type="file" name="file_user_excel" id="file_user_excel" class="form-control" required>
            <div id="error-file_user_excel" class="invalid-feedback d-block"></div> {{-- Untuk pesan error dari validasi JS/Server --}}
        </div>

        <div id="import-progress" class="mt-3" style="display:none;">
            <div class="progress" style="height: 20px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
            <p id="import-status-text" class="text-center mt-2 mb-0"></p>
        </div>

        <div id="import-errors-display" class="mt-3" style="max-height: 200px; overflow-y: auto;">
            {{-- Pesan error per baris akan ditampilkan di sini oleh JavaScript jika ada --}}
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="btn-submit-import">
            <i class="ti ti-upload me-1"></i> Upload & Proses
        </button>
    </div>
    {{-- </div> --}}
    {{-- </div> --}}
</form>

{{-- Pastikan jQuery dan jQuery Validate sudah dimuat sebelum script ini --}}
{{-- Contoh pemuatan library jika belum ada di layout utama:
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
--}}
<script>
$(document).ready(function() {
    if (typeof $().validate !== 'function') {
        console.error("jQuery Validate plugin is not loaded. Pastikan sudah dimuat.");
        // Mungkin tampilkan pesan ke pengguna jika SweetAlert2 sudah ada
        // if (typeof Swal !== 'undefined') {
        //     Swal.fire('Error Kritis', 'Komponen validasi tidak termuat. Harap hubungi administrator.', 'error');
        // }
        return;
    }
    // Disarankan juga untuk mengecek additional-methods jika Anda sangat bergantung padanya
    // if (typeof $.validator.methods.extension !== 'function') {
    //    console.error("jQuery Validate Additional Methods (untuk rule 'extension') tidak termuat.");
    // }

    initializeFormValidationUser();

    function initializeFormValidationUser() {
        $("#form-import-user").validate({
            rules: {
                file_user_excel: {
                    required: true,
                    extension: "xlsx|xls"
                }
            },
            messages: {
                file_user_excel: {
                    required: "Silakan pilih file Excel.",
                    extension: "Hanya file .xlsx atau .xls yang diizinkan."
                }
            },
            submitHandler: function(form) {
                var formData = new FormData(form);
                var submitButton = $('#btn-submit-import');
                var progressBar = $('.progress-bar'); // Lebih spesifik jika ada beberapa progress bar: $('#form-import-user .progress-bar')
                var progressContainer = $('#import-progress');
                var statusText = $('#import-status-text');
                var errorsDisplay = $('#import-errors-display');
                
                // Pastikan ID modal ini benar-benar ada di DOM sebagai ID dari elemen modal utama
                var modalElement = $('#myModal'); 

                submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengunggah...');
                progressContainer.show();
                progressBar.css('width', '0%').attr('aria-valuenow', 0).text('0%');
                statusText.text('Mengunggah file...');
                errorsDisplay.html('');

                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                                progressBar.css('width', percentComplete + '%').attr('aria-valuenow', percentComplete).text(percentComplete + '%');
                                if (percentComplete < 100) {
                                    statusText.text('Mengunggah file: ' + percentComplete + '%');
                                } else {
                                    statusText.text('File terunggah, memproses data...');
                                    progressBar.removeClass('bg-success bg-danger').addClass('bg-info');
                                }
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        submitButton.prop('disabled', false).html('<i class="fas fa-upload me-1"></i> Upload & Proses');
                        // progressContainer.hide(); // Sembunyikan progress setelah selesai, atau biarkan untuk status akhir
                        // progressBar.removeClass('bg-info');

                        if (response.status) {
                            progressBar.removeClass('bg-info').addClass('bg-success');
                            statusText.text('Impor berhasil!');
                            // Menunggu sebentar sebelum menutup modal dan menampilkan swal
                            // setTimeout(function() {
                                // if (modalElement.length && typeof bootstrap !== 'undefined') {
                                //     var modalInstance = bootstrap.Modal.getInstance(modalElement[0]);
                                //     if (modalInstance) {
                                //         modalInstance.hide();
                                //     } else {
                                //         // Fallback jika instance tidak ditemukan (kurang ideal)
                                //         modalElement.modal('hide');
                                //     }
                                // }
                                console.log("Mencoba menutup modal:", modalElement); // Debug modalElement
                                    if (modalElement.length && typeof bootstrap !== 'undefined') {
                                        var modalInstance = bootstrap.Modal.getInstance(modalElement[0]);
                                        console.log("Instance modal Bootstrap:", modalInstance); // Debug instance
                                        if (modalInstance) {
                                            modalInstance.hide();
                                        } else {
                                            console.warn('Instance modal Bootstrap tidak ditemukan. Mencoba fallback jQuery.');
                                            modalElement.modal('hide'); // Fallback
                                        }
                                    } else {
                                        if (!modalElement.length) console.error('#myModal tidak ditemukan di DOM.');
                                        if (typeof bootstrap === 'undefined') console.error('Variabel global bootstrap tidak terdefinisi.');
                                    }
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    html: response.message,
                                    confirmButtonText: 'OK'
                                });

                                if (typeof window.dataUserTable !== 'undefined' && typeof window.dataUserTable.ajax === 'function') {
                                    window.dataUserTable.ajax.reload(null, false); // false agar paging tidak reset
                                } else if (typeof window.table !== 'undefined' && typeof window.table.ajax === 'function') {
                                    window.table.ajax.reload(null, false);
                                } else {
                                    // location.reload(); // Opsi terakhir jika tidak ada datatable
                                }
                            // }, 1000); // Delay 1 detik

                        } else {
                            progressBar.removeClass('bg-info').addClass('bg-danger');
                            statusText.text('Impor gagal. Periksa detail kesalahan.');
                            var errorMessagesHtml = '<div class="alert alert-danger p-2 mt-2" role="alert"><h6 class="alert-heading mb-1">Detail Kesalahan:</h6><ul class="list-unstyled mb-0">';
                            if (response.errors_detail && Array.isArray(response.errors_detail) && response.errors_detail.length > 0) {
                                response.errors_detail.forEach(function(err) {
                                    errorMessagesHtml += '<li><small>' + err + '</small></li>';
                                });
                            } else if (response.msgField && typeof response.msgField === 'object') {
                                $.each(response.msgField, function(key, value) {
                                    errorMessagesHtml += '<li><small>' + value[0] + '</small></li>';
                                });
                            } else {
                                errorMessagesHtml += '<li><small>' + (response.message || 'Tidak ada detail error spesifik.') + '</small></li>';
                            }
                            errorMessagesHtml += '</ul></div>';
                            errorsDisplay.html(errorMessagesHtml);

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message || 'Terjadi kesalahan saat impor data.',
                                confirmButtonText: 'Coba Lagi'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        submitButton.prop('disabled', false).html('<i class="fas fa-upload me-1"></i> Upload & Proses');
                        progressBar.removeClass('bg-info bg-success').addClass('bg-danger').css('width', '100%').text('Gagal'); // Ubah teks progress bar
                        statusText.text('Impor gagal. Terjadi kesalahan validasi atau server.'); // Perjelas status teks

                        var responseJSON = null;
                        try {
                            responseJSON = JSON.parse(xhr.responseText); // Coba parse respons error dari server
                        } catch (e) {
                            // Biarkan responseJSON null jika bukan JSON
                        }

                        let errorTitle = 'Gagal Impor!';
                        let errorHtml = '<p>Proses impor tidak berhasil.</p>';

                        if (responseJSON && responseJSON.message) {
                            errorHtml = '<p>' + responseJSON.message + '</p>'; // Ambil pesan utama dari server
                            if (responseJSON.errors_detail && Array.isArray(responseJSON.errors_detail) && responseJSON.errors_detail.length > 0) {
                                errorHtml += '<p class="mb-1 mt-2"><strong>Detail Kesalahan:</strong></p><ul class="list-unstyled text-start mb-0" style="max-height: 150px; overflow-y: auto; font-size: 0.875em;">';
                                responseJSON.errors_detail.forEach(function(err) {
                                    errorHtml += '<li>' + err + '</li>';
                                });
                                errorHtml += '</ul>';
                            }
                        } else {
                            // Fallback jika respons bukan JSON atau tidak ada pesan spesifik
                            errorHtml = '<p>Gagal menghubungi server atau terjadi kesalahan tidak terduga.</p>';
                            errorHtml += '<p><small>Status: ' + status + ', Error: ' + error + '</small></p>';
                            errorTitle = 'Error Server';
                        }

                        // Logika untuk menutup modal (pastikan modalElement sudah didefinisikan di scope yang benar)
                        // var modalElement = $('#myModal'); // Jika belum didefinisikan atau untuk memastikan
                        setTimeout(function() {
                            // modalElement diambil dari scope submitHandler, seharusnya masih bisa diakses
                            if (modalElement.length && typeof bootstrap !== 'undefined') {
                                var modalInstance = bootstrap.Modal.getInstance(modalElement[0]);
                                if (modalInstance) {
                                    modalInstance.hide();
                                } else {
                                    // Fallback jika instance tidak ditemukan
                                    modalElement.modal('hide');
                                }
                            }
                        }, 1000); // Beri jeda sedikit agar pengguna sempat melihat status di modal jika perlu

                        Swal.fire({
                            icon: 'error',
                            title: errorTitle,
                            html: errorHtml,
                            confirmButtonText: 'Tutup'
                        });
                        console.error("AJAX error: ", status, error, xhr.responseText);
                    }
                });
                return false;
            },
            errorElement: 'div',
            errorPlacement: function(error, element) {
                var errorContainer = $('#error-' + element.attr('name')); // Target div error spesifik
                if (errorContainer.length) {
                    errorContainer.html(error.text()).addClass('d-block'); // Tampilkan pesan error di div tersebut
                } else {
                    error.addClass('invalid-feedback'); // Fallback
                    element.closest('.mb-3').append(error); // Sesuaikan dengan struktur terdekat Anda
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');
                $('#error-' + $(element).attr('name')).html('').removeClass('d-block'); // Bersihkan dan sembunyikan
            }
        });
    }
});
</script>


{{-- <form action="{{ url('/user/import_ajax') }}" method="POST" id="form-import" enctype="multipart/form-data">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Import Data User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Download Template</label>
                    <a href="{{ asset('template_user.xlsx') }}" class="btn btn-info btn-sm" download><i class="fa fa-file-excel"></i>Download</a>
                    <small id="error-kategori_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Pilih File</label>
                    <input type="file" name="file_user" id="file_user" class="form-control" required>
                    <small id="error-file_user" class="error-text form-text text-danger"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $("#form-import").validate({
            rules: {
                file_user: {required: true, extension: "xlsx"},
            },
            submitHandler: function(form) {
                var formData = new FormData(form); // Jadikan form ke FormData 
                // untuk menghandle file
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: formData, // Data yang dikirim berupa FormData
                    processData: false, // setting processData dan contentType 
                    // ke false, untuk menghandle file
                    contentType: false,
                    success: function(response) {
                        if(response.status){ // jika sukses
                            $('#myModal').modal('hide');
                                Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            dataUser.ajax.reload(); // reload datatable
                        } else { // jika error
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-'+prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script> --}}