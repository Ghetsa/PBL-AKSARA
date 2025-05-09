{{-- resources/views/user/edit_ajax.blade.php --}}
<form id="formEditUser" class="form-horizontal" method="POST" action="{{ route('user.update_ajax', $user->user_id) }}">
    @csrf
    @method('PUT') {{-- Penting untuk method update --}}

    <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Edit User: {{ $user->nama }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body">
        {{-- Role --}}
        <div class="form-group row mb-3">
            <label for="edit_role_modal" class="col-sm-2 col-form-label">Role</label>
            <div class="col-sm-10">
                <select class="form-select" id="edit_role_modal" name="role" required>
                    <option value="">- Pilih Role -</option>
                    @foreach($roles as $roleValue)
                        <option value="{{ $roleValue }}" {{ $user->role == $roleValue ? 'selected' : '' }}>
                            {{ ucfirst($roleValue) }}
                        </option>
                    @endforeach
                </select>
                <span class="invalid-feedback error-text" id="error-edit-role"></span>
            </div>
        </div>

        {{-- Nama --}}
        <div class="form-group row mb-3">
            <label for="edit_nama" class="col-sm-2 col-form-label">Nama</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="edit_nama" name="nama" value="{{ old('nama', $user->nama) }}" required>
                <span class="invalid-feedback error-text" id="error-edit-nama"></span>
            </div>
        </div>

        {{-- Email --}}
        <div class="form-group row mb-3">
            <label for="edit_email" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="edit_email" name="email" value="{{ old('email', $user->email) }}" required>
                <span class="invalid-feedback error-text" id="error-edit-email"></span>
            </div>
        </div>

        {{-- Password --}}
        <div class="form-group row mb-3">
            <label for="edit_password" class="col-sm-2 col-form-label">Password</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" id="edit_password" name="password">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                <span class="invalid-feedback error-text" id="error-edit-password"></span>
            </div>
        </div>

        {{-- === Field Tambahan (sesuaikan ID dan value) === --}}
        {{-- NIP (Untuk Admin & Dosen) --}}
        <div id="form-edit-nip-modal" style="display: none;">
            <div class="form-group row mb-3">
                <label for="edit_nip" class="col-sm-2 col-form-label">NIP</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="edit_nip" name="nip" value="{{ old('nip', $user->admin->nip ?? $user->dosen->nip ?? '') }}">
                    <span class="invalid-feedback error-text" id="error-edit-nip"></span>
                </div>
            </div>
        </div>

        {{-- Bidang Keahlian (Untuk Dosen) --}}
        <div id="form-edit-keahlian-modal" style="display: none;">
            <div class="form-group row mb-3">
                <label for="edit_bidang_keahlian" class="col-sm-2 col-form-label">Bidang Keahlian</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="edit_bidang_keahlian" name="bidang_keahlian" value="{{ old('bidang_keahlian', $user->dosen->bidang_keahlian ?? '') }}">
                    <span class="invalid-feedback error-text" id="error-edit-bidang_keahlian"></span>
                </div>
            </div>
        </div>

        {{-- NIM (Untuk Mahasiswa) --}}
        <div id="form-edit-nim-modal" style="display: none;">
            <div class="form-group row mb-3">
                <label for="edit_nim" class="col-sm-2 col-form-label">NIM</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="edit_nim" name="nim" value="{{ old('nim', $user->mahasiswa->nim ?? '') }}">
                    <span class="invalid-feedback error-text" id="error-edit-nim"></span>
                </div>
            </div>
        </div>

        {{-- Prodi (Untuk Mahasiswa) --}}
        <div id="form-edit-prodi_id-modal" style="display: none;">
            <div class="form-group row mb-3">
                <label for="edit_prodi_id" class="col-sm-2 col-form-label">Prodi</label>
                <div class="col-sm-10">
                    <select class="form-select" id="edit_prodi_id" name="prodi_id">
                        <option value="">- Pilih Prodi -</option>
                        @foreach($prodi as $item)
                            <option value="{{ $item->prodi_id }}" {{ (old('prodi_id', $user->mahasiswa->prodi_id ?? '') == $item->prodi_id) ? 'selected' : '' }}>
                                {{ $item->nama }}
                            </option>
                        @endforeach
                    </select>
                    <span class="invalid-feedback error-text" id="error-edit-prodi_id"></span>
                </div>
            </div>
        </div>

        {{-- Periode (Untuk Mahasiswa) --}}
        <div id="form-edit-periode_id-modal" style="display: none;">
            <div class="form-group row mb-3">
                <label for="edit_periode_id" class="col-sm-2 col-form-label">Periode</label>
                <div class="col-sm-10">
                    <select class="form-select" id="edit_periode_id" name="periode_id">
                        <option value="">- Pilih Periode -</option>
                        @foreach($periode as $item)
                            <option value="{{ $item->periode_id }}" {{ (old('periode_id', $user->mahasiswa->periode_id ?? '') == $item->periode_id) ? 'selected' : '' }}>
                                {{ $item->tahun_akademik }} / {{ $item->semester }}
                            </option>
                        @endforeach
                    </select>
                    <span class="invalid-feedback error-text" id="error-edit-periode_id"></span>
                </div>
            </div>
        </div>

        {{-- Bidang Minat (Untuk Mahasiswa) --}}
        <div id="form-edit-bidang_minat-modal" style="display: none;">
            <div class="form-group row mb-3">
                <label for="edit_bidang_minat" class="col-sm-2 col-form-label">Bidang Minat</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="edit_bidang_minat" name="bidang_minat" value="{{ old('bidang_minat', $user->mahasiswa->bidang_minat ?? '') }}">
                    <span class="invalid-feedback error-text" id="error-edit-bidang_minat"></span>
                </div>
            </div>
        </div>

        {{-- Keahlian Mahasiswa (Untuk Mahasiswa) --}}
        <div id="form-edit-keahlian-mahasiswa-modal" style="display: none;">
            <div class="form-group row mb-3">
                <label for="edit_keahlian_mahasiswa" class="col-sm-2 col-form-label">Keahlian</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="edit_keahlian_mahasiswa" name="keahlian_mahasiswa" value="{{ old('keahlian_mahasiswa', $user->mahasiswa->keahlian_mahasiswa ?? '') }}">
                    <span class="invalid-feedback error-text" id="error-edit-keahlian_mahasiswa"></span>
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="form-group row mb-3">
            <label for="edit_status" class="col-sm-2 col-form-label">Status</label>
            <div class="col-sm-10">
                <select class="form-select" id="edit_status" name="status" required>
                    <option value="">- Pilih Status -</option>
                    <option value="aktif" {{ (old('status', $user->status) == 'aktif') ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ (old('status', $user->status) == 'nonaktif') ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <span class="invalid-feedback error-text" id="error-edit-status"></span>
            </div>
        </div>
    </div> {{-- Akhir modal-body --}}

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>

<script>
    $(document).ready(function () {
        const formEditUser = $('#formEditUser');
    
        function toggleEditAdditionalForms() {
            const role = $('#edit_role_modal').val();
            // Sembunyikan semua field tambahan dulu & reset required
            $('#form-edit-nip-modal, #form-edit-keahlian-modal, #form-edit-nim-modal, #form-edit-prodi_id-modal, #form-edit-periode_id-modal, #form-edit-bidang_minat-modal, #form-edit-keahlian_mahasiswa-modal').hide();
            $('#edit_nip, #edit_bidang_keahlian, #edit_nim, #edit_prodi_id, #edit_periode_id, #edit_bidang_minat, #edit_keahlian_mahasiswa')
                .prop('required', false)
                .removeClass('is-invalid')
                .next('.invalid-feedback').text('');
    
            if (role === 'admin') {
                $('#form-edit-nip-modal').show();
                $('#edit_nip').prop('required', true);
            } else if (role === 'dosen') {
                $('#form-edit-nip-modal').show();
                $('#form-edit-keahlian-modal').show();
                $('#edit_nip').prop('required', true);
                $('#edit_bidang_keahlian').prop('required', true);
            } else if (role === 'mahasiswa') {
                $('#form-edit-nim-modal').show();
                $('#form-edit-prodi_id-modal').show();
                $('#form-edit-periode_id-modal').show();
                $('#form-edit-bidang_minat-modal').show();
                $('#form-edit-keahlian_mahasiswa-modal').show();
                $('#edit_nim').prop('required', true);
                $('#edit_prodi_id').prop('required', true);
                $('#edit_periode_id').prop('required', true);
                $('#edit_bidang_minat').prop('required', false); 
                $('#edit_keahlian_mahasiswa').prop('required', false); 
            }
    
            // Update validasi jQuery HANYA JIKA validator sudah ada
            if (formEditUser.data('validator')) { // Pengecekan apakah validator sudah diinisialisasi
                formEditUser.validate().settings.ignore = ":hidden";
                $('#edit_role_modal, #edit_nip, #edit_bidang_keahlian, #edit_nim, #edit_prodi_id, #edit_periode_id, #edit_bidang_minat, #edit_keahlian_mahasiswa').valid();
            }
        }
    
        $('#edit_role_modal').on('change', toggleEditAdditionalForms);
    
        // ---------------------------------------------------------------------------
        // HAPUS PANGGILAN toggleEditAdditionalForms() DARI SINI:
        // toggleEditAdditionalForms(); // <<-- BARIS INI YANG MENYEBABKAN MASALAH JIKA SEBELUM .validate()
        // ---------------------------------------------------------------------------
    
        formEditUser.validate({
            ignore: ":hidden",
            rules: {
                nama: { required: true, minlength: 3 },
                email: { required: true, email: true },
                password: { minlength: 6 }, // Tidak required
                role: { required: true },
                status: { required: true },
                nip: { digits: true }, // required diatur dinamis oleh toggleEditAdditionalForms
                nim: { digits: true }, // required diatur dinamis
                // bidang_keahlian, prodi_id, periode_id, dll. akan divalidasi berdasarkan prop 'required'
            },
            messages: {
                nama: { required: "Nama tidak boleh kosong", minlength: "Nama minimal 3 karakter" },
                email: { required: "Email tidak boleh kosong", email: "Format email tidak valid" },
                password: { minlength: "Password minimal 6 karakter" },
                role: "Silakan pilih role",
                status: "Silakan pilih status",
                nip: { required: "NIP wajib diisi", digits: "NIP hanya boleh angka" },
                bidang_keahlian: { required: "Bidang keahlian wajib diisi" },
                nim: { required: "NIM wajib diisi", digits: "NIM hanya boleh angka" },
                prodi_id: { required: "Prodi wajib diisi" },
                periode_id: { required: "Periode wajib diisi" },
                bidang_minat: { required: "Bidang Minat wajib diisi" },
                keahlian_mahasiswa: { required: "Keahlian mahasiswa wajib diisi" }
            },
            submitHandler: function (form, event) { // Tambahkan parameter 'event'
                event.preventDefault(); // <-- PENTING: Cegah submit form standar secara eksplisit
                
                console.log("SCRIPT EDIT USER: submitHandler (AJAX) dieksekusi!");
                $('.error-text').text('');
                $('.is-invalid').removeClass('is-invalid');
    
                $.ajax({
                    url: $(form).attr('action'),
                    method: 'POST',
                    data: $(form).serialize(),
                    dataType: 'json',
                    beforeSend: function () {
                        $(form).find('button[type="submit"]').prop('disabled', true).text('Menyimpan...');
                    },
                    success: function (response) {
                        if (response.status) {
                            $("#myModal").modal('hide');
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message });
                            if (typeof dataUser !== 'undefined' && dataUser.ajax) {
                                dataUser.ajax.reload(null, false);
                            } else {
                                window.location.reload(); 
                            }
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: response.message || 'Gagal memperbarui data.' });
                            if (response.errors) {
                                $.each(response.errors, function (key, value) {
                                    $('#error-edit-' + key).text(value[0]).show();
                                    $('#edit_' + key).addClass('is-invalid');
                                });
                            }
                        }
                    },
                    error: function (xhr) {
                        $(form).find('button[type="submit"]').prop('disabled', false).text('Simpan Perubahan');
                        let errorMessage = 'Terjadi kesalahan server.';
                        let errors = {};
                        if (xhr.responseJSON) {
                            errorMessage = xhr.responseJSON.message || errorMessage;
                            errors = xhr.responseJSON.errors || {};
                        } else {
                            console.error("AJAX Error:", xhr.responseText);
                        }

                        $('.error-text').text('');
                        $('.form-control, .form-select').removeClass('is-invalid');
                        $.each(errors, function (key, value) {
                            $('#error-edit-' + key).text(value[0]).show();
                            $('#edit_' + key).addClass('is-invalid');
                        });

                        if (Object.keys(errors).length === 0 && xhr.status !== 422) { // Jangan tampilkan swal umum jika ada error field
                            Swal.fire({ icon: 'error', title: 'Oops...', text: errorMessage });
                        }
                    },
                    complete: function() {
                        $(form).find('button[type="submit"]').prop('disabled', false).text('Simpan Perubahan');
                    }
                });
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                let errorSpan = $('#error-edit-' + element.attr('name'));
                if (errorSpan.length) {
                    errorSpan.text(error.text()).show();
                } else {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                }
            },
            highlight: function (element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
                $('#error-edit-' + $(element).attr('name')).show();
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
                $('#error-edit-' + $(element).attr('name')).text('').hide();
            }
        });
    
        // Panggil toggleAdditionalForms SETELAH inisialisasi .validate() utama
        // untuk mengatur tampilan awal field berdasarkan role yang sudah ada.
        if ($('#edit_role_modal').val()) {
            toggleEditAdditionalForms();
        }
    });
</script>