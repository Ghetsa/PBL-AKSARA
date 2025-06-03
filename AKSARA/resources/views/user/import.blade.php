{{-- Simpan sebagai resources/views/user/import_excel_ajax.blade.php (atau nama yang telah disesuaikan) --}}
{{-- Ini adalah KONTEN MODAL, untuk dimasukkan ke dalam <div class="modal-content"> --}}

<form action="{{ url('/user/import-excel-ajax') }}" method="POST" id="form-import-user" enctype="multipart/form-data">
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
            <input type="file" name="file_user_excel" id="file_user_excel" class="form-control" required accept=".xls,.xlsx">
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
            <i class="fas fa-upload me-1"></i> Upload & Proses
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
    // Cek apakah jQuery Validate sudah dimuat
    if (typeof $().validate !== 'function') {
        console.error("jQuery Validate plugin is not loaded. Pastikan sudah dimuat.");
        // Anda bisa memuatnya secara dinamis jika perlu, atau menampilkan pesan ke pengguna
        // Contoh memuat dinamis (tidak ideal jika sering terjadi):
        // $.getScript("https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js", function() {
        //     $.getScript("https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js", function() {
        //         initializeFormValidationUser();
        //     });
        // });
        return; // Hentikan eksekusi jika validate tidak ada
    }

    initializeFormValidationUser(); // Panggil fungsi inisialisasi

    function initializeFormValidationUser() {
        $("#form-import-user").validate({
            rules: {
                file_user_excel: { // Sesuaikan dengan name input file
                    required: true,
                    extension: "xlsx|xls" // Memastikan ekstensi file adalah xlsx atau xls
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
                var progressBar = $('.progress-bar');
                var progressContainer = $('#import-progress');
                var statusText = $('#import-status-text');
                var errorsDisplay = $('#import-errors-display');
                var modalElement = $('#importUserModal'); // Ganti dengan ID modal utama Anda

                submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengunggah...');
                progressContainer.show();
                progressBar.css('width', '0%').attr('aria-valuenow', 0).text('0%');
                statusText.text('Mengunggah file...');
                errorsDisplay.html(''); // Bersihkan error sebelumnya

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
                                    progressBar.addClass('bg-info'); // Ganti warna saat proses
                                }
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        submitButton.prop('disabled', false).html('<i class="fas fa-upload me-1"></i> Upload & Proses');
                        progressContainer.hide();
                        progressBar.removeClass('bg-info');


                        if (response.status) {
                            // Tutup modal Bootstrap 5
                            var modalInstance = bootstrap.Modal.getInstance(modalElement[0]);
                            if (modalInstance) {
                                modalInstance.hide();
                            } else {
                                modalElement.modal('hide'); // Fallback jika instance tidak ditemukan
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                html: response.message, // response.message bisa berisi HTML
                                confirmButtonText: 'OK'
                            });

                            // Reload DataTable (sesuaikan dengan nama variabel DataTable Anda)
                            if (typeof window.dataUserTable !== 'undefined' && typeof window.dataUserTable.ajax === 'function') {
                                window.dataUserTable.ajax.reload();
                            } else if (typeof window.table !== 'undefined' && typeof window.table.ajax === 'function') {
                                window.table.ajax.reload();
                            } else {
                                console.log('DataTable variable (dataUserTable or table) not found. Cannot reload.');
                                // Pertimbangkan untuk refresh halaman jika DataTable tidak terdefinisi secara global
                                // location.reload();
                            }
                        } else {
                            var errorMessagesHtml = '<div class="alert alert-danger p-2" role="alert"><h6 class="alert-heading">Detail Kesalahan:</h6><ul class="list-group list-group-flush">';
                            if (response.errors_detail && Array.isArray(response.errors_detail) && response.errors_detail.length > 0) {
                                response.errors_detail.forEach(function(err) {
                                    errorMessagesHtml += '<li class="list-group-item list-group-item-danger p-1 ps-2">' + err + '</li>';
                                });
                            } else if(response.msgField && typeof response.msgField === 'object') {
                                $.each(response.msgField, function(key, value) {
                                     errorMessagesHtml += '<li class="list-group-item list-group-item-danger p-1 ps-2">' + value[0] + '</li>';
                                });
                            } else {
                                errorMessagesHtml += '<li class="list-group-item list-group-item-danger p-1 ps-2">Tidak ada detail error spesifik.</li>';
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
                        progressContainer.hide();
                        progressBar.removeClass('bg-info');
                        let errorMsg = 'Gagal menghubungi server atau terjadi error tidak terduga. Silakan coba lagi.';
                        if(xhr.responseJSON && xhr.responseJSON.message){
                            errorMsg = xhr.responseJSON.message;
                        } else if(xhr.responseText){
                             try {
                                let jsonResponse = JSON.parse(xhr.responseText);
                                errorMsg = jsonResponse.message || xhr.responseText;
                            } catch (e) {
                                errorMsg = xhr.responseText.substring(0, 200) + "... (Selengkapnya di console)"; // Potong pesan error panjang
                            }
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Server',
                            html: '<p>' + errorMsg + '</p><p><small>Status: ' + status + ', Error: ' + error + '</small></p>',
                            confirmButtonText: 'Tutup'
                        });
                        console.error("AJAX error: ", status, error, xhr.responseText);
                    }
                });
                return false; // Mencegah submit form standar
            },
            // Konfigurasi error placement untuk Bootstrap 5 dengan jQuery Validate
            errorElement: 'div', // Menggunakan div untuk pesan error agar bisa di-style sebagai invalid-feedback
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback'); // Kelas Bootstrap 5 untuk pesan error
                // Tempatkan pesan error di bawah elemen input, atau di dalam elemen small.error-text jika ada
                var errorTextElement = element.closest('.form-group').find('small.error-text');
                if (errorTextElement.length) {
                    errorTextElement.html(error);
                } else {
                    element.closest('.form-group').append(error);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass(validClass); // Tambah is-invalid
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass(validClass); // Hapus is-invalid
                $(element).closest('.form-group').find('.invalid-feedback').text(''); // Bersihkan pesan error
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