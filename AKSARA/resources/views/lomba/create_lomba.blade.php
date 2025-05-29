<form id="formUserLomba" action="{{ isset($lomba) ? route('lomba.user.update', $lomba->lomba_id) : route('lomba.user.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($lomba))
        @method('PUT')
    @endif

    <div class="modal-header">
        <h5 class="modal-title">{{ isset($lomba) ? 'Edit Info Lomba' : 'Ajukan Info Lomba Baru' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
        {{-- Field-field sama seperti di lomba.mahasiswa.create.blade.php --}}
        {{-- Pastikan value diisi jika $lomba ada (untuk mode edit) --}}
        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="user_nama_lomba" class="form-label">Nama Lomba <span class="text-danger">*</span></label>
                <input type="text" name="nama_lomba" id="user_nama_lomba" class="form-control" value="{{ old('nama_lomba', $lomba->nama_lomba ?? '') }}" required>
                <span class="invalid-feedback error-nama_lomba"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="user_pembukaan_pendaftaran" class="form-label">Pembukaan Pendaftaran <span class="text-danger">*</span></label>
                <input type="date" name="pembukaan_pendaftaran" id="user_pembukaan_pendaftaran" class="form-control" value="{{ old('pembukaan_pendaftaran', isset($lomba->pembukaan_pendaftaran) ? $lomba->pembukaan_pendaftaran->format('Y-m-d') : '') }}" required>
                <span class="invalid-feedback error-pembukaan_pendaftaran"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="user_batas_pendaftaran" class="form-label">Batas Pendaftaran <span class="text-danger">*</span></label>
                <input type="date" name="batas_pendaftaran" id="user_batas_pendaftaran" class="form-control" value="{{ old('batas_pendaftaran', isset($lomba->batas_pendaftaran) ? $lomba->batas_pendaftaran->format('Y-m-d') : '') }}" required>
                <span class="invalid-feedback error-batas_pendaftaran"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="user_kategori" class="form-label">Kategori Lomba <span class="text-danger">*</span></label>
                <select name="kategori" id="user_kategori" class="form-select" required>
                    <option value="individu" {{ old('kategori', $lomba->kategori ?? '') == 'individu' ? 'selected' : '' }}>Individu</option>
                    <option value="kelompok" {{ old('kategori', $lomba->kategori ?? '') == 'kelompok' ? 'selected' : '' }}>Kelompok</option>
                </select>
                <span class="invalid-feedback error-kategori"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="user_tingkat" class="form-label">Tingkat Lomba <span class="text-danger">*</span></label>
                <select name="tingkat" id="user_tingkat" class="form-select" required>
                    <option value="lokal" {{ old('tingkat', $lomba->tingkat ?? '') == 'lokal' ? 'selected' : '' }}>Lokal/Daerah</option>
                    <option value="nasional" {{ old('tingkat', $lomba->tingkat ?? '') == 'nasional' ? 'selected' : '' }}>Nasional</option>
                    <option value="internasional" {{ old('tingkat', $lomba->tingkat ?? '') == 'internasional' ? 'selected' : '' }}>Internasional</option>
                </select>
                <span class="invalid-feedback error-tingkat"></span>
            </div>
             <div class="col-md-12 mb-3">
                <label for="user_penyelenggara" class="form-label">Penyelenggara <span class="text-danger">*</span></label>
                <input type="text" name="penyelenggara" id="user_penyelenggara" class="form-control" value="{{ old('penyelenggara', $lomba->penyelenggara ?? '') }}" required>
                <span class="invalid-feedback error-penyelenggara"></span>
            </div>
            <div class="col-md-12 mb-3">
                <label for="user_bidang_keahlian" class="form-label">Bidang Keahlian Relevan <span class="text-danger">*</span></label>
                <input type="text" name="bidang_keahlian" id="user_bidang_keahlian" class="form-control" value="{{ old('bidang_keahlian', $lomba->bidang_keahlian ?? '') }}" placeholder="Cth: Web Development, Bisnis Digital" required>
                 <span class="invalid-feedback error-bidang_keahlian"></span>
            </div>
             <div class="col-md-12 mb-3">
                <label for="user_biaya" class="form-label">Biaya Pendaftaran (Kosongkan jika gratis)</label>
                <input type="number" name="biaya" id="user_biaya" class="form-control" value="{{ old('biaya', $lomba->biaya ?? '') }}" min="0" placeholder="Cth: 50000">
                <span class="invalid-feedback error-biaya"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="user_link_pendaftaran" class="form-label">Link Pendaftaran (Opsional)</label>
                <input type="url" name="link_pendaftaran" id="user_link_pendaftaran" class="form-control" value="{{ old('link_pendaftaran', $lomba->link_pendaftaran ?? '') }}" placeholder="https://contoh.com/daftar">
                 <span class="invalid-feedback error-link_pendaftaran"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="user_link_penyelenggara" class="form-label">Link Website Penyelenggara (Opsional)</label>
                <input type="url" name="link_penyelenggara" id="user_link_penyelenggara" class="form-control" value="{{ old('link_penyelenggara', $lomba->link_penyelenggara ?? '') }}" placeholder="https://penyelenggara.com">
                <span class="invalid-feedback error-link_penyelenggara"></span>
            </div>
            <div class="col-md-12 mb-3">
                <label for="user_poster" class="form-label">Poster Lomba (Opsional, Max 2MB)</label>
                <input type="file" name="poster" id="user_poster" class="form-control" accept="image/jpeg,image/png,image/jpg">
                 @if(isset($lomba) && $lomba->poster && Storage::disk('public')->exists($lomba->poster))
                    <small class="form-text text-muted mt-1 d-block">Poster saat ini: <a href="{{ asset('storage/'.$lomba->poster) }}" target="_blank">Lihat Poster</a>. Kosongkan jika tidak ingin mengubah.</small>
                @endif
                <span class="invalid-feedback error-poster"></span>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">{{ isset($lomba) ? 'Simpan Perubahan' : 'Ajukan Informasi Lomba' }}</button>
    </div>
</form>
<script>
  $(document).ready(function () {

        // Ambil form dengan ID yang benar
        const formCreate = $('#formCreateProdi');

        // Inisialisasi jQuery Validation untuk FORM DI DALAM MODAL
        // Pastikan ignore hidden fields diaktifkan saat inisialisasi
        formCreate.validate({
            rules: {
                kode: { required: true, minlength: 3 },
                nama: { required: true, minlength: 5 }
            },
            messages: {
                kode: { required: "Kode program studi tidak boleh kosong", minlength: "Kode program studi minimal 5 karakter" },
                nama: { required: "Nama program studi tidak boleh kosong", minlength: "Nama program studi minimal harus 3 karakter" }
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
                        // Pastikan variabel dataProdi dapat diakses (sudah dideklarasikan di index.blade.php)
                        if (typeof dataProdi !== 'undefined' && dataProdi.ajax) {
                            dataProdi.ajax.reload();
                        } else {
                            console.error("DataTable object 'dataProdi' not found or misconfigured.");
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