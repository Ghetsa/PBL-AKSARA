{{-- profil/edit.blade.php --}}
<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <form id="formUpdateProfile" method="POST" action="{{ route('profile.update_ajax') }}"
            enctype="multipart/form-data">
            @csrf
            {{-- HAPUS BARIS INI: @method('PUT') --}}

            <div class="modal-header">
                <h5 class="modal-title">Perbarui Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">

                {{-- Bagian Info Dasar --}}
                <div id="section-dasar">
                    <h5><i class="ti ti-id me-2"></i>Info Dasar</h5>
                    <hr class="mt-1 mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nama" class="form-label">Nama Lengkap <span
                                        class="text-danger"></span></label>
                                <input type="text" name="nama" id="nama" class="form-control"
                                    value="{{ old('nama', $user->nama) }}" required>
                                <span class="invalid-feedback error-nama"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email <span
                                        class="text-danger"></span></label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ old('email', $user->email) }}" required>
                                <span class="invalid-feedback error-email"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <input type="text" name="alamat" id="alamat" class="form-control"
                                    value="{{ old('alamat', $user->alamat ?? '') }}">
                                <span class="invalid-feedback error-alamat"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="no_telepon" class="form-label">Nomor Telepon</label>
                                <input type="text" name="no_telepon" id="no_telepon" class="form-control"
                                    value="{{ old('no_telepon', $user->no_telepon ?? '') }}">
                                <span class="invalid-feedback error-no_telepon"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_foto" class="form-label">Foto Profil</label>
                        <input type="file" name="foto" id="edit_foto" class="form-control">
                        @if($user->foto && Storage::disk('public')->exists($user->foto))
                            <small class="form-text text-muted mt-1">
                                Foto saat ini: <a href="{{ Storage::url($user->foto) }}" target="_blank">lihat foto</a>.
                                <br>Kosongkan jika tidak ingin mengubah.
                            </small>
                        @elseif($user->foto)
                            <small class="form-text text-danger mt-1">File foto profil sebelumnya tidak ditemukan.</small>
                        @endif
                        <span class="invalid-feedback error-foto"></span>
                    </div>

                </div>
                <hr class="my-4">

                {{-- Bagian Minat --}}
                @if ($user->role === 'dosen' || $user->role === 'mahasiswa')
                    <div id="section-minat">
                        <h5><i class="ti ti-heart me-2"></i> Minat</h5>
                        <hr class="mt-1 mb-3">
                        <div class="row">
                            @foreach ($allBidangOptions as $bidang)
                                @php
                                    $inputIdSlug = Str::slug($bidang->bidang_nama);
                                    $userMinatIni = $userMinatData->get($bidang->bidang_id);
                                    $isMinatChecked = $userMinatIni !== null;
                                    $currentLevel = $isMinatChecked ? $userMinatIni['level'] : 'minat';
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <div class="card shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input minat-checkbox" type="checkbox"
                                                    name="minat_pilihan[]" value="{{ $bidang->bidang_id }}"
                                                    id="minat_{{ $inputIdSlug }}" {{ $isMinatChecked ? 'checked' : '' }}
                                                    data-bidang-id="{{ $bidang->bidang_id }}">
                                                <label class="form-check-label" for="minat_{{ $inputIdSlug }}">
                                                    {{ $bidang->bidang_nama }}
                                                </label>
                                            </div>
                                            <div class="ms-1 minat-level-container"
                                                id="minat_level_container_{{ $bidang->bidang_id }}"
                                                style="{{ $isMinatChecked ? '' : 'display:none;' }}">
                                                <label for="minat_level_{{ $bidang->bidang_id }}"
                                                    class="form-label small mb-1">Level Minat:</label>
                                                <select class="form-select form-select-sm"
                                                    name="minat_level[{{ $bidang->bidang_id }}]"
                                                    id="minat_level_{{ $bidang->bidang_id }}">
                                                    @foreach($minatLevelOptions as $value => $label)
                                                        <option value="{{ $value }}" {{ $currentLevel == $value ? 'selected' : '' }}>
                                                            {{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <span class="invalid-feedback error-minat_pilihan"></span>
                    </div>
                    <hr class="my-4">
                @endif

                {{-- Bagian Pengalaman --}}
                @if ($user->role === 'mahasiswa' || $user->role === 'dosen')
                    <div id="section-pengalaman" class="mt-3">
                        <h5><i class="ti ti-briefcase me-2"></i>Pengalaman</h5>
                        <hr class="mt-1 mb-3">
                        <div id="pengalaman-fields-container">
                            @forelse ($selectedPengalaman as $index => $pengalaman)
                                <div class="pengalaman-item border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn mb-2"><i
                                                class="ti ti-trash"></i></button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Nama Pengalaman/Posisi <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="pengalaman_items[{{ $index }}][pengalaman_nama]"
                                                class="form-control pengalaman-nama-input" value="{{ $pengalaman->pengalaman_nama }}" required>
                                            <span class="invalid-feedback error-pengalaman_items.{{$index}}.pengalaman_nama"></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Kategori</label>
                                            <select name="pengalaman_items[{{ $index }}][pengalaman_kategori]"
                                                class="form-control">
                                                <option value="">-- Pilih Kategori --</option>
                                                <option value="Workshop" {{ old('pengalaman_kategori', $pengalaman->pengalaman_kategori ?? '') == 'Workshop' ? 'selected' : '' }}>
                                                    Workshop</option>
                                                <option value="Magang" {{ old('pengalaman_kategori', $pengalaman->pengalaman_kategori ?? '') == 'Magang' ? 'selected' : '' }}>
                                                    Magang</option>
                                                <option value="Proyek" {{ old('pengalaman_kategori', $pengalaman->pengalaman_kategori ?? '') == 'Proyek' ? 'selected' : '' }}>
                                                    Proyek</option>
                                                <option value="Organisasi" {{ old('pengalaman_kategori', $pengalaman->pengalaman_kategori ?? '') == 'Organisasi' ? 'selected' : '' }}>
                                                    Organisasi</option>
                                                <option value="Pekerjaan" {{ old('pengalaman_kategori', $pengalaman->pengalaman_kategori ?? '') == 'Pekerjaan' ? 'selected' : '' }}>
                                                    Pekerjaan</option>
                                            </select>
                                            <span class="invalid-feedback error-pengalaman_items.{{$index}}.pengalaman_kategori"></span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="pengalaman-item border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn mb-2"
                                            style="display:none;"><i class="ti ti-trash"></i></button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Nama Pengalaman/Posisi <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="pengalaman_items[0][pengalaman_nama]" class="form-control pengalaman-nama-input" required>
                                            <span class="invalid-feedback error-pengalaman_items.0.pengalaman_nama"></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Kategori</label>
                                            <select name="pengalaman_items[0][pengalaman_kategori]" class="form-control">
                                                <option value="">-- Pilih Kategori --</option>
                                                <option value="Workshop">Workshop</option>
                                                <option value="Magang">Magang</option>
                                                <option value="Proyek">Proyek</option>
                                                <option value="Organisasi">Organisasi</option>
                                                <option value="Pekerjaan">Pekerjaan</option>
                                            </select>
                                            <span class="invalid-feedback error-pengalaman_items.0.pengalaman_kategori"></span>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" id="add-pengalaman-btn" class="btn btn-sm btn-outline-primary mt-2"><i
                                class="ti ti-plus"></i> Tambah Pengalaman</button>
                    </div>
                @endif

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" id="submitUpdateProfile">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

<script>
    $(document).ready(function () {
        // Logika untuk menampilkan/menyembunyikan level minat (tetap sama)
        $('.minat-checkbox').each(function () {
            var $checkbox = $(this);
            var bidangId = $checkbox.data('bidang-id');
            var $levelContainer = $('#minat_level_container_' + bidangId);

            if (!$checkbox.is(':checked')) {
                $levelContainer.hide();
            }
            $checkbox.on('change', function () {
                if ($(this).is(':checked')) {
                    $levelContainer.slideDown();
                } else {
                    $levelContainer.slideUp();
                }
            });
        });

        // Logika untuk Tambah/Hapus Pengalaman (tetap sama, dengan sedikit penyesuaian)
        function updatePengalamanRemoveButtons() {
            const itemCount = $('#pengalaman-fields-container .pengalaman-item').length;
            $('#pengalaman-fields-container .pengalaman-item').each(function (index) {
                const $currentItem = $(this);
                const isNameFieldEmpty = $currentItem.find('input[name$="[pengalaman_nama]"]').val() === '';

                if (itemCount > 1 || (itemCount === 1 && !isNameFieldEmpty)) {
                    $currentItem.find('.remove-item-btn').show();
                } else {
                    $currentItem.find('.remove-item-btn').hide();
                }
            });
            if (itemCount === 0) {
                $('#add-pengalaman-btn').click();
            }
        }

        $('#add-pengalaman-btn').click(function () {
            const newIndex = Date.now();
            const newItemHtml = `
            <div class="pengalaman-item border rounded p-3 mb-3">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn mb-2"><i class="ti ti-trash"></i></button>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Nama Pengalaman/Posisi <span class="text-danger">*</span></label>
                        <input type="text" name="pengalaman_items[${newIndex}][pengalaman_nama]" class="form-control pengalaman-nama-input" required>
                        <span class="invalid-feedback error-pengalaman_items.${newIndex}.pengalaman_nama"></span>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Kategori</label>
                        <select name="pengalaman_items[${newIndex}][pengalaman_kategori]" class="form-control">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Workshop">Workshop</option>
                            <option value="Magang">Magang</option>
                            <option value="Proyek">Proyek</option>
                            <option value="Organisasi">Organisasi</option>
                            <option value="Pekerjaan">Pekerjaan</option>
                        </select>
                        <span class="invalid-feedback error-pengalaman_items.${newIndex}.pengalaman_kategori"></span>
                    </div>
                </div>
            </div>`;
            $('#pengalaman-fields-container').append(newItemHtml);
            
            // Daftarkan rule validasi untuk elemen baru yang ditambahkan
             $(`[name="pengalaman_items[${newIndex}][pengalaman_nama]"]`).rules('add', {
                required: true,
                messages: {
                    required: "Nama pengalaman wajib diisi."
                }
            });

            updatePengalamanRemoveButtons();
        });

        $('body').on('click', '#pengalaman-fields-container .remove-item-btn', function () {
            $(this).closest('.pengalaman-item').remove();
            updatePengalamanRemoveButtons();
        });
        
        updatePengalamanRemoveButtons();


        // =================================================================
        // IMPLEMENTASI JQUERY VALIDATION
        // =================================================================
        $('#formUpdateProfile').validate({
            // 1. Tentukan Rules (Aturan Validasi)
            rules: {
                nama: {
                    required: true,
                    maxlength: 50,
                    lettersonly: true
                },
                email: {
                    required: true,
                    email: true,
                    maxlength: 50
                },
                no_telepon: {
                    required: true,
                    digits: true,
                    maxlength: 15
                },
                alamat: {
                    required: true,
                    maxlength: 100
                },
                foto: {
                    accept: "image/jpeg, image/png, image/jpg, image/gif",
                    filesize: 2048000 // 2MB in bytes
                },
                // Validasi untuk input dinamis "pengalaman" ditangani dengan menambahkan atribut 'required' langsung di HTML
            },

            // 2. Tentukan Pesan Error Kustom
            messages: {
                nama: {
                    required: "Nama lengkap tidak boleh kosong.",
                    maxlength: "Nama tidak boleh lebih dari 50 karakter.",
                    lettersonly: "Nama hanya boleh berisi huruf."
                },
                email: {
                    required: "Email tidak boleh kosong.",
                    email: "Format email tidak valid.",
                    maxlength: "Email terlalu panjang."
                },
                no_telepon: {
                    required: "Nomor telepon tidak boleh kosong.",
                    digits: "Nomor telepon hanya boleh berisi angka.",
                    maxlength: "Nomor telepon tidak boleh lebih dari 15 karakter."
                },
                alamat: {
                    required: "Alamat tidak boleh kosong.",
                    maxlength: "Alamat tidak boleh lebih dari 100 karakter."
                },
                foto: {
                    accept: "Format file harus berupa gambar (jpg, jpeg, png, gif).",
                    filesize: "Ukuran file tidak boleh lebih dari 2MB."
                }
            },

            // 3. Atur Penempatan dan Tampilan Error agar Sesuai Bootstrap 5
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                // Untuk input file, tempatkan error di siblingnya
                if (element.prop('type') === 'file') {
                    element.closest('.form-group').find('.invalid-feedback').html(error);
                } else {
                    element.closest('.form-group').append(error);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },

            // 4. Submit Handler: Kode yang dijalankan HANYA JIKA form valid
            submitHandler: function (form) {
                let formData = new FormData(form);
                const submitButton = $(form).find('button[type="submit"]');
                const originalButtonText = submitButton.html();
                
                submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan…');
                
                // Hapus pesan error lama dari validasi server (jika ada)
                $('.invalid-feedback').text('').hide();
                $('input, select, textarea').removeClass('is-invalid');

                // AJAX call yang sudah ada sebelumnya dipindahkan ke sini
                $.ajax({
                    url: $(form).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        let modalElement = $(form).closest('.modal');
                        modalElement.modal('hide');
                        setTimeout(function () {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses!',
                                text: response.message,
                            }).then(() => {
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                } else {
                                    location.reload();
                                }
                            });
                        }, 300);
                    },
                    error: function (xhr) { 
                        // Jika validasi client lolos tapi server menolak, tampilkan error dari server
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, messages) {
                                let nameAttributeForSelector = key.replace(/\.(\d+)\.(.+)/g, '[$1][$2]');
                                if (!nameAttributeForSelector.includes('[')) {
                                    nameAttributeForSelector = key;
                                } else {
                                    let parts = key.split('.');
                                    let baseName = parts.shift();
                                    nameAttributeForSelector = baseName + '[' + parts.join('][') + ']';
                                }
                                let inputElement = $(form).find(`[name="${nameAttributeForSelector}"]`);
                                if (inputElement.length) {
                                    inputElement.addClass('is-invalid');
                                    let errorContainer = inputElement.closest('.form-group, .mb-2, .mb-3').find('.invalid-feedback');
                                    errorContainer.text(messages[0]).show();
                                }
                            });
                            Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: 'Periksa kembali isian Anda.' });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan server.'
                            });
                        }
                    },
                    complete: function () {
                        submitButton.prop('disabled', false).html(originalButtonText);
                    }
                });
            }
        });

        // Tambahkan rule untuk validasi ukuran file
        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param);
        }, 'Ukuran file melebihi batas.');

    });
</script>