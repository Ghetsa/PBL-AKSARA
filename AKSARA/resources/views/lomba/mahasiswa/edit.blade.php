<form id="formUserLomba" action="{{ route('lomba.mhs.update_form', $lomba->lomba_id) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @if (isset($lomba))
        @method('PUT')
    @endif

    <div class="modal-header">
        <h5 class="modal-title">{{ isset($lomba) ? 'Edit Info Lomba' : 'Ajukan Info Lomba Baru' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">
        <div class="row">
            {{-- Contoh satu field: Nama Lomba --}}
            <div class="col-md-12 mb-3">
                <label for="user_nama_lomba" class="form-label">
                    Nama Lomba <span class="text-danger">*</span>
                </label>
                <input type="text" name="nama_lomba" id="user_nama_lomba" class="form-control"
                    value="{{ old('nama_lomba', $lomba->nama_lomba ?? '') }}" required>
                {{-- Sesuaikan: pakai class, bukan id --}}
                <span class="invalid-feedback error-nama_lomba"></span>
            </div>

            {{-- Field Pembukaan Pendaftaran --}}
            <div class="col-md-6 mb-3">
                <label for="user_pembukaan_pendaftaran" class="form-label">
                    Pembukaan Pendaftaran <span class="text-danger">*</span>
                </label>
                <input type="date" name="pembukaan_pendaftaran" id="user_pembukaan_pendaftaran" class="form-control"
                    value="{{ old(
                        'pembukaan_pendaftaran',
                        isset($lomba->pembukaan_pendaftaran) ? $lomba->pembukaan_pendaftaran->format('Y-m-d') : '',
                    ) }}"
                    required>
                <span class="invalid-feedback error-pembukaan_pendaftaran"></span>
            </div>

            {{-- Field Batas Pendaftaran --}}
            <div class="col-md-6 mb-3">
                <label for="user_batas_pendaftaran" class="form-label">
                    Batas Pendaftaran <span class="text-danger">*</span>
                </label>
                <input type="date" name="batas_pendaftaran" id="user_batas_pendaftaran" class="form-control"
                    value="{{ old('batas_pendaftaran', isset($lomba->batas_pendaftaran) ? $lomba->batas_pendaftaran->format('Y-m-d') : '') }}"
                    required>
                <span class="invalid-feedback error-batas_pendaftaran"></span>
            </div>

            {{-- Field Kategori --}}
            <div class="col-md-6 mb-3">
                <label for="user_kategori" class="form-label">
                    Kategori Lomba <span class="text-danger">*</span>
                </label>
                <select name="kategori" id="user_kategori" class="form-select" required>
                    <option value="individu"
                        {{ old('kategori', $lomba->kategori ?? '') == 'individu' ? 'selected' : '' }}>
                        Individu
                    </option>
                    <option value="kelompok"
                        {{ old('kategori', $lomba->kategori ?? '') == 'kelompok' ? 'selected' : '' }}>
                        Kelompok
                    </option>
                </select>
                <span class="invalid-feedback error-kategori"></span>
            </div>

            {{-- Field Tingkat --}}
            <div class="col-md-6 mb-3">
                <label for="user_tingkat" class="form-label">
                    Tingkat Lomba <span class="text-danger">*</span>
                </label>
                <select name="tingkat" id="user_tingkat" class="form-select" required>
                    <option value="lokal" {{ old('tingkat', $lomba->tingkat ?? '') == 'lokal' ? 'selected' : '' }}>
                        Lokal/Daerah
                    </option>
                    <option value="nasional"
                        {{ old('tingkat', $lomba->tingkat ?? '') == 'nasional' ? 'selected' : '' }}>
                        Nasional
                    </option>
                    <option value="internasional"
                        {{ old('tingkat', $lomba->tingkat ?? '') == 'internasional' ? 'selected' : '' }}>
                        Internasional
                    </option>
                </select>
                <span class="invalid-feedback error-tingkat"></span>
            </div>

            {{-- Field Penyelenggara --}}
            <div class="col-md-12 mb-3">
                <label for="user_penyelenggara" class="form-label">
                    Penyelenggara <span class="text-danger">*</span>
                </label>
                <input type="text" name="penyelenggara" id="user_penyelenggara" class="form-control"
                    value="{{ old('penyelenggara', $lomba->penyelenggara ?? '') }}" required>
                <span class="invalid-feedback error-penyelenggara"></span>
            </div>

            {{-- Field Bidang Keahlian (checkbox group) --}}
            <div class="col-md-12 mb-3">
                <label class="form-label d-block mb-2 text-sm-start">
                    Bidang Keahlian Relevan <span class="text-danger">*</span>
                </label>
                <div class="row ps-2">
                    @php
                        $bidang_terpilih = old(
                            'bidang_keahlian',
                            $lomba->bidangKeahlian && method_exists($lomba->bidangKeahlian, 'pluck')
                                ? $lomba->bidangKeahlian->pluck('bidang_id')->toArray()
                                : $lomba->bidang_keahlian ?? [],
                        );
                        if (!is_array($bidang_terpilih) && is_string($bidang_terpilih)) {
                            $bidang_terpilih = explode(',', $bidang_terpilih);
                        } elseif (!is_array($bidang_terpilih)) {
                            $bidang_terpilih = [];
                        }
                    @endphp

                    @if (isset($bidangList) && $bidangList->count() > 0)
                        @foreach ($bidangList as $bidang)
                            @php
                                $inputIdSlug = Str::slug($bidang->bidang_nama) . '_edit_' . $bidang->bidang_id;
                                $isChecked = in_array($bidang->bidang_id, $bidang_terpilih);
                            @endphp
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="bidang_keahlian[]"
                                        value="{{ $bidang->bidang_id }}" id="{{ $inputIdSlug }}"
                                        {{ $isChecked ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $inputIdSlug }}">
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

            {{-- Field Biaya (kosong = gratis) --}}
            <div class="col-md-12 mb-3">
                <label for="user_biaya" class="form-label">
                    Biaya Pendaftaran (Kosongkan jika gratis)
                </label>
                <input type="number" name="biaya" id="user_biaya" class="form-control"
                    value="{{ old('biaya', $lomba->biaya ?? '') }}" min="0" placeholder="Cth: 50000">
                <span class="invalid-feedback error-biaya"></span>
            </div>

            {{-- Field Link Pendaftaran (opsional) --}}
            <div class="col-md-6 mb-3">
                <label for="user_link_pendaftaran" class="form-label">
                    Link Pendaftaran (Opsional)
                </label>
                <input type="url" name="link_pendaftaran" id="user_link_pendaftaran" class="form-control"
                    value="{{ old('link_pendaftaran', $lomba->link_pendaftaran ?? '') }}"
                    placeholder="https://contoh.com/daftar">
                <span class="invalid-feedback error-link_pendaftaran"></span>
            </div>

            {{-- Field Link Penyelenggara (opsional) --}}
            <div class="col-md-6 mb-3">
                <label for="user_link_penyelenggara" class="form-label">
                    Link Website Penyelenggara (Opsional)
                </label>
                <input type="url" name="link_penyelenggara" id="user_link_penyelenggara" class="form-control"
                    value="{{ old('link_penyelenggara', $lomba->link_penyelenggara ?? '') }}"
                    placeholder="https://penyelenggara.com">
                <span class="invalid-feedback error-link_penyelenggara"></span>
            </div>

            {{-- Field Poster (opsional) --}}
            <div class="col-md-12 mb-3">
                <label for="user_poster" class="form-label">
                    Poster Lomba (Opsional, Max 2MB)
                </label>
                <input type="file" name="poster" id="user_poster" class="form-control"
                    accept="image/jpeg,image/png,image/jpg,application/pdf">
                @if (isset($lomba) && $lomba->poster && Storage::disk('public')->exists($lomba->poster))
                    <small class="form-text text-muted mt-1 d-block">
                        Poster saat ini:
                        <a href="{{ asset('storage/' . $lomba->poster) }}" target="_blank">
                            Lihat Poster
                        </a>.
                        Kosongkan jika tidak ingin mengubah.
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
        const formCreate = $('#formUserLomba');

        // Custom rule: batas_pendaftaran ≥ pembukaan_pendaftaran
        $.validator.addMethod("afterDate", function(value, element, params) {
            if (!value || !$(params).val()) {
                return true; // valid jika salah satu kosong
            }
            return new Date(value) >= new Date($(params).val());
        }, 'Tanggal batas harus setelah atau sama dengan tanggal pembukaan.');

        // Custom rule: maksimal ukuran file 2MB
        $.validator.addMethod("filesize", function(value, element, param) {
            return this.optional(element) || (element.files[0].size <= param);
        }, 'Ukuran file maksimal adalah 2MB.');

        // Custom rule: URL boleh kosong atau valid URL
        $.validator.addMethod("nullableUrl", function(value, element) {
            if (value === "") return true;
            return /^(ftp|http|https):\/\/[^ "]+$/.test(value);
        }, "Format URL tidak valid.");

        formCreate.validate({
            // ⚠️ Jangan lupa tambahkan koma di antara date:true dan afterDate:
            rules: {
                nama_lomba: {
                    required: true,
                    maxlength: 255
                },
                kategori: {
                    required: true
                },
                'bidang_keahlian[]': {
                    required: true,
                    minlength: 1
                },
                penyelenggara: {
                    required: true,
                    maxlength: 255
                },
                tingkat: {
                    required: true
                },
                link_pendaftaran: {
                    nullableUrl: true,
                    maxlength: 255
                },
                link_penyelenggara: {
                    nullableUrl: true,
                    maxlength: 255
                },
                poster: {
                    extension: "jpg,jpeg,png,pdf",
                    filesize: 2097152
                },
                pembukaan_pendaftaran: {
                    required: true,
                    date: true
                },
                batas_pendaftaran: {
                    required: true,
                    date: true,
                    afterDate: '#user_pembukaan_pendaftaran'
                },
            },
            messages: {
                nama_lomba: {
                    required: "Nama Lomba tidak boleh kosong",
                    minlength: "Nama Lomba minimal harus 3 karakter"
                },
                kategori: {
                    required: "Kategori Lomba tidak boleh kosong"
                },
                'bidang_keahlian[]': {
                    required: "Minimal pilih satu bidang keahlian",
                },
                penyelenggara: {
                    required: "Penyelenggara Lomba tidak boleh kosong",
                    minlength: "Penyelenggara Lomba minimal harus 3 karakter"
                },
                tingkat: {
                    required: "Tingkat tidak boleh kosong"
                },
                link_pendaftaran: {
                    url: "Link pendaftaran tidak valid"
                },
                link_penyelenggara: {
                    url: "Link penyelenggara tidak valid"
                },
                poster: {
                    extension: "Format file tidak valid. Hanya jpg, jpeg, png, pdf yang diperbolehkan."
                },
                pembukaan_pendaftaran: {
                    required: "Tanggal pembukaan pendaftaran tidak boleh kosong",
                    date: "Format tanggal tidak valid"
                },
                batas_pendaftaran: {
                    required: "Tanggal penutupan pendaftaran tidak boleh kosong",
                    date: "Format tanggal tidak valid",
                    afterDate: "Tanggal batas harus setelah atau sama dengan tanggal pembukaan."
                },
            },
            // Jika kamu mengandalkan <span class="error-<field>">, maka gunakan opsi A (selector .error-<field>)
            errorElement: 'span',
            errorPlacement: function(error, element) {
                if (element.attr("name") == "bidang_keahlian[]") {
                    error.insertAfter(element); // atau custom selector khusus
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
                $('.error-' + $(element).attr('name')).text('').hide();
            },
            submitHandler: function(form) {
                // Kosongkan semua error
                $('.error-text').text('');
                $('.is-invalid').removeClass('is-invalid');

                // Buat FormData
                var formData = new FormData(form);

                $.ajax({
                    url: $(form).attr('action'),
                    method: $(form).attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend: function() {
                        $(form).find('button[type="submit"]')
                            .prop('disabled', true)
                            .html(
                                '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...'
                            );
                    },
                    success: function(response) {
                        $("#myModal").modal('hide');
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                            });
                        } else {
                            alert(response.message);
                        }
                        if (typeof dataLomba !== 'undefined' && dataLomba.ajax) {
                            dataLomba.ajax.reload();
                        } else {
                            window.location.reload();
                        }
                    },
                    error: function(xhr) {
                        const res = xhr.responseJSON;
                        if (res && res.errors) {
                            $.each(res.errors, function(key, messages) {
                                // Tandai input yang bermasalah
                                const el = formCreate.find('[name="' + key +
                                    '"]');
                                el.addClass('is-invalid');
                                formCreate.find('.error-' + key).text(messages[
                                    0]).show();
                            });
                        } else {
                            alert("Terjadi kesalahan saat mengirim data.");
                        }
                        $(form).find('button[type="submit"]').prop('disabled', false)
                            .html('Simpan Perubahan');
                    },
                    complete: function() {
                        $(form).find('button[type="submit"]')
                            .prop('disabled', false)
                            .html('Simpan Perubahan');
                    }
                });
            }
        });
    });
</script>
