{{-- File: resources/views/user/edit_ajax.blade.php --}}
{{-- Ini hanya konten yang akan dimuat ke dalam #myModal .modal-content --}}

{{-- Cek apakah data user ditemukan --}}
@empty($user)
    {{-- Konten jika user tidak ditemukan --}}
    <div class="modal-header">
        <h5 class="modal-title">Kesalahan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body text-center">
        <i class="fas fa-exclamation-circle text-danger mb-3" style="font-size: 3rem;"></i>
        <p class="mb-0">Data user yang Anda cari tidak ditemukan.</p>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
    </div>
@else
    {{-- Konten form edit jika user ditemukan --}}
    {{-- Form untuk update data user via AJAX --}}
    {{-- Action mengarah ke route update_ajax dengan method PUT --}}
    <form id="formEditUser" class="form-horizontal" method="POST" action="{{ route('user.update_ajax', $user->user_id) }}">
        @csrf {{-- CSRF token --}}
        @method('PUT') {{-- Method spoofing untuk PUT request --}}

        <div class="modal-header">
            <h5 class="modal-title" id="myModalLabel">Edit User: {{ $user->nama }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
            {{-- Field Role (untuk menampilkan field tambahan yang sesuai) --}}
            {{-- Disabled agar role tidak bisa diubah setelah dibuat --}}
            <div class="form-group row mb-3">
                <label for="role_modal_edit" class="col-sm-2 col-form-label">Role</label>
                <div class="col-sm-10">
                    {{-- Hidden input untuk mengirim role ke backend --}}
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    {{-- Select hanya untuk tampilan, disabled --}}
                    <select class="form-select" id="role_modal_edit" disabled>
                        <option value="">- Pilih Role -</option>
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="mahasiswa" {{ $user->role == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="dosen" {{ $user->role == 'dosen' ? 'selected' : '' }}>Dosen</option>
                    </select>
                </div>
            </div>

            {{-- Field Nama --}}
            <div class="form-group row mb-3">
                <label for="nama_edit" class="col-sm-2 col-form-label">Nama</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="nama_edit" name="nama" value="{{ old('nama', $user->nama) }}" required>
                    <span class="invalid-feedback error-text" id="error-nama"></span>
                </div>
            </div>
            {{-- Field Email --}}
            <div class="form-group row mb-3">
                <label for="email_edit" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" id="email_edit" name="email" value="{{ old('email', $user->email) }}" required>
                    <span class="invalid-feedback error-text" id="error-email"></span>
                </div>
            </div>
            {{-- Field Password --}}
            <div class="form-group row mb-3">
                <label for="password_edit" class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                    {{-- Password field tidak required untuk edit --}}
                    <input type="password" class="form-control" id="password_edit" name="password">
                    <span class="invalid-feedback error-text" id="error-password"></span>
                    <small class="form-text text-muted">Abaikan jika tidak ingin mengganti password.</small>
                </div>
            </div>

            {{-- Field tambahan (sesuai role user) --}}
            {{-- Container untuk field Admin --}}
            <div id="form-admin-edit" style="display: none;">
                 <div class="form-group row mb-3">
                    <label for="nip_admin_edit" class="col-sm-2 col-form-label">NIP Admin</label>
                    <div class="col-sm-10">
                        {{-- Mengambil NIP dari relasi admin --}}
                        <input type="text" class="form-control" id="nip_admin_edit" name="nip_admin" value="{{ old('nip_admin', $user->admin->nip ?? '') }}">
                        <span class="invalid-feedback error-text" id="error-nip_admin"></span>
                    </div>
                </div>
            </div>

            {{-- Container untuk field Dosen --}}
            <div id="form-dosen-edit" style="display: none;">
                 <div class="form-group row mb-3">
                    <label for="nip_dosen_edit" class="col-sm-2 col-form-label">NIP Dosen</label>
                    <div class="col-sm-10">
                         {{-- Mengambil NIP dari relasi dosen --}}
                        <input type="text" class="form-control" id="nip_dosen_edit" name="nip_dosen" value="{{ old('nip_dosen', $user->dosen->nip ?? '') }}">
                        <span class="invalid-feedback error-text" id="error-nip_dosen"></span>
                    </div>
                </div>
                 <div class="form-group row mb-3">
                    <label for="bidang_keahlian_edit" class="col-sm-2 col-form-label">Bidang Keahlian</label>
                    <div class="col-sm-10">
                         {{-- Mengambil bidang keahlian dari relasi dosen --}}
                        <input type="text" class="form-control" id="bidang_keahlian_edit" name="bidang_keahlian" value="{{ old('bidang_keahlian', $user->dosen->bidang_keahlian ?? '') }}">
                        <span class="invalid-feedback error-text" id="error-bidang_keahlian"></span>
                    </div>
                </div>
            </div>

            {{-- Container untuk field Mahasiswa --}}
            <div id="form-mahasiswa-edit" style="display: none;">
                 <div class="form-group row mb-3">
                    <label for="nim_edit" class="col-sm-2 col-form-label">NIM</label>
                    <div class="col-sm-10">
                         {{-- Mengambil NIM dari relasi mahasiswa --}}
                        <input type="text" class="form-control" id="nim_edit" name="nim" value="{{ old('nim', $user->mahasiswa->nim ?? '') }}">
                        <span class="invalid-feedback error-text" id="error-nim"></span>
                    </div>
                </div>
                 <div class="form-group row mb-3">
                    <label for="prodi_id_edit" class="col-sm-2 col-form-label">Prodi</label>
                    <div class="col-sm-10">
                         {{-- Pastikan variabel $prodi dilewatkan dari controller --}}
                         {{-- Mengambil prodi_id dari relasi mahasiswa dan mencocokkan dengan opsi --}}
                        <select class="form-select" id="prodi_id_edit" name="prodi_id">
                            <option value="">- Pilih Prodi -</option>
                            @if(isset($prodi))
                                @foreach($prodi as $item)
                                    <option value="{{ $item->prodi_id }}" {{ old('prodi_id', $user->mahasiswa->prodi_id ?? '') == $item->prodi_id ? 'selected' : '' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <span class="invalid-feedback error-text" id="error-prodi_id"></span>
                    </div>
                </div>
                 <div class="form-group row mb-3">
                    <label for="periode_id_edit" class="col-sm-2 col-form-label">Periode</label>
                    <div class="col-sm-10">
                         {{-- Pastikan variabel $periode dilewatkan dari controller --}}
                         {{-- Mengambil periode_id dari relasi mahasiswa dan mencocokkan dengan opsi --}}
                        <select class="form-select" id="periode_id_edit" name="periode_id">
                            <option value="">- Pilih Periode -</option>
                            @if(isset($periode))
                                @foreach($periode as $item)
                                    <option value="{{ $item->periode_id }}" {{ old('periode_id', $user->mahasiswa->periode_id ?? '') == $item->periode_id ? 'selected' : '' }}>
                                        {{ $item->tahun_akademik }} / {{ $item->semester }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <span class="invalid-feedback error-text" id="error-periode_id"></span>
                    </div>
                </div>
                 {{-- Field bidang minat dan keahlian mahasiswa --}}
                <div class="form-group row mb-3">
                    <label for="bidang_minat_edit" class="col-sm-2 col-form-label">Bidang Minat</label>
                    <div class="col-sm-10">
                         {{-- Mengambil bidang minat dari relasi mahasiswa --}}
                        <input type="text" class="form-control" id="bidang_minat_edit" name="bidang_minat" value="{{ old('bidang_minat', $user->mahasiswa->bidang_minat ?? '') }}">
                        <span class="invalid-feedback error-text" id="error-bidang_minat"></span>
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <label for="keahlian_mahasiswa_edit" class="col-sm-2 col-form-label">Keahlian Mahasiswa</label>
                    <div class="col-sm-10">
                         {{-- Mengambil keahlian mahasiswa dari relasi mahasiswa --}}
                        <input type="text" class="form-control" id="keahlian_mahasiswa_edit" name="keahlian_mahasiswa" value="{{ old('keahlian_mahasiswa', $user->mahasiswa->keahlian_mahasiswa ?? '') }}">
                        <span class="invalid-feedback error-text" id="error-keahlian_mahasiswa"></span>
                    </div>
                </div>
            </div>


            {{-- Field Status --}}
            <div class="form-group row mb-3">
                <label for="status_edit" class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-10">
                    <select class="form-select" id="status_edit" name="status" required>
                        <option value="">- Pilih Status -</option>
                        <option value="aktif" {{ old('status', $user->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $user->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                     <span class="invalid-feedback error-text" id="error-status"></span>
                </div>
            </div>

        </div> {{-- Akhir dari modal-body --}}

        <div class="modal-footer">
            {{-- Tombol Batal --}}
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            {{-- Tombol submit form --}}
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>

    </form> {{-- Akhir dari tag <form> --}}
@endempty

{{-- Script ini harus inline agar dieksekusi setelah HTML dimuat via AJAX --}}
<script>
    $(document).ready(function() {
        // ----- LOGIKA FORM EDIT USER (di dalam modal) -----

        // Ambil form dengan ID yang benar
        const formEdit = $('#formEditUser');

        // Fungsi untuk menampilkan/menyembunyikan field tambahan
        function toggleAdditionalFormsModalEdit() {
            // Ambil value dari hidden input role (karena select role disabled)
            const role = formEdit.find('input[name="role"]').val();
            const formAdmin = $('#form-admin-edit');
            const formDosen = $('#form-dosen-edit');
            const formMahasiswa = $('#form-mahasiswa-edit');

            // Sembunyikan semua field tambahan dulu
            formAdmin.hide();
            formDosen.hide();
            formMahasiswa.hide();

            // Reset required attribute dan hapus pesan error untuk semua field yang di-toggle
            $('#nip_admin_edit, #nip_dosen_edit, #bidang_keahlian_edit, #nim_edit, #prodi_id_edit, #periode_id_edit, #bidang_minat_edit, #keahlian_mahasiswa_edit')
                .prop('required', false)
                .removeClass('is-invalid')
                .next('.invalid-feedback').text(''); // Kosongkan pesan error

            // Tampilkan dan set required sesuai role
            if (role === 'admin') {
                formAdmin.show();
                $('#nip_admin_edit').prop('required', true);
            } else if (role === 'dosen') {
                formDosen.show();
                $('#nip_dosen_edit').prop('required', true);
                $('#bidang_keahlian_edit').prop('required', true);
            } else if (role === 'mahasiswa') {
                formMahasiswa.show();
                $('#nim_edit').prop('required', true);
                $('#prodi_id_edit').prop('required', true);
                $('#periode_id_edit').prop('required', true);
                $('#bidang_minat_edit').prop('required', true);
                $('#keahlian_mahasiswa_edit').prop('required', true);
            }

            // Setelah mengubah required, perbarui aturan validasi jQuery Validate jika perlu
            formEdit.validate().settings.ignore = ":hidden";
             // Pemicu validasi untuk field yang mungkin berubah required-nya
             $('#nip_admin_edit').valid();
             $('#nip_dosen_edit').valid();
             $('#bidang_keahlian_edit').valid();
             $('#nim_edit').valid();
             $('#prodi_id_edit').valid();
             $('#periode_id_edit').valid();
             $('#bidang_minat_edit').valid();
             $('#keahlian_mahasiswa_edit').valid();
        }

        // Panggil fungsi toggle saat dokumen ready (setelah konten modal dimuat)
        toggleAdditionalFormsModalEdit();


        // Inisialisasi jQuery Validation untuk FORM DI DALAM MODAL
        formEdit.validate({
             ignore: ":hidden", // Abaikan field yang disembunyikan
            rules: {
                nama: { required: true, minlength: 3 },
                email: { required: true, email: true },
                // Password tidak required untuk edit, hanya jika diisi
                password: { minlength: 6 },
                // Role tidak divalidasi di sini karena disabled, tapi di backend
                status: { required: true },
                // Aturan required untuk field tambahan diatur dinamis oleh toggleAdditionalFormsModalEdit
                nip_admin: { digits: true },
                nip_dosen: { digits: true },
                bidang_keahlian: {},
                nim: { digits: true },
                prodi_id: {},
                periode_id: {},
                bidang_minat: {},
                keahlian_mahasiswa: {}
            },
            messages: {
                nama: { required: "Nama tidak boleh kosong", minlength: "Nama minimal harus 3 karakter" },
                email: { required: "Email tidak boleh kosong", email: "Format email tidak valid" },
                password: { minlength: "Password minimal harus 6 karakter" },
                status: "Silakan pilih status",
                nip_admin: { required: "NIP wajib diisi untuk role Admin", digits: "NIP hanya boleh berisi angka" },
                nip_dosen: { required: "NIP wajib diisi untuk role Dosen", digits: "NIP hanya boleh berisi angka" },
                bidang_keahlian: { required: "Bidang keahlian wajib diisi untuk Dosen" },
                nim: { required: "NIM wajib diisi untuk role Mahasiswa", digits: "NIM hanya boleh berisi angka" },
                prodi_id: { required: "Prodi wajib diisi untuk Mahasiswa" },
                periode_id: { required: "Periode wajib diisi untuk Mahasiswa" },
                bidang_minat: { required: "Bidang Minat wajib diisi untuk Mahasiswa" },
                keahlian_mahasiswa: { required: "Keahlian mahasiswa wajib diisi untuk Mahasiswa" }
            },

            // --- AJAX Submission ---
            submitHandler: function(form) {
                // Reset tampilan error sebelum submit
                $('.error-text').text('');
                $('.is-invalid').removeClass('is-invalid');

                const formUrl = $(form).attr('action');
                const formMethod = $(form).attr('method'); // Akan jadi PUT
                const formData = $(form).serialize();
                const csrfToken = $('meta[name="csrf-token"]').attr('content'); // Ambil CSRF token dari meta tag

                console.log("Attempting AJAX submission for Edit:");
                console.log("URL:", formUrl);
                console.log("Method:", formMethod); // Should be PUT
                console.log("Data:", formData);
                console.log("CSRF Token:", csrfToken);


                $.ajax({
                    url: formUrl,
                    method: formMethod, // Akan jadi PUT
                    data: formData,
                    dataType: 'json', // Harapkan response JSON dari server
                    headers: { // Tambahkan header kustom
                        'X-CSRF-TOKEN': csrfToken, // Kirim CSRF token di header
                        'X-Requested-With': 'XMLHttpRequest' // Explicitly set X-Requested-With
                    },
                    beforeSend: function(xhr) {
                        $(form).find('button[type="submit"]').prop('disabled', true).text('Menyimpan...');
                         console.log("AJAX beforeSend. Check Network tab for request details, especially Headers.");
                    },
                    success: function(response) {
                        console.log("AJAX Success:", response); // Log success response
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
                        if (typeof dataUser !== 'undefined' && dataUser.ajax) {
                            dataUser.ajax.reload();
                        } else {
                            console.error("DataTable object 'dataUser' not found or misconfigured.");
                             window.location.reload(); // Fallback page reload
                        }

                         // Reset form setelah sukses (opsional untuk edit, tapi bisa membantu)
                         // form.reset(); // Mungkin tidak perlu reset field setelah edit berhasil
                         formEdit.validate().resetForm(); // Reset validasi
                         formEdit.find('.is-invalid').removeClass('is-invalid');
                         formEdit.find('.is-valid').removeClass('is-valid');
                         // toggleAdditionalFormsModalEdit(); // Tidak perlu dipanggil setelah sukses
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", xhr, status, error); // Log error details
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
                             errorMessage = 'Error: ' + xhr.status + ' ' + xhr.statusText + '. Cek console browser untuk detail.';
                             console.error("AJAX Error Response:", xhr.responseText);
                         }

                         // Tampilkan pesan error validasi di samping field
                         $('.error-text').text('');
                         $('.form-control, .form-select').removeClass('is-invalid').removeClass('is-valid');
                         $.each(errors, function(key, value) {
                             // Perhatikan penamaan field di backend vs frontend
                             // Jika backend mengembalikan error untuk 'nip', tapi frontend pakai 'nip_admin', sesuaikan ID span error
                             let errorFieldId = key;
                             // Contoh penyesuaian ID span error jika nama field di backend berbeda dengan ID di frontend
                             if (key === 'nip' && formEdit.find('input[name="role"]').val() === 'admin') errorFieldId = 'nip_admin';
                             if (key === 'nip' && formEdit.find('input[name="role"]').val() === 'dosen') errorFieldId = 'nip_dosen';
                              if (key === 'bidang_keahlian' && formEdit.find('input[name="role"]').val() === 'dosen') errorFieldId = 'bidang_keahlian_edit'; // Perbaiki ID jika beda
                              if (key === 'nim' && formEdit.find('input[name="role"]').val() === 'mahasiswa') errorFieldId = 'nim_edit'; // Perbaiki ID jika beda
                              if (key === 'prodi_id' && formEdit.find('input[name="role"]').val() === 'mahasiswa') errorFieldId = 'prodi_id_edit'; // Perbaiki ID jika beda
                              if (key === 'periode_id' && formEdit.find('input[name="role"]').val() === 'mahasiswa') errorFieldId = 'periode_id_edit'; // Perbaiki ID jika beda
                              if (key === 'bidang_minat' && formEdit.find('input[name="role"]').val() === 'mahasiswa') errorFieldId = 'bidang_minat_edit'; // Perbaiki ID jika beda
                              if (key === 'keahlian_mahasiswa' && formEdit.find('input[name="role"]').val() === 'mahasiswa') errorFieldId = 'keahlian_mahasiswa_edit'; // Perbaiki ID jika beda


                             $('#error-' + errorFieldId).text(value[0]).show();
                             $('#' + errorFieldId).addClass('is-invalid');
                         });

                         // Tampilkan SweetAlert untuk error umum
                        if (Object.keys(errors).length === 0 || (xhr.responseJSON && xhr.responseJSON.message && Object.keys(errors).length > 0)) {
                             if (typeof Swal !== 'undefined') {
                                 Swal.fire({
                                     icon: 'error',
                                     title: 'Oops...',
                                     text: errorMessage,
                                 });
                             } else {
                                 alert(errorMessage);
                             }
                         }
                    }
                });
                return false; // Prevent default form submission
            },
            // --- Validasi Styling ---
            errorElement: 'span',
            errorPlacement: function (error, element) {
                let errorSpanId = element.attr('name');
                 // Sesuaikan ID span error untuk field yang namanya bisa sama (nip)
                 if (errorSpanId === 'nip' && $('#formEditUser input[name="role"]').val() === 'admin') errorSpanId = 'nip_admin';
                 if (errorSpanId === 'nip' && $('#formEditUser input[name="role"]').val() === 'dosen') errorSpanId = 'nip_dosen';
                 // Tambahkan penyesuaian lain jika ada perbedaan nama field/ID span
                 if (errorSpanId === 'bidang_keahlian') errorSpanId = 'bidang_keahlian_edit';
                 if (errorSpanId === 'nim') errorSpanId = 'nim_edit';
                 if (errorSpanId === 'prodi_id') errorSpanId = 'prodi_id_edit';
                 if (errorSpanId === 'periode_id') errorSpanId = 'periode_id_edit';
                 if (errorSpanId === 'bidang_minat') errorSpanId = 'bidang_minat_edit';
                 if (errorSpanId === 'keahlian_mahasiswa') errorSpanId = 'keahlian_mahasiswa_edit';


                let errorSpan = $('#error-' + errorSpanId);
                if (errorSpan.length) {
                    errorSpan.text(error.text()).show();
                } else {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                     console.warn("Missing span.error-text for field:", element.attr('name'), "or incorrect ID mapping.");
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
                $(element).removeClass('is-valid');
                 let errorSpanId = $(element).attr('name');
                  if (errorSpanId === 'nip' && $('#formEditUser input[name="role"]').val() === 'admin') errorSpanId = 'nip_admin';
                  if (errorSpanId === 'nip' && $('#formEditUser input[name="role"]').val() === 'dosen') errorSpanId = 'nip_dosen';
                  if (errorSpanId === 'bidang_keahlian') errorSpanId = 'bidang_keahlian_edit';
                  if (errorSpanId === 'nim') errorSpanId = 'nim_edit';
                  if (errorSpanId === 'prodi_id') errorSpanId = 'prodi_id_edit';
                  if (errorSpanId === 'periode_id') errorSpanId = 'periode_id_edit';
                  if (errorSpanId === 'bidang_minat') errorSpanId = 'bidang_minat_edit';
                  if (errorSpanId === 'keahlian_mahasiswa') errorSpanId = 'keahlian_mahasiswa_edit';

                 $('#error-' + errorSpanId).show();
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
                $(element).addClass('is-valid');
                 let errorSpanId = $(element).attr('name');
                  if (errorSpanId === 'nip' && $('#formEditUser input[name="role"]').val() === 'admin') errorSpanId = 'nip_admin';
                  if (errorSpanId === 'nip' && $('#formEditUser input[name="role"]').val() === 'dosen') errorSpanId = 'nip_dosen';
                   if (errorSpanId === 'bidang_keahlian') errorSpanId = 'bidang_keahlian_edit';
                   if (errorSpanId === 'nim') errorSpanId = 'nim_edit';
                   if (errorSpanId === 'prodi_id') errorSpanId = 'prodi_id_edit';
                   if (errorSpanId === 'periode_id') errorSpanId = 'periode_id_edit';
                   if (errorSpanId === 'bidang_minat') errorSpanId = 'bidang_minat_edit';
                   if (errorSpanId === 'keahlian_mahasiswa') errorSpanId = 'keahlian_mahasiswa_edit';

                 $('#error-' + errorSpanId).text('').hide();
            }
        });

         // Panggil toggleAdditionalFormsModalEdit sekali saat konten dimuat untuk mengatur state awal
         toggleAdditionalFormsModalEdit();

    }); // End document ready
</script>
