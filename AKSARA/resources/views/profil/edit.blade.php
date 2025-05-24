{{-- profil/edit.blade.php --}}
<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <form id="formUpdateProfile" method="POST" action="{{ route('profile.update_ajax') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title">Perbarui Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">

                {{-- Bagian Info Dasar (Tetap sama, pastikan nama input foto adalah 'foto') --}}
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
                        <input type="file" name="foto" id="edit_foto" class="form-control"> {{-- Pastikan name="foto" --}}
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
                            $currentLevel = $isMinatChecked ? $userMinatIni['level'] : 'minat'; // Default to 'minat'
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

                {{-- Bagian Pengalaman (Pastikan $selectedPengalaman dan $allBidangOptions tersedia jika ada select bidang di sini) --}}
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
                                    <input type="hidden" name="pengalaman_items[{{ $index }}][id]" value="{{ $pengalaman->id }}">
                                     <div class="col-md-6 mb-2">
                                         <label class="form-label">Nama Pengalaman/Posisi <span class="text-danger">*</span></label>
                                         <input type="text" name="pengalaman_items[{{ $index }}][pengalaman_nama]" class="form-control" value="{{ $pengalaman->pengalaman_nama }}">
                                         <span class="invalid-feedback error-pengalaman_items.{{$index}}.pengalaman_nama"></span>
                                     </div>
                                     <div class="col-md-6 mb-2">
                                         <label class="form-label">Kategori</label>
                                         <select name="pengalaman_items[{{ $index }}][pengalaman_kategori]" class="form-control">
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="workshop" {{ (old('pengalaman_kategori', $pengalaman->pengalaman_kategori ?? '') == 'workshop') ? 'selected' : '' }}>Workshop</option>
                                            <option value="magang" {{ (old('pengalaman_kategori', $pengalaman->pengalaman_kategori ?? '') == 'magang') ? 'selected' : '' }}>Magang</option>
                                            <option value="proyek" {{ (old('pengalaman_kategori', $pengalaman->pengalaman_kategori ?? '') == 'proyek') ? 'selected' : '' }}>Proyek</option>
                                            <option value="organisasi" {{ (old('pengalaman_kategori', $pengalaman->pengalaman_kategori ?? '') == 'organisasi') ? 'selected' : '' }}>Organisasi</option>
                                            <option value="pekerjaan" {{ (old('pengalaman_kategori', $pengalaman->pengalaman_kategori ?? '') == 'pekerjaan') ? 'selected' : '' }}>Pekerjaan</option>
                                        </select>
                                         <span class="invalid-feedback error-pengalaman_items.{{$index}}.pengalaman_kategori"></span>
                                     </div>
                                     {{-- Jika Anda ingin menghubungkan pengalaman dengan bidang dari tabel 'bidang' --}}
                                     {{-- <div class="col-md-12 mb-2">
                                         <label class="form-label">Bidang Terkait (Opsional)</label>
                                         <select name="pengalaman_items[{{ $index }}][bidang_id]" class="form-select form-select-sm">
                                             <option value="">-- Pilih Bidang --</option>
                                             @foreach($allBidangOptions as $bidang_opt)
                                                 <option value="{{ $bidang_opt->bidang_id }}" {{ (isset($pengalaman->bidang_id) && $pengalaman->bidang_id == $bidang_opt->bidang_id) ? 'selected' : '' }}>
                                                     {{ $bidang_opt->bidang_nama }}
                                                 </option>
                                             @endforeach
                                         </select>
                                     </div> --}}
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
                                            <option value="workshop">Workshop</option>
                                            <option value="magang">Magang</option>
                                            <option value="proyek">Proyek</option>
                                            <option value="organisasi">Organisasi</option>
                                            <option value="pekerjaan">Pekerjaan</option>
                                        </select>

                                        <span class="invalid-feedback error-pengalaman_items.0.pengalaman_kategori"></span>
                                    </div>
                                     {{-- <div class="col-md-12 mb-2">
                                        <label class="form-label">Bidang Terkait (Opsional)</label>
                                        <select name="pengalaman_items[0][bidang_id]" class="form-select form-select-sm">
                                            <option value="">-- Pilih Bidang --</option>
                                            @foreach($allBidangOptions as $bidang_opt)
                                                <option value="{{ $bidang_opt->bidang_id }}">{{ $bidang_opt->bidang_nama }}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let pengalamanIndex = $('#pengalaman-fields-container .pengalaman-item').length || 0;

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
            const isEmptyTemplate = $currentItem.find('input[name$="[pengalaman_nama]"]').val() === '' && itemCount === 1;

            if (itemCount > 1 || !isEmptyTemplate) {
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
        const newIndex = pengalamanIndex++; // increment index

        const newItemHtml = `
            <div class="pengalaman-item border rounded p-3 mb-3">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn mb-2"><i class="ti ti-trash"></i></button>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Nama Pengalaman/Posisi <span class="text-danger">*</span></label>
                        <input type="text" name="pengalaman_items[${newIndex}][pengalaman_nama]" class="form-control">
                        <span class="invalid-feedback error-pengalaman_items_${newIndex}_pengalaman_nama"></span>
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
                        <span class="invalid-feedback error-pengalaman_items_${newIndex}_pengalaman_kategori"></span>
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

        const submitButton = $(form).find('button[type="submit"]');
        const originalButtonText = submitButton.html();

        // Disable tombol dan ganti teks jadi spinner
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan…');

        // Handle checkbox dan select minat supaya data dikirim benar
        $('.minat-checkbox').each(function() {
            const bidangId = $(this).data('bidang-id');
            const $levelSelect = $(`#minat_level_${bidangId}`);

            if ($(this).is(':checked')) {
                $(this).prop('checked', true);
                $levelSelect.prop('disabled', false);
            } else {
                $(this).prop('checked', false);
                $levelSelect.prop('disabled', false); // tetap enabled supaya terkirim
                $levelSelect.val(''); // kosongkan nilai level jika checkbox tidak dicentang
            }
        });

        let formData = new FormData(form);

        $.ajax({
            url: $(form).attr('action'),
            method: 'POST', // tetap POST karena ada @method('PUT')
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
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let firstErrorElement = null;
                    $.each(errors, function (key, messages) {
                        let sanitizedKey = key.replace(/\./g, '_');
                        let inputElement = $(form).find(`[name="${key}"]`);
                        if (!inputElement.length && key.includes('.')) {
                             inputElement = $(form).find(`[name^="${key.split('.')[0]}"][name*="[${key.split('.')[1]}]"]`);
                        }
                        if(!inputElement.length && key.startsWith('sertifikasi_file.')){
                            let bidangIdFromFileKey = key.split('.')[1];
                            inputElement = $(form).find(`input[name="sertifikasi_file[${bidangIdFromFileKey}]"]`);
                        }

                        if (inputElement.length) {
                            inputElement.addClass('is-invalid');
                            let errorContainer = inputElement.closest('.form-group, .mb-2, .mb-3, .ms-1').find('.invalid-feedback.error-' + sanitizedKey.split('[')[0]);
                            if(!errorContainer.length) errorContainer = inputElement.parent().find('.invalid-feedback');
                            if(!errorContainer.length) inputElement.after('<span class="invalid-feedback d-block error-'+sanitizedKey+'">' + messages[0] + '</span>');
                            else errorContainer.text(messages[0]).show();

                            if (!firstErrorElement) firstErrorElement = inputElement;
                        } else {
                            $('.modal-body').prepend(`<div class="alert alert-danger alert-dismissible fade show small py-2" role="alert">Error pada ${key}: ${messages[0]}<button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button></div>`);
                            if(!firstErrorElement) firstErrorElement = $('.modal-body .alert-danger').first();
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