<form id="formCreateUser" class="form-horizontal" method="POST" action="{{ route('user.store_ajax') }}" enctype="multipart/form-data">
    @csrf

    <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Tambah User Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body" style="max-height: 68vh; overflow-y: auto;">
        <div class="form-group row mb-3">
            <label for="role_modal" class="col-sm-2 col-form-label">Role</label>
            <div class="col-sm-10">
                <select class="form-select" id="role_modal" name="role" required>
                    <option value="">- Pilih Role -</option>
                    <option value="admin">Admin</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                </select>
                <span class="invalid-feedback error-text" id="error-role"></span>
            </div>
        </div>

        {{-- Field Nama --}}
        <div class="form-group row mb-3">
            <label for="nama" class="col-sm-2 col-form-label">Nama</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="nama" name="nama" required>
                <span class="invalid-feedback error-text" id="error-nama"></span> 
            </div>
        </div>
        {{-- Field Email --}}
        <div class="form-group row mb-3">
            <label for="email" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="email" name="email" required>
                <span class="invalid-feedback error-text" id="error-email"></span> 
            </div>
        </div>
        {{-- Field Password --}}
        <div class="form-group row mb-3">
            <label for="password" class="col-sm-2 col-form-label">Password</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" id="password" name="password" required>
                <span class="invalid-feedback error-text" id="error-password"></span> 
            </div>
        </div>

        {{-- No. Telepon --}}
        <div class="form-group row mb-3">
            <label for="no_telepon_modal" class="col-sm-2 col-form-label">No. Telepon</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="no_telepon_modal" name="no_telepon">
                <span class="invalid-feedback error-text" id="error-no_telepon"></span> 
            </div>
        </div>

        {{-- Alamat --}}
        <div class="form-group row mb-3">
            <label for="alamat_modal" class="col-sm-2 col-form-label">Alamat</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="alamat_modal" name="alamat" rows="2"></textarea>
                <span class="invalid-feedback error-text" id="error-alamat"></span> 
            </div>
        </div>

        {{-- Field tambahan --}}
        <div id="form-nip-modal" style="display: none;">
            <div class="form-group row mb-3">
                <label for="nip" class="col-sm-2 col-form-label">NIP</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="nip" name="nip">
                    <span class="invalid-feedback error-text" id="error-nip"></span> 
                </div>
            </div>
        </div>
        <div id="form-nim-modal" style="display: none;">
            <div class="form-group row mb-3">
                <label for="nim" class="col-sm-2 col-form-label">NIM</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="nim" name="nim">
                    <span class="invalid-feedback error-text" id="error-nim"></span> 
                </div>
            </div>
        </div>
        <div id="form-prodi_id-modal" style="display: none;">
            <div class="form-group row mb-3">
                <label for="prodi_id" class="col-sm-2 col-form-label">Program Studi</label>
                <div class="col-sm-10">
                    <select class="form-select" id="prodi_id" name="prodi_id">
                        <option value="">- Pilih Program Studi -</option>
                        @if(isset($prodi))
                            @foreach($prodi as $item)
                                <option value="{{ $item->prodi_id }}">{{ $item->nama }}</option>
                            @endforeach
                        @endif
                    </select>
                    <span class="invalid-feedback error-text" id="error-prodi_id"></span> 
                </div>
            </div>
        </div>
        <div id="form-periode_id-modal" style="display: none;">
            <div class="form-group row mb-3">
                <label for="periode_id" class="col-sm-2 col-form-label">Periode</label>
                <div class="col-sm-10">
                    <select class="form-select" id="periode_id" name="periode_id">
                        <option value="">- Pilih Periode -</option>
                        @if(isset($periode))
                            @foreach($periode as $item)
                                <option value="{{ $item->periode_id }}">{{ $item->tahun_akademik }} / {{ $item->semester }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <span class="invalid-feedback error-text" id="error-periode_id"></span> 
                </div>
            </div>
        </div>

        {{-- Field Status --}}
        <div class="form-group row mb-3">
            <label for="status" class="col-sm-2 col-form-label">Status</label>
            <div class="col-sm-10">
                <select class="form-select" id="status" name="status" required>
                    <option value="">- Pilih Status -</option>
                    <option value="aktif" selected>Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
                <span class="invalid-feedback error-text" id="error-status"></span> 
            </div>
        </div>

    </div> 

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>

</form>

<script>
    $(document).ready(function () {

        // Ambil form dengan ID yang benar
        const formCreate = $('#formCreateUser');

        // Fungsi untuk menampilkan/menyembunyikan field tambahan
        function toggleAdditionalFormsModal() {
            // Ambil value dari select role di dalam modal
            const role = $('#role_modal').val();
            const formNip = $('#form-nip-modal');
            const formNim = $('#form-nim-modal');
            const formProdi_id = $('#form-prodi_id-modal');
            const formPeriode = $('#form-periode_id-modal');

            // Sembunyikan semua field tambahan terlebih dahulu
            formNip.hide();
            formNim.hide();
            formProdi_id.hide();
            formPeriode.hide();

            // Reset required attribute dan hapus pesan error untuk semua field yang di-toggle
            $('#nip, #bidang_keahlian, #nim, #prodi_id, #periode_id, #bidang_minat, #keahlian_mahasiswa')
                .prop('required', false)
                .removeClass('is-invalid')
                .next('.invalid-feedback').text(''); // Kosongkan pesan error

            // Tampilkan dan set required sesuai role
            if (role === 'admin') {
                formNip.show();
                $('#nip').prop('required', true);
            } else if (role === 'dosen') {
                formNip.show();
                $('#nip').prop('required', true);
            } else if (role === 'mahasiswa') {
                formNim.show();
                formProdi_id.show();
                formPeriode.show();

                $('#nim').prop('required', true);
                $('#prodi_id').prop('required', true);
                $('#periode_id').prop('required', true);
            }

            // Pastikan ignore hidden fields diaktifkan
            formCreate.validate().settings.ignore = ":hidden";
            // Pemicu validasi untuk field yang mungkin berubah required-nya
            $('#role_modal').valid();
            $('#nip').valid();
            $('#nim').valid();
            $('#prodi_id').valid();
            $('#periode_id').valid();
        }

        // Event listener untuk select role
        $('#role_modal').on('change', toggleAdditionalFormsModal);

        // Inisialisasi jQuery Validation untuk FORM DI DALAM MODAL
        formCreate.validate({
            ignore: ":hidden", // Abaikan field yang disembunyikan
            rules: {
                nama: { required: true, minlength: 3, maxlength:50 },
                email: { required: true, email: true },
                password: { required: true, minlength: 6, maxlength: 100 },
                role: { required: true },
                status: { required: true },
                no_telepon: { required: true, digits: true, maxlength:15 },
                alamat: {required: true, maxlength: 100},
                nip: { digits: true, maxlength: 10}, // required diatur dinamis
                nim: { digits: true, maxlength: 10}, // required diatur dinamis
                prodi_id: {}, // required diatur dinamis
                periode_id: {}, // required diatur dinamis
            },
            messages: {
                nama: { required: "Nama tidak boleh kosong", minlength: "Nama minimal harus 3 karakter", maxlength: "Nama maksimal 50 karakter"},
                email: { required: "Email tidak boleh kosong", email: "Format email tidak valid" },
                password: { required: "Password tidak boleh kosong", minlength: "Password minimal harus 6 karakter", maxlength: "Password maksimal 100 karakter" },
                role: "Silakan pilih role",
                status: "Silakan pilih status",
                no_telepon: { required: "Nomor telepon tidak boleh kosong", digits: "Nomor telepon hanya boleh berisi angka", maxlength: "Nomor telepon maksimal 15 karakter" },
                alamat: { required: "Alamat tidak boleh kosong", maxlength: "Alamat maksimal 100 karakter" },
                nip: { required: "NIP wajib diisi untuk role ini", digits: "NIP hanya boleh berisi angka", maxlength: "NIP maksimal 10 karakter" },
                nim: { required: "NIM wajib diisi untuk role ini", digits: "NIM hanya boleh berisi angka", maxlength: "NIM maksimal 10 karakter" },
                prodi_id: { required: "Prodi wajib diisi untuk Mahasiswa" },
                periode_id: { required: "Periode wajib diisi untuk Mahasiswa" },
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
                        // Cek apakah status = true dari backend
                        if (response.status === true) {
                            $("#myModal").modal('hide');

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                            });

                            if (typeof dataUser !== 'undefined' && dataUser.ajax) {
                                dataUser.ajax.reload();
                            } else {
                                window.location.reload();
                            }

                            form.reset();
                            formCreate.validate().resetForm();
                            formCreate.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
                            toggleAdditionalFormsModal();
                        } else {
                            // Jika status false, anggap validasi gagal → tampilkan pesan error
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validasi Gagal',
                                    text: response.message || 'Silakan periksa input Anda.',
                                });
                            }

                            // Jika ada errors di response, tampilkan di field terkait
                            if (response.errors) {
                                $.each(response.errors, function (key, value) {
                                    $('#error-' + key).text(value[0]).show();
                                    $('#' + key).addClass('is-invalid');
                                });
                            }
                        }

                        // Aktifkan kembali tombol submit
                        $(form).find('button[type="submit"]').prop('disabled', false).text('Simpan');
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
        toggleAdditionalFormsModal();
    }); 
</script>