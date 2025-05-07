<form id="formCreateUser" class="form-horizontal" method="POST" action="{{ route('user.store') }}">
    @csrf

    <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Tambah User Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body">
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

        {{-- Field tambahan (pastikan ID wrapper dan field name/ID unik) --}}
        <div id="form-nip-modal" style="display: none;">
            <div class="form-group row mb-3">
                <label for="nip" class="col-sm-2 col-form-label">NIP</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="nip" name="nip">
                    <span class="invalid-feedback error-text" id="error-nip"></span> 
                </div>
            </div>
        </div>
        <div id="form-keahlian-modal" style="display: none;">
            <div class="form-group row mb-3">
                <label for="bidang_keahlian" class="col-sm-2 col-form-label">Bidang Keahlian</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="bidang_keahlian" name="bidang_keahlian">
                    <span class="invalid-feedback error-text" id="error-bidang_keahlian"></span> 
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
                <label for="prodi_id" class="col-sm-2 col-form-label">Prodi</label>
                <div class="col-sm-10">
                    <select class="form-select" id="prodi_id" name="prodi_id">
                        <option value="">- Pilih Prodi -</option>
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
        <div id="form-bidang_minat-modal" style="display: none;">
            <div class="form-group row mb-3">
                <label for="bidang_minat" class="col-sm-2 col-form-label">Bidang Minat</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="bidang_minat" name="bidang_minat">
                    <span class="invalid-feedback error-text" id="error-bidang_minat"></span> 
                </div>
            </div>
        </div>
        <div id="form-keahlian-mahasiswa-modal" style="display: none;">
            <div class="form-group row mb-3">
                <label for="keahlian_mahasiswa" class="col-sm-2 col-form-label">Keahlian Mahasiswa</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="keahlian_mahasiswa" name="keahlian_mahasiswa">
                    <span class="invalid-feedback error-text" id="error-keahlian_mahasiswa"></span> 
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

    </div> {{-- Akhir dari modal-body --}}

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
                const formKeahlian = $('#form-keahlian-modal');
                const formNim = $('#form-nim-modal');
                const formProdi_id = $('#form-prodi_id-modal');
                const formPeriode = $('#form-periode_id-modal');
                const formBidang_Minat = $('#form-bidang_minat-modal'); // Pastikan ID ini benar
                const formKeahlianMahasiswa = $('#form-keahlian-mahasiswa-modal'); // Pastikan ID ini benar

                // Sembunyikan semua field tambahan dulu
                formNip.hide();
                formKeahlian.hide();
                formNim.hide();
                formProdi_id.hide();
                formPeriode.hide();
                formBidang_Minat.hide();
                formKeahlianMahasiswa.hide();

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
                    formKeahlian.show();
                    $('#nip').prop('required', true);
                    $('#bidang_keahlian').prop('required', true);
                } else if (role === 'mahasiswa') {
                    formNim.show();
                    formProdi_id.show();
                    formPeriode.show();
                    formBidang_Minat.show(); // Tampilkan field bidang minat
                    formKeahlianMahasiswa.show(); // Tampilkan field keahlian mahasiswa

                    $('#nim').prop('required', true);
                    $('#prodi_id').prop('required', true);
                    $('#periode_id').prop('required', true);
                    $('#bidang_minat').prop('required', true); // Set required bidang minat
                    $('#keahlian_mahasiswa').prop('required', true); // Set required keahlian mahasiswa
                }

                // Setelah mengubah required, perbarui aturan validasi jQuery Validate jika perlu
                // Pastikan ignore hidden fields diaktifkan
                formCreate.validate().settings.ignore = ":hidden";
                // Pemicu validasi untuk field yang mungkin berubah required-nya
                $('#role_modal').valid();
                $('#nip').valid();
                $('#bidang_keahlian').valid();
                $('#nim').valid();
                $('#prodi_id').valid();
                $('#periode_id').valid();
                $('#bidang_minat').valid();
                $('#keahlian_mahasiswa').valid();
            }

            // Event listener untuk select role
            $('#role_modal').on('change', toggleAdditionalFormsModal);

            // Inisialisasi jQuery Validation untuk FORM DI DALAM MODAL
            // Pastikan ignore hidden fields diaktifkan saat inisialisasi
            formCreate.validate({
                ignore: ":hidden", // Abaikan field yang disembunyikan
                rules: {
                    nama: { required: true, minlength: 3 },
                    email: { required: true, email: true },
                    password: { required: true, minlength: 6 },
                    role: { required: true },
                    status: { required: true },
                    nip: { digits: true }, // required diatur dinamis
                    nim: { digits: true }, // required diatur dinamis
                    bidang_keahlian: {}, // required diatur dinamis
                    prodi_id: {}, // required diatur dinamis
                    periode_id: {}, // required diatur dinamis
                    bidang_minat: {}, // required diatur dinamis
                    keahlian_mahasiswa: {} // required diatur dinamis
                },
                messages: {
                    nama: { required: "Nama tidak boleh kosong", minlength: "Nama minimal harus 3 karakter" },
                    email: { required: "Email tidak boleh kosong", email: "Format email tidak valid" },
                    password: { required: "Password tidak boleh kosong", minlength: "Password minimal harus 6 karakter" },
                    role: "Silakan pilih role",
                    status: "Silakan pilih status",
                    nip: { required: "NIP wajib diisi untuk role ini", digits: "NIP hanya boleh berisi angka" },
                    nim: { required: "NIM wajib diisi untuk role ini", digits: "NIM hanya boleh berisi angka" },
                    bidang_keahlian: { required: "Bidang keahlian wajib diisi untuk Dosen" },
                    prodi_id: { required: "Prodi wajib diisi untuk Mahasiswa" },
                    periode_id: { required: "Periode wajib diisi untuk Mahasiswa" },
                    bidang_minat: { required: "Bidang Minat wajib diisi untuk Mahasiswa" },
                    keahlian_mahasiswa: { required: "Keahlian mahasiswa wajib diisi untuk Mahasiswa" }
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
                            // Pastikan variabel dataUser dapat diakses (sudah dideklarasikan di index.blade.php)
                            if (typeof dataUser !== 'undefined' && dataUser.ajax) {
                                dataUser.ajax.reload();
                            } else {
                                console.error("DataTable object 'dataUser' not found or misconfigured.");
                                // Fallback: reload halaman jika DataTable tidak bisa di-reload
                                window.location.reload();
                            }

                            // Reset form setelah sukses
                            form.reset(); // Reset nilai field form
                            formCreate.validate().resetForm(); // Reset validasi jQuery
                            formCreate.find('.is-invalid').removeClass('is-invalid'); // Hapus class invalid
                            formCreate.find('.is-valid').removeClass('is-valid'); // Hapus class valid
                            toggleAdditionalFormsModal(); // Sembunyikan kembali field tambahan
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
            toggleAdditionalFormsModal();
        }); // End document ready
    </script>