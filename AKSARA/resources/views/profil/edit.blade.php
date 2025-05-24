{{-- profil/edit.blade.php --}}
<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <form id="formUpdateProfile" method="POST" action="{{ route('profile.update_ajax') }}" enctype="multipart/form-data">
            @csrf
            {{-- HAPUS BARIS INI: @method('PUT') --}}

            <div class="modal-header">
                <h5 class="modal-title">Perbarui Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">

                {{-- Bagian Info Dasar --}}
                <div id="section-dasar">
                    <h5><i class="ti ti-id me-2"></i>Info Dasar</h5>
                    <hr class="mt-1 mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_nama" class="form-label">Nama Lengkap <span class="text-danger"></span></label>
                                <input type="text" name="nama" id="edit_nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                                <span class="invalid-feedback error-nama"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_email" class="form-label">Email <span class="text-danger"></span></label>
                                <input type="email" name="email" id="edit_email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                 <span class="invalid-feedback error-email"></span>
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
                    @if ($user->role === 'dosen' && $user->dosen)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="gelar" class="form-label">Gelar</label>
                                    <input type="text" name="gelar" id="gelar" class="form-control"
                                        value="{{ old('gelar', $user->dosen->gelar ?? '') }}">
                                    <span class="invalid-feedback error-gelar"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="no_hp" class="form-label">No. HP</label>
                                    <input type="text" name="no_hp" id="no_hp" class="form-control"
                                        value="{{ old('no_hp', $user->dosen->no_hp ?? '') }}">
                                    <span class="invalid-feedback error-no_hp"></span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <hr class="my-4">

                {{-- Bagian Minat (kode lainnya tetap sama) --}}
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
                                        <input class="form-check-input minat-checkbox" type="checkbox" name="minat_pilihan[]" value="{{ $bidang->bidang_id }}" id="minat_{{ $inputIdSlug }}" {{ $isMinatChecked ? 'checked' : '' }} data-bidang-id="{{ $bidang->bidang_id }}">
                                        <label class="form-check-label" for="minat_{{ $inputIdSlug }}">
                                            {{ $bidang->bidang_nama }}
                                        </label>
                                    </div>
                                    <div class="ms-1 minat-level-container" id="minat_level_container_{{ $bidang->bidang_id }}" style="{{ $isMinatChecked ? '' : 'display:none;' }}">
                                        <label for="minat_level_{{ $bidang->bidang_id }}" class="form-label small mb-1">Level Minat:</label>
                                        <select class="form-select form-select-sm" name="minat_level[{{ $bidang->bidang_id }}]" id="minat_level_{{ $bidang->bidang_id }}">
                                            @foreach($minatLevelOptions as $value => $label)
                                                <option value="{{ $value }}" {{ $currentLevel == $value ? 'selected' : '' }}>{{ $label }}</option>
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

                {{-- Bagian Pengalaman (kode lainnya tetap sama) --}}
                @if ($user->role === 'mahasiswa' || $user->role === 'dosen')
                 <div id="section-pengalaman" class="mt-3">
                    <h5><i class="ti ti-briefcase me-2"></i>Pengalaman</h5>
                     <hr class="mt-1 mb-3">
                     <div id="pengalaman-fields-container">
                         @forelse ($selectedPengalaman as $index => $pengalaman)
                             <div class="pengalaman-item border rounded p-3 mb-3">
                                 <div class="d-flex justify-content-end">
                                     <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn mb-2"><i class="ti ti-trash"></i></button>
                                 </div>
                                 <div class="row">
                                     <div class="col-md-6 mb-2">
                                         <label class="form-label">Nama Pengalaman/Posisi <span class="text-danger">*</span></label>
                                         <input type="text" name="pengalaman_items[{{ $index }}][pengalaman_nama]" class="form-control" value="{{ $pengalaman->pengalaman_nama }}">
                                         <span class="invalid-feedback error-pengalaman_items.{{$index}}.pengalaman_nama"></span>
                                     </div>
                                     <div class="col-md-6 mb-2">
                                         <label class="form-label">Kategori</label>
                                         <select name="pengalaman_items[{{ $index }}][pengalaman_kategori]" class="form-control">
                                             <option value="">-- Pilih Kategori --</option>
                                             <option value="Workshop" {{ old('pengalaman_kategori', $pengalaman->pengalaman_kategori ?? '') == 'Workshop' ? 'selected' : '' }}>Workshop</option>
                                             <option value="Magang" {{ old('pengalaman_kategori', $pengalaman->pengalaman_kategori ?? '') == 'Magang' ? 'selected' : '' }}>Magang</option>
                                             <option value="Proyek" {{ old('pengalaman_kategori', $pengalaman->pengalaman_kategori ?? '') == 'Proyek' ? 'selected' : '' }}>Proyek</option>
                                             <option value="Organisasi" {{ old('pengalaman_kategori', $pengalaman->pengalaman_kategori ?? '') == 'Organisasi' ? 'selected' : '' }}>Organisasi</option>
                                             <option value="Pekerjaan" {{ old('pengalaman_kategori', $pengalaman->pengalaman_kategori ?? '') == 'Pekerjaan' ? 'selected' : '' }}>Pekerjaan</option>
                                         </select>
                                         <span class="invalid-feedback error-pengalaman_items.{{$index}}.pengalaman_kategori"></span>
                                     </div>
                                 </div>
                             </div>
                         @empty
                             <div class="pengalaman-item border rounded p-3 mb-3">
                                 <div class="d-flex justify-content-end">
                                     <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn mb-2" style="display:none;"><i class="ti ti-trash"></i></button>
                                 </div>
                                 <div class="row">
                                     <div class="col-md-6 mb-2">
                                         <label class="form-label">Nama Pengalaman/Posisi <span class="text-danger">*</span></label>
                                         <input type="text" name="pengalaman_items[0][pengalaman_nama]" class="form-control">
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
                     <button type="button" id="add-pengalaman-btn" class="btn btn-sm btn-outline-primary mt-2"><i class="ti ti-plus"></i> Tambah Pengalaman</button>
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

{{-- Kode JavaScript di bawah ini tidak perlu diubah untuk error ini, --}}
{{-- kecuali jika Anda ingin menyesuaikan logika penanganan error validasi --}}
{{-- berdasarkan perubahan error span class yang saya sarankan sebelumnya. --}}
{{-- Perubahan sebelumnya pada error span class di JS (menggunakan titik) sudah baik. --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // ... (kode JavaScript yang sudah ada dan telah diperbaiki sebelumnya tetap di sini) ...
    // Pastikan kode JavaScript untuk AJAX submission menggunakan method: 'POST'
    // dan tidak secara eksplisit menambahkan _method='PUT' ke formData jika
    // Anda sudah menghapus @method('PUT') dari form.
    // FormData(form) akan secara otomatis menyertakan semua field dari form.
    // Jika @method('PUT') dihapus, maka field _method tidak akan ada.

    $('.minat-checkbox').each(function() {
        var $checkbox = $(this);
        var bidangId = $checkbox.data('bidang-id');
        var $levelContainer = $('#minat_level_container_' + bidangId);

        if (!$checkbox.is(':checked')) {
            $levelContainer.hide();
        }
        $checkbox.on('change', function() {
            if ($(this).is(':checked')) {
                $levelContainer.slideDown();
            } else {
                $levelContainer.slideUp();
            }
        });
    });

    function updatePengalamanRemoveButtons() {
        const itemCount = $('#pengalaman-fields-container .pengalaman-item').length;
        $('#pengalaman-fields-container .pengalaman-item').each(function(index) {
            const $currentItem = $(this);
            const isNameFieldEmpty = $currentItem.find('input[name$="[pengalaman_nama]"]').val() === '';

            if (itemCount > 1 || (itemCount === 1 && !isNameFieldEmpty) ) {
                $currentItem.find('.remove-item-btn').show();
            } else {
                $currentItem.find('.remove-item-btn').hide();
            }
        });
         if (itemCount === 0) {
             $('#add-pengalaman-btn').click();
         }
    }

    $('#add-pengalaman-btn').click(function() {
        const newIndex = Date.now();
        const newItemHtml = `
            <div class="pengalaman-item border rounded p-3 mb-3">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn mb-2"><i class="ti ti-trash"></i></button>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Nama Pengalaman/Posisi <span class="text-danger">*</span></label>
                        <input type="text" name="pengalaman_items[${newIndex}][pengalaman_nama]" class="form-control">
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
        updatePengalamanRemoveButtons();
    });

    $('body').on('click', '#pengalaman-fields-container .remove-item-btn', function() {
        $(this).closest('.pengalaman-item').remove();
        updatePengalamanRemoveButtons();
    });
    updatePengalamanRemoveButtons();


    $('#formUpdateProfile').on('submit', function (e) {
        e.preventDefault();
        let form = this;
        let formData = new FormData(form); // Jika @method('PUT') dihapus, _method tidak akan ada di sini

        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan…');
        $('.invalid-feedback').text('').hide();
        $('input, select, textarea').removeClass('is-invalid');

        $.ajax({
            url: $(form).attr('action'),
            method: 'POST', // Ini sudah benar methodnya POST
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
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        if(response.redirect) {
                             window.location.href = response.redirect;
                        } else {
                            location.reload();
                        }
                    });
                }, 300);
            },
            error: function (xhr) { // Logika error handling yang sudah diperbaiki sebelumnya
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let firstErrorElement = null;
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

                        if(!inputElement.length && key.startsWith('sertifikasi_file.')){
                            let bidangIdFromFileKey = key.split('.')[1];
                            inputElement = $(form).find(`input[name="sertifikasi_file[${bidangIdFromFileKey}]"]`);
                        }
                         if (!inputElement.length && key.startsWith('minat_pilihan')) {
                            inputElement = $(form).find('[name="minat_pilihan[]"]').first();
                        }

                        if (inputElement.length) {
                            inputElement.addClass('is-invalid');
                            let errorKeyForSelector = key.replace(/\./g, '\\.');
                            let errorContainer = inputElement.closest('.form-group, .mb-2, .mb-3, .ms-1, .card-body').find(`.invalid-feedback.error-${errorKeyForSelector}`);
                            
                            if (!errorContainer.length) {
                                errorContainer = inputElement.parent().find(`.invalid-feedback.error-${errorKeyForSelector}`);
                            }
                             if (!errorContainer.length && inputElement.parent().hasClass('input-group')) {
                                 errorContainer = inputElement.parent().parent().find(`.invalid-feedback.error-${errorKeyForSelector}`);
                            }
                            if (!errorContainer.length) {
                                inputElement.after(`<span class="invalid-feedback d-block error-${key.replace(/\./g, '\\.')}">${messages[0]}</span>`);
                                errorContainer = inputElement.siblings(`.invalid-feedback.error-${key.replace(/\./g, '\\.')}`);
                            }
                            errorContainer.text(messages[0]).show();
                            if (!firstErrorElement) firstErrorElement = inputElement;
                        } else {
                            if ($(`.modal-body .alert-danger[data-error-key="${key}"]`).length === 0) {
                                $('.modal-body').prepend(`<div class="alert alert-danger alert-dismissible fade show small py-2" role="alert" data-error-key="${key}">Error pada ${key}: ${messages[0]}<button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button></div>`);
                                if(!firstErrorElement) firstErrorElement = $('.modal-body .alert-danger').first();
                            }
                        }
                    });
                    if(firstErrorElement && firstErrorElement.length){
                        $(form).closest('.modal-body').animate({
                            scrollTop: firstErrorElement.offset().top - $(form).closest('.modal-body').offset().top + $(form).closest('.modal-body').scrollTop() - 20
                        }, 300);
                    }
                     Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: xhr.responseJSON.message || 'Periksa kembali isian Anda.' });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan server. Silakan coba lagi.'
                    });
                }
            },
            complete: function () {
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });
});
</script>