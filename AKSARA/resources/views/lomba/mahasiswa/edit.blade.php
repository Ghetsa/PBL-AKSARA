<form id="formUserEditLomba" action="{{ route('lomba.mhs.update_form', $lomba->lomba_id) }}" {{-- Sesuaikan route jika untuk dosen juga --}}
    method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="modal-header">
        <h5 class="modal-title"><i class="ti ti-edit-circle me-2"></i>Edit Pengajuan Info Lomba</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body" style="max-height: 68vh; overflow-y: auto;">
        <div class="row">
            {{-- Nama Lomba --}}
            <div class="col-md-12 mb-3">
                <label for="user_edit_nama_lomba" class="form-label">Nama Lomba</label>
                <input type="text" name="nama_lomba" id="user_edit_nama_lomba" class="form-control form-control-sm" value="{{ old('nama_lomba', $lomba->nama_lomba) }}">
                <span class="invalid-feedback error-nama_lomba"></span>
            </div>

            {{-- Tanggal Pendaftaran --}}
            <div class="col-md-6 mb-3">
                <label for="user_edit_pembukaan_pendaftaran" class="form-label">Pembukaan Pendaftaran</label>
                <input type="date" name="pembukaan_pendaftaran" id="user_edit_pembukaan_pendaftaran" class="form-control form-control-sm" value="{{ old('pembukaan_pendaftaran', optional($lomba->pembukaan_pendaftaran)->format('Y-m-d')) }}">
                <span class="invalid-feedback error-pembukaan_pendaftaran"></span>
            </div>

            <div class="col-md-6 mb-3">
                <label for="user_edit_batas_pendaftaran" class="form-label">Batas Pendaftaran</label>
                <input type="date" name="batas_pendaftaran" id="user_edit_batas_pendaftaran" class="form-control form-control-sm" value="{{ old('batas_pendaftaran', optional($lomba->batas_pendaftaran)->format('Y-m-d')) }}">
                <span class="invalid-feedback error-batas_pendaftaran"></span>
            </div>

            {{-- Kategori & Tingkat --}}
            <div class="col-md-6 mb-3">
                <label for="user_edit_kategori" class="form-label">Kategori Peserta</label>
                <select name="kategori" id="user_edit_kategori" class="form-select form-select-sm">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="individu" {{ old('kategori', $lomba->kategori) == 'individu' ? 'selected' : '' }}>Individu</option>
                    <option value="kelompok" {{ old('kategori', $lomba->kategori) == 'kelompok' ? 'selected' : '' }}>Kelompok</option>
                </select>
                <span class="invalid-feedback error-kategori"></span>
            </div>

            <div class="col-md-6 mb-3">
                <label for="user_edit_tingkat" class="form-label">Tingkat Lomba</label>
                <select name="tingkat" id="user_edit_tingkat" class="form-select form-select-sm">
                    <option value="">-- Pilih Tingkat --</option>
                    <option value="lokal" {{ old('tingkat', $lomba->tingkat) == 'lokal' ? 'selected' : '' }}>Lokal/Daerah</option>
                    <option value="nasional" {{ old('tingkat', $lomba->tingkat) == 'nasional' ? 'selected' : '' }}>Nasional</option>
                    <option value="internasional" {{ old('tingkat', $lomba->tingkat) == 'internasional' ? 'selected' : '' }}>Internasional</option>
                </select>
                <span class="invalid-feedback error-tingkat"></span>
            </div>

            {{-- Penyelenggara --}}
            <div class="col-md-12 mb-3">
                <label for="user_edit_penyelenggara" class="form-label">Penyelenggara</label>
                <input type="text" name="penyelenggara" id="user_edit_penyelenggara" class="form-control form-control-sm" value="{{ old('penyelenggara', $lomba->penyelenggara) }}">
                <span class="invalid-feedback error-penyelenggara"></span>
            </div>

            {{-- Bidang Keahlian --}}
            <div class="col-md-12 mb-3">
                <label class="form-label d-block mb-2">Bidang Keahlian Relevan</label>
                <div class="row ps-2">
                    @php
                        // Pastikan $lomba->bidangKeahlian adalah collection atau array yang bisa di-pluck
                        $bidangUserTerpilih = old('bidang_keahlian', ($lomba->bidangKeahlian && method_exists($lomba->bidangKeahlian, 'pluck')) ? $lomba->bidangKeahlian->pluck('bidang_id')->toArray() : ($lomba->bidang_keahlian ?? []));
                        if (!is_array($bidangUserTerpilih) && is_string($bidangUserTerpilih)) {
                            $bidangUserTerpilih = explode(',', $bidangUserTerpilih);
                        } elseif (!is_array($bidangUserTerpilih)) {
                            $bidangUserTerpilih = [];
                        }
                    @endphp
                    @if(isset($bidangList) && $bidangList->count() > 0)
                        @foreach ($bidangList as $bidang)
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="bidang_keahlian[]" value="{{ $bidang->bidang_id }}" id="user_edit_bidang_{{ $bidang->bidang_id }}"
                                {{ in_array($bidang->bidang_id, $bidangUserTerpilih) ? 'checked' : '' }}>
                                <label class="form-check-label" for="user_edit_bidang_{{ $bidang->bidang_id }}">
                                    {{ $bidang->bidang_nama }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">Tidak ada data bidang keahlian.</p>
                    @endif
                </div>
                <span class="invalid-feedback error-bidang_keahlian d-block mt-1"></span>
            </div>
            
            {{-- Input Hadiah Dinamis --}}
            <div class="col-md-12 mb-3">
                <label class="form-label d-block mb-2">Hadiah Lomba</label>
                <div id="hadiahInputsContainerUserEdit">
                    @if($lomba->daftarHadiah && $lomba->daftarHadiah->count() > 0)
                        @foreach($lomba->daftarHadiah as $index => $itemHadiah)
                        <div class="input-group input-group-sm mb-2 hadiah-input-group-user-edit">
                            <input type="text" name="hadiah[]" class="form-control" placeholder="Contoh: Uang Tunai Rp 1.000.000" value="{{ old('hadiah.'.$index, $itemHadiah->hadiah) }}">
                            <button type="button" class="btn btn-danger remove-hadiah-btn-user-edit"><i class="ti ti-trash"></i></button>
                        </div>
                        @endforeach
                    @else
                    {{-- Sediakan minimal satu input jika tidak ada hadiah sebelumnya --}}
                    <div class="input-group input-group-sm mb-2 hadiah-input-group-user-edit">
                        <input type="text" name="hadiah[]" class="form-control" placeholder="Contoh: Uang Tunai Rp 1.000.000">
                        <button type="button" class="btn btn-danger remove-hadiah-btn-user-edit"><i class="ti ti-trash"></i></button>
                    </div>
                    @endif
                </div>
                <button type="button" id="addHadiahBtnUserEdit" class="btn btn-sm btn-outline-success mt-1"><i class="fas fa-plus"></i> Tambah Hadiah</button>
                <span class="invalid-feedback error-hadiah d-block"></span>
            </div>

            {{-- Biaya --}}
            <div class="col-md-12 mb-3">
                <label for="user_edit_biaya" class="form-label">Biaya Pendaftaran (Rp)</label>
                <input type="number" name="biaya" id="user_edit_biaya" class="form-control form-control-sm" value="{{ old('biaya', $lomba->biaya) }}" min="0" placeholder="Kosongkan jika gratis">
                <span class="invalid-feedback error-biaya"></span>
            </div>

            {{-- Link Pendaftaran --}}
            <div class="col-md-6 mb-3">
                <label for="user_edit_link_pendaftaran" class="form-label">Link Pendaftaran</label>
                <input type="url" name="link_pendaftaran" id="user_edit_link_pendaftaran" class="form-control form-control-sm" value="{{ old('link_pendaftaran', $lomba->link_pendaftaran) }}" placeholder="https://contoh.com/daftar">
                <span class="invalid-feedback error-link_pendaftaran"></span>
            </div>

            {{-- Link Penyelenggara --}}
            <div class="col-md-6 mb-3">
                <label for="user_edit_link_penyelenggara" class="form-label">Link Website Penyelenggara</label>
                <input type="url" name="link_penyelenggara" id="user_edit_link_penyelenggara" class="form-control form-control-sm" value="{{ old('link_penyelenggara', $lomba->link_penyelenggara) }}" placeholder="https://penyelenggara.com">
                <span class="invalid-feedback error-link_penyelenggara"></span>
            </div>

            {{-- Poster --}}
            <div class="col-md-12 mb-3">
                <label for="user_edit_poster" class="form-label">Poster Lomba</label>
                <input type="file" name="poster" id="user_edit_poster" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg,application/pdf">
                <small class="form-text text-muted">Format: JPG, JPEG, PNG. Max: 2MB.</small>
                @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
                    <small class="form-text text-muted mt-1 d-block">
                        Poster saat ini: <a href="{{ asset('storage/' . $lomba->poster) }}" target="_blank">Lihat Poster</a>. Kosongkan jika tidak ingin mengubah.
                    </small>
                @endif
                <span class="invalid-feedback error-poster"></span>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>

<script>
$(document).ready(function() {
    const formUserEditLomba = $('#formUserEditLomba');

    // Custom validation methods (jika belum ada global)
    if (typeof $.validator.methods.afterDate === 'undefined') {
        $.validator.addMethod("afterDate", function(value, element, params) {
            if (!value || !$(params).val()) { return true; }
            return new Date(value) >= new Date($(params).val());
        }, 'Tanggal batas harus setelah atau sama dengan tanggal pembukaan.');
    }
    if (typeof $.validator.methods.filesize === 'undefined') {
        $.validator.addMethod("filesize", function(value, element, param) {
            return this.optional(element) || (element.files[0].size <= param);
        }, 'Ukuran file maksimal adalah 2MB.');
    }
    if (typeof $.validator.methods.nullableUrl === 'undefined') {
        $.validator.addMethod("nullableUrl", function(value, element) {
            if (value === "") return true;
            return /^(ftp|http|https):\/\/[^ "]+$/.test(value);
        }, "Format URL tidak valid.");
    }
     if (typeof $.validator.methods.extension === 'undefined') {
        $.validator.addMethod("extension", function(value, element, param) {
            param = typeof param === "string" ? param.replace(/,/g, "|") : "png|jpe?g|gif";
            return this.optional(element) || value.match(new RegExp("\\.(" + param + ")$", "i"));
        }, $.validator.format("Please enter a value with a valid extension."));
    }


    formUserEditLomba.validate({
        rules: {
            nama_lomba: { required: true, maxlength: 50, minlength: 5  },
            pembukaan_pendaftaran: { required: true, dateISO: true },
            batas_pendaftaran: { required: true, dateISO: true, afterDate: '#user_edit_pembukaan_pendaftaran' },
            kategori: { required: true },
            penyelenggara: { required: true, maxlength: 50, minlength: 2 },
            tingkat: { required: true },
            'bidang_keahlian[]': { required: true, minlength: 1 },
            biaya: { number: true, min: 0 },
            link_pendaftaran: { required: true, nullableUrl: true, maxlength: 150 },
            link_penyelenggara: { nullableUrl: true, maxlength: 150 },
            poster: { extension: "jpg|jpeg|png|pdf", filesize: 2097152 },
            'hadiah[]': { minlength: 4, maxlength: 40 }
        },
        messages: {
            nama_lomba: { required: "Nama Lomba wajib diisi.", maxlength: "Nama lomba maksimal 50 karakter.", minlength: "Nama lomba minimal 5 karakter." },
            pembukaan_pendaftaran: { required: "Tanggal pembukaan wajib diisi.", dateISO: "Format tanggal tidak valid." },
            batas_pendaftaran: { required: "Batas pendaftaran wajib diisi.", dateISO: "Format tanggal tidak valid.", afterDate: "Batas pendaftaran harus sesudah tanggal pembukaan." },
            kategori: { required: "Kategori peserta wajib dipilih." },
            penyelenggara: { required: "Penyelenggara wajib diisi.", maxlength: "Penyelenggara maksimal 50 karakter.", minlength: "Penyelenggara minimal 2 karakter." },
            tingkat: { required: "Tingkat lomba wajib dipilih." },
            'bidang_keahlian[]': { required: "Pilih minimal satu bidang keahlian.", minlength: "Pilih minimal satu bidang keahlian." },
            biaya: { number: "Biaya harus berupa angka.", min: "Biaya tidak boleh negatif." },
            link_pendaftaran: { required: "Link pendaftaran wajib diisi.", nullableUrl: "Format URL pendaftaran tidak valid.", maxlength: "Link pendaftaran maksimal 150 karakter." },
            link_penyelenggara: { nullableUrl: "Format URL penyelenggara tidak valid.", maxlength: "Link penyelenggara maksimal 150 karakter." },
            poster: { extension: "Format file poster tidak valid (hanya JPG, JPEG, PNG).", filesize: "Ukuran file poster maksimal 2MB." },
            'hadiah[]': { minlength: "Deskripsi hadiah minimal 4 karakter.", maxlength: "Deskripsi hadiah maksimal 40 karakter."}
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback'); // Bootstrap 5 class for errors
            let fieldName = element.attr('name');

            if (fieldName === "bidang_keahlian[]") {
                // Place error for checkbox group after the whole group
                element.closest('.col-md-12.mb-3').find('span.error-bidang_keahlian').html(error.html()).show();
            } else if (fieldName === "hadiah[]") {
                // Place error for hadiah group after the container
                $('#hadiahInputsContainerUserEdit').closest('.col-md-12.mb-3').find('span.error-hadiah').html(error.html()).show();
            } else {
                // For other inputs, place error after the input, inside its .mb-3 parent
                let errorSpan = element.closest('.mb-3').find('span.invalid-feedback.error-' + fieldName.replace('[]',''));
                if (errorSpan.length) {
                    errorSpan.html(error.html()).show();
                } else {
                     // Fallback if specific span not found
                    element.after(error);
                }
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid').removeClass('is-valid');
            if ($(element).attr("name") === "bidang_keahlian[]") {
                // Optionally, add class to parent of checkboxes for visual cue
                // $(element).closest('.form-check').addClass('is-invalid-item'); 
            }
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid').addClass('is-valid');
            let fieldName = $(element).attr('name');
            if (fieldName === "bidang_keahlian[]") {
                // $(element).closest('.form-check').removeClass('is-invalid-item');
                // Hide group error if all checkboxes in group are valid
                if (!$(element).closest('.col-md-12.mb-3').find('input[name="bidang_keahlian[]"].is-invalid').length) {
                    $(element).closest('.col-md-12.mb-3').find('span.error-bidang_keahlian').empty().hide();
                }
            } else if (fieldName === "hadiah[]") {
                 if (!$('#hadiahInputsContainerUserEdit').find('input[name="hadiah[]"].is-invalid').length) {
                    $('#hadiahInputsContainerUserEdit').closest('.col-md-12.mb-3').find('span.error-hadiah').empty().hide();
                 }
            } else {
                 $(element).closest('.mb-3').find('span.invalid-feedback.error-' + fieldName.replace('[]','')).empty().hide();
            }
        },
        submitHandler: function(form) {
            let formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');
            const originalButtonText = submitButton.html();

            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');
            
            // Clear previous errors specifically for this form
            $(form).find('.invalid-feedback').text('').hide();
            $(form).find('.form-control, .form-select, .form-check-input').removeClass('is-invalid is-valid');
            // $(form).find('.form-check.is-invalid-item').removeClass('is-invalid-item'); // Jika menggunakan class ini

            $.ajax({
                url: $(form).attr('action'),
                method: 'POST', 
                headers: { 
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 
                    'X-HTTP-Method-Override': 'PUT'
                },
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        // ID Modal yang digunakan di histori_pengajuan_lomba.blade.php (atau halaman pemicu)
                        // Asumsi ID modal adalah 'modalFormLombaUser'
                        $('#modalFormLombaUser').modal('hide'); 
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                        });
                        // Reload DataTable di halaman histori jika ada
                        if (typeof historiTable !== 'undefined' && historiTable.ajax) {
                            historiTable.ajax.reload(null, false); // false agar tidak reset paging
                        }
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message || 'Gagal menyimpan data. Periksa kembali isian Anda.',
                            icon: 'error'
                        });
                        // Tampilkan error validasi dari server jika ada
                        if (response.errors) {
                            $.each(response.errors, function(key, messages) {
                                let inputName = key.replace(/\./g, '_'); // Untuk error array seperti 'bidang_keahlian.0'
                                let inputElement = $(form).find(`[name^="${inputName.split('_')[0]}"]`); // Cari berdasarkan name dasar
                                
                                if (key === 'bidang_keahlian' || key.startsWith('bidang_keahlian.')) {
                                     inputElement = $(form).find(`[name="bidang_keahlian[]"]`).first(); // Targetkan elemen pertama dari grup checkbox
                                     let errorContainer = inputElement.closest('.col-md-12.mb-3').find('span.error-bidang_keahlian');
                                     errorContainer.html(messages[0]).show();
                                     inputElement.closest('.col-md-12.mb-3').find('input[name="bidang_keahlian[]"]').addClass('is-invalid');
                                } else if (key === 'hadiah' || key.startsWith('hadiah.')) {
                                    let errorContainer = $('#hadiahInputsContainerUserEdit').closest('.col-md-12.mb-3').find('span.error-hadiah');
                                    errorContainer.html(messages[0]).show();
                                    $('#hadiahInputsContainerUserEdit').find('input[name="hadiah[]"]').addClass('is-invalid');
                                } else if (inputElement.length) {
                                    inputElement.addClass('is-invalid');
                                    // Cari span error spesifik dulu
                                    let errorContainer = inputElement.closest('.mb-3').find('.invalid-feedback.error-' + inputName);
                                    if (errorContainer.length) {
                                        errorContainer.text(messages[0]).show();
                                    } else { // Fallback jika tidak ada span .error-FIELDNAME
                                        inputElement.after('<span class="invalid-feedback d-block">' + messages[0] + '</span>');
                                    }
                                }
                            });
                        }
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
                     if (xhr.responseJSON) {
                        errorMessage = xhr.responseJSON.message || (xhr.responseJSON.errors ? 'Terjadi kesalahan validasi.' : errorMessage);
                        if (xhr.responseJSON.errors) {
                             let errorMessagesList = '<ul class="text-start ps-3">';
                            $.each(xhr.responseJSON.errors, function(key, messages) {
                                 errorMessagesList += `<li>${messages[0]}</li>`;
                            });
                             errorMessagesList += '</ul>';
                             errorMessage = '<strong>Detail Kesalahan:</strong>' + errorMessagesList;
                        }
                    }
                    Swal.fire({ title: 'Error!', html: errorMessage, icon: 'error' });
                },
                complete: function() {
                    submitButton.prop('disabled', false).html(originalButtonText);
                }
            });
        }
    });

    // Logika untuk menambah/menghapus input hadiah di form EDIT
    $('#addHadiahBtnUserEdit').on('click', function() {
        const newHadiahInput = `
            <div class="input-group input-group-sm mb-2 hadiah-input-group-user-edit">
                <input type="text" name="hadiah[]" class="form-control" placeholder="Deskripsi hadiah lainnya...">
                <button type="button" class="btn btn-danger remove-hadiah-btn-user-edit"><i class="ti ti-trash"></i></button>
            </div>
        `;
        $('#hadiahInputsContainerUserEdit').append(newHadiahInput);
    });

    // Gunakan event delegation untuk tombol remove karena elemennya dinamis
    $('#hadiahInputsContainerUserEdit').on('click', '.remove-hadiah-btn-user-edit', function() {
        // Selalu biarkan minimal satu input, tapi bisa dikosongkan jika user mau
        if ($('#hadiahInputsContainerUserEdit .hadiah-input-group-user-edit').length > 1) {
            $(this).closest('.hadiah-input-group-user-edit').remove();
        } else {
            // Jika hanya satu, kosongkan nilainya saja
             $(this).closest('.hadiah-input-group-user-edit').find('input[name="hadiah[]"]').val('');
            // Beri tahu user bahwa minimal satu field (meski bisa kosong jika tidak wajib)
            // Swal.fire('Info', 'Minimal harus ada satu field untuk hadiah. Anda bisa mengosongkannya jika tidak ada hadiah.', 'info');
        }
    });
});
</script>
