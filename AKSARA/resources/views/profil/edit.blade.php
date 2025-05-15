<div class="modal-dialog modal-xl"> 
    <div class="modal-content">
        <form id="formUpdateProfile" method="POST" action="{{ route('profile.update_ajax') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT') 

            <div class="modal-header">
                <h5 class="modal-title">Perbarui Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;"> 

                    <div id="section-dasar">
                        <h5><i class="ti ti-id me-2"></i>Info Dasar</h5>
                        <hr class="mt-1 mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama" id="nama" class="form-control" value="{{ $user->nama }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="profile_photo" class="form-label">Foto Profil</label>
                            <input type="file" name="profile_photo" id="profile_photo" class="form-control">
                            @if($user->profile_photo)
                                <small class="form-text text-muted">Foto saat ini: <a href="{{ Storage::url($user->profile_photo) }}" target="_blank">lihat foto</a>. Kosongkan jika tidak ingin mengubah.</small>
                            @endif
                        </div>
                    </div>
                    <hr class="my-4"> 


                    @if ($user->role === 'dosen' || $user->role === 'mahasiswa')
                    <div id="section-keahlian" class="mt-4"> 
                        <h5><i class="ti ti-star me-2"></i>Keahlian</h5>
                        <hr class="mt-1 mb-3">
                        <div id="keahlian-fields-container">
                            @forelse ($selectedKeahlian as $index => $keahlian)
                                <div class="row keahlian-item mb-2 align-items-center">
                                    <div class="col-md-5">
                                        <input type="text" name="keahlian_items[{{ $index }}][nama]" class="form-control" placeholder="Nama Keahlian (mis: Pemrograman Python)" value="{{ $keahlian->keahlian_nama }}">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="keahlian_items[{{ $index }}][sertifikasi]" class="form-control" placeholder="Sertifikasi (Opsional)" value="{{ $keahlian->sertifikasi }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-danger remove-item-btn">Hapus</button>
                                    </div>
                                </div>
                            @empty
                                <div class="row keahlian-item mb-2 align-items-center">
                                    <div class="col-md-5">
                                        <input type="text" name="keahlian_items[0][nama]" class="form-control" placeholder="Nama Keahlian (mis: Pemrograman Python)">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="keahlian_items[0][sertifikasi]" class="form-control" placeholder="Sertifikasi (Opsional)">
                                    </div>
                                    <div class="col-md-2">
                                    </div>
                                    <!-- Keahlian -->
                                    <div class="form-group">
                                        <label>Keahlian</label>
                                        <select name="keahlian_id[]" class="form-control" multiple>
                                            @foreach ($keahlianList as $keahlian)
                                                <option value="{{ $keahlian->id }}"
                                                    {{ in_array($keahlian->id, $selectedKeahlianIds ?? []) ? 'selected' : '' }}>
                                                    {{ $keahlian->keahlian_nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" id="add-keahlian-btn" class="btn btn-sm btn-outline-primary mt-2">Tambah Keahlian</button>
                    </div>
                    <hr class="my-4"> 
                    @endif


                    @if ($user->role === 'mahasiswa')
                    <div id="section-pengalaman" class="mt-4"> 
                        <h5><i class="ti ti-briefcase me-2"></i>Pengalaman</h5>
                         <hr class="mt-1 mb-3">
                        <div id="pengalaman-fields-container">
                            @forelse ($selectedPengalaman as $index => $pengalaman)
                                <div class="pengalaman-item border rounded p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Nama Pengalaman</label>
                                            <input type="text" name="pengalaman_items[{{ $index }}][pengalaman_nama]" class="form-control" value="{{ $pengalaman->pengalaman_nama }}">
                                        </div>
                                         <div class="col-md-6 mb-2">
                                            <label class="form-label">Kategori</label>
                                            <input type="text" name="pengalaman_items[{{ $index }}][pengalaman_kategori]" class="form-control" placeholder="Mis: Pekerjaan, Magang, Proyek" value="{{ $pengalaman->pengalaman_kategori }}">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger remove-item-btn mt-2">Hapus Pengalaman Ini</button>
                                </div>
                            @empty
                                <div class="pengalaman-item border rounded p-3 mb-3">
                                    <div class="row">
                                         <div class="col-md-6 mb-2"><label class="form-label">Nama Pengalaman</label><input type="text" name="pengalaman_items[0][pengalaman_nama]" class="form-control"></div>
                                         <div class="col-md-6 mb-2">
                                            <label class="form-label">Kategori</label>
                                            <select name="pengalaman_items[0][pengalaman_kategori]" class="form-control">
                                                <option value="">-- Pilih Kategori --</option>
                                                <option value="Workshop">Workshop</option>
                                                <option value="Magang">Magang</option>
                                                <option value="Proyek">Proyek</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" id="add-pengalaman-btn" class="btn btn-sm btn-outline-primary mt-2">Tambah Pengalaman</button>
                    </div>
                    <hr class="my-4">
                    @endif


                    {{-- Bagian Minat --}}
                    @if ($user->role === 'dosen' || $user->role === 'mahasiswa')
                    <div id="section-minat" class="mt-4"> 
                        <h5><i class="ti ti-heart me-2"></i>Minat</h5>
                        <hr class="mt-1 mb-3">
                        <div id="minat-fields-container">
                             @forelse ($selectedMinat as $index => $minat)
                                <div class="row minat-item mb-2 align-items-center">
                                    <div class="col-md-10">
                                        <input type="text" name="minat_items[]" class="form-control" placeholder="Nama Minat" value="{{ $minat->nama_minat }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-danger remove-item-btn">Hapus</button>
                                    </div>
                                </div>
                            @empty
                                <div class="row minat-item mb-2 align-items-center">
                                    <div class="col-md-10">
                                        <input type="text" name="minat_items[]" class="form-control" placeholder="Nama Minat">
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" id="add-minat-btn" class="btn btn-sm btn-outline-primary mt-2">Tambah Minat</button>
                    </div>
                    <hr class="my-4"> 
                    @endif

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // ... (JavaScript Anda untuk add/remove item tetap sama) ...
    // --- Keahlian ---
    let keahlianIndex = {{ $selectedKeahlian->count() > 0 ? $selectedKeahlian->count() : 1 }};
    if (keahlianIndex === 1 && "{{$selectedKeahlian->count()}}" === "0" ) { // Jika tidak ada data, dan index masih 1, kita bersihkan field awal
        $('#keahlian-fields-container .keahlian-item:first-child input').val('');
    }
    $('#add-keahlian-btn').click(function() {
        let newIndex = $('#keahlian-fields-container .keahlian-item').length; // Dapatkan index baru berdasarkan jumlah item yang ada
        $('#keahlian-fields-container').append(`
            <div class="row keahlian-item mb-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="keahlian_items[${newIndex}][nama]" class="form-control" placeholder="Nama Keahlian">
                </div>
                <div class="col-md-5">
                    <input type="text" name="keahlian_items[${newIndex}][sertifikasi]" class="form-control" placeholder="Sertifikasi (Opsional)">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-danger remove-item-btn">Hapus</button>
                </div>
            </div>
        `);
        // keahlianIndex++; // Tidak perlu increment global lagi jika index diambil dari length
        updateRemoveButtons('#keahlian-fields-container .keahlian-item', '#add-keahlian-btn');
    });

    // --- Minat ---
    if ("{{$selectedMinat->count()}}" === "0") {
         $('#minat-fields-container .minat-item:first-child input').val('');
    }
    $('#add-minat-btn').click(function() {
        let newIndex = $('#minat-fields-container .minat-item').length;
        $('#minat-fields-container').append(`
            <div class="row minat-item mb-2 align-items-center">
                <div class="col-md-10">
                    <input type="text" name="minat_items[]" class="form-control" placeholder="Nama Minat">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-danger remove-item-btn">Hapus</button>
                </div>
            </div>
        `);
        updateRemoveButtons('#minat-fields-container .minat-item', '#add-minat-btn');
    });

    // --- Pengalaman ---
    let pengalamanIndex = {{ $selectedPengalaman->count() > 0 ? $selectedPengalaman->count() : 0 }}; // Mulai dari 0 jika kosong
     if (pengalamanIndex === 0) {
        // Jika tidak ada pengalaman, kita bisa membersihkan field default atau membiarkannya untuk input baru
        $('#pengalaman-fields-container .pengalaman-item:first-child input, #pengalaman-fields-container .pengalaman-item:first-child textarea').val('');
        $('#pengalaman-fields-container .pengalaman-item:first-child .masih-bekerja-checkbox').prop('checked', false).trigger('change');
        $('#pengalaman-fields-container .pengalaman-item:first-child .remove-item-btn').hide(); // Sembunyikan tombol hapus jika hanya 1 (template)
    }

    $('#add-pengalaman-btn').click(function() {
        pengalamanIndex = $('#pengalaman-fields-container .pengalaman-item').length;
        const newItemHtml = `
            <div class="pengalaman-item border rounded p-3 mb-3">
                <div class="d-flex justify-content-end"> <button type="button" class="btn btn-sm btn-danger remove-item-btn mb-2">Hapus Pengalaman Ini</button> </div>
                <div class="row">
                    <div class="col-md-6 mb-2"><label class="form-label">Nama Pengalaman</label><input type="text" name="pengalaman_items[${pengalamanIndex}][pengalaman_nama]" class="form-control"></div>
                    <div class="col-md-6 mb-2"><label class="form-label">Kategori</label><input type="text" name="pengalaman_items[${pengalamanIndex}][pengalaman_kategori]" placeholder="Mis: Pekerjaan, Magang, Proyek" class="form-control"></div>
                </div>
            </div>`;
        $('#pengalaman-fields-container').append(newItemHtml);
        // pengalamanIndex++; // Tidak perlu jika index diambil dari length
        // Untuk pengalaman, tombol remove selalu ada per item block, jadi tidak perlu updateRemoveButtons secara khusus untuk menyembunyikan.
    });

    // Fungsi umum untuk menghapus item
    $('body').on('click', '.remove-item-btn', function() {
        $(this).closest('.keahlian-item, .minat-item, .pengalaman-item, .prestasi-item').remove();
        // Panggil updateRemoveButtons jika diperlukan untuk item yang hanya punya 1 tombol hapus jika > 1
        updateRemoveButtons('#keahlian-fields-container .keahlian-item', '#add-keahlian-btn');
        updateRemoveButtons('#minat-fields-container .minat-item', '#add-minat-btn');
        // Untuk pengalaman dan prestasi, tombol hapus ada di setiap item block, jadi tidak perlu logic khusus updateRemoveButtons
        // Namun, jika semua item dihapus, template kosong awal mungkin perlu ditambahkan kembali
        if($('#pengalaman-fields-container .pengalaman-item').length === 0) {
            $('#add-pengalaman-btn').click(); // Tambahkan satu template kosong
            $('#pengalaman-fields-container .pengalaman-item:first-child .remove-item-btn').hide();
        }
        if($('#prestasi-fields-container .prestasi-item').length === 0 && "{{$user->role}}" === "mahasiswa") {
            $('#add-prestasi-btn').click(); // Tambahkan satu template kosong
            $('#prestasi-fields-container .prestasi-item:first-child .remove-item-btn').hide();
        }

    });

    // Fungsi untuk disable/enable tanggal selesai
    $('body').on('change', '.masih-bekerja-checkbox', function() {
        const $tanggalSelesaiInput = $(this).closest('.row').find('.tanggal-selesai-input');
        if ($(this).is(':checked')) {
            $tanggalSelesaiInput.prop('disabled', true).val('');
        } else {
            $tanggalSelesaiInput.prop('disabled', false);
        }
    });
    // Inisialisasi untuk item yang sudah ada
    $('.masih-bekerja-checkbox').each(function(){
        $(this).trigger('change');
    });


    // Fungsi untuk memastikan tombol hapus hanya muncul jika ada lebih dari 1 item (untuk Keahlian & Minat)
    // atau untuk menyembunyikan tombol hapus pada item template jika hanya itu yang ada.
    function updateRemoveButtons(itemSelector, addButtonSelector) {
        const $items = $(itemSelector);
        if ($items.length <= 1) {
            // Jika hanya satu item (bisa jadi template awal yang kosong atau satu data), sembunyikan tombol hapusnya.
            $items.first().find('.remove-item-btn').hide();
        } else {
            $items.find('.remove-item-btn').show();
        }
         // Jika tidak ada item sama sekali, dan itu adalah kontainer keahlian/minat, tambahkan template awal
        if ($items.length === 0) {
            if (addButtonSelector) $(addButtonSelector).click();
        }
    }

    // Panggil updateRemoveButtons saat modal pertama kali dimuat untuk state awal
    updateRemoveButtons('#keahlian-fields-container .keahlian-item', '#add-keahlian-btn');
    updateRemoveButtons('#minat-fields-container .minat-item', '#add-minat-btn');
    // Untuk pengalaman dan prestasi, jika tidak ada data, pastikan template awal tidak memiliki tombol hapus
    if ($('#pengalaman-fields-container .pengalaman-item').length <= 1 && "{{$selectedPengalaman->count()}}" === "0") {
        $('#pengalaman-fields-container .pengalaman-item:first-child .remove-item-btn').hide();
    }
    if ("{{$user->role}}" === "mahasiswa" && $('#prestasi-fields-container .prestasi-item').length <= 1 && "{{$selectedPrestasi->count()}}" === "0") {
         $('#prestasi-fields-container .prestasi-item:first-child .remove-item-btn').hide();
    }


    // Handle form submission (tetap sama)
    $('#formUpdateProfile').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        // Tambahkan _method karena FormData tidak secara native mengirim PUT/PATCH
        formData.append('_method', 'PUT');

        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');


        $.ajax({
            url: $(this).attr('action'),
            method: "POST", // Selalu POST untuk FormData dengan _method
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success){
                    toastr.success(response.message || 'Profil berhasil diperbarui!');
                    $('#updateProfileModal').modal('hide');
                    setTimeout(() => { location.reload(); }, 1500);
                } else {
                     toastr.error(response.message || 'Terjadi kesalahan.');
                     if(response.errors){
                        let errorMsg = '<strong>Kesalahan Validasi:</strong><br>';
                        $.each(response.errors, function(key, value){
                            errorMsg += `&bull; ${value.join('<br>&bull; ')}<br>`;
                        });
                        // Tampilkan pesan error yang lebih detail
                        toastr.error(errorMsg, 'Validasi Gagal', {timeOut: 7000, extendedTimeOut: 3000, escapeHtml: false});
                        console.warn("Validation errors:", response.errors);
                     }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                let errorMessage = 'Gagal memperbarui profil. Coba lagi nanti.';
                if (jqXHR.responseJSON) {
                    errorMessage = jqXHR.responseJSON.message || errorMessage;
                    if(jqXHR.responseJSON.errors){
                         $.each(jqXHR.responseJSON.errors, function(key, value){
                            errorMessage += '<br>&bull; ' + value.join('<br>&bull; ');
                        });
                    }
                }
                toastr.error(errorMessage, 'Error ' + jqXHR.status, {timeOut: 7000, extendedTimeOut: 3000, escapeHtml: false});
                console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
            },
            complete: function(){
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });
});
</script> 

{{-- <div class="modal-dialog modal-xl"> 
    <div class="modal-content">
        <form id="formUpdateProfile" method="POST" action="{{ route('profile.update_ajax') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT') 

            <div class="modal-header">
                <h5 class="modal-title">Ubah Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="profileEditTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="nav-dasar-tab" data-bs-toggle="tab" data-bs-target="#nav-dasar" type="button" role="tab" aria-controls="nav-dasar" aria-selected="true">Info Dasar</button>
                    </li>
                    @if ($user->role === 'dosen' || $user->role === 'mahasiswa')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="nav-keahlian-tab" data-bs-toggle="tab" data-bs-target="#nav-keahlian" type="button" role="tab" aria-controls="nav-keahlian" aria-selected="false">Keahlian</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="nav-pengalaman-tab" data-bs-toggle="tab" data-bs-target="#nav-pengalaman" type="button" role="tab" aria-controls="nav-pengalaman" aria-selected="false">Pengalaman</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="nav-minat-tab" data-bs-toggle="tab" data-bs-target="#nav-minat" type="button" role="tab" aria-controls="nav-minat" aria-selected="false">Minat</button>
                    </li>
                    @endif
                    @if ($user->role === 'mahasiswa')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="nav-prestasi-tab" data-bs-toggle="tab" data-bs-target="#nav-prestasi" type="button" role="tab" aria-controls="nav-prestasi" aria-selected="false">Prestasi</button>
                    </li>
                    @endif
                </ul>

                <div class="tab-content" id="profileEditTabsContent">
                    <div class="tab-pane fade show active" id="nav-dasar" role="tabpanel" aria-labelledby="nav-dasar-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama" id="nama" class="form-control" value="{{ $user->nama }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="profile_photo" class="form-label">Foto Profil</label>
                            <input type="file" name="profile_photo" id="profile_photo" class="form-control">
                            @if($user->profile_photo)
                                <small class="form-text text-muted">Foto saat ini: <a href="{{ Storage::url($user->profile_photo) }}" target="_blank">lihat foto</a>. Kosongkan jika tidak ingin mengubah.</small>
                            @endif
                        </div>

                        @if ($user->role === 'dosen' && $user->dosen)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="gelar" class="form-label">Gelar</label>
                                    <input type="text" name="gelar" id="gelar" class="form-control" value="{{ $user->dosen->gelar ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="no_hp" class="form-label">No. HP</label>
                                    <input type="text" name="no_hp" id="no_hp" class="form-control" value="{{ $user->dosen->no_hp ?? '' }}">
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if ($user->role === 'dosen' || $user->role === 'mahasiswa')
                    <div class="tab-pane fade" id="nav-keahlian" role="tabpanel" aria-labelledby="nav-keahlian-tab">
                        <h5>Keahlian</h5>
                        <div id="keahlian-fields-container">
                            @forelse ($selectedKeahlian as $index => $keahlian)
                                <div class="row keahlian-item mb-2 align-items-center">
                                    <div class="col-md-5">
                                        <input type="text" name="keahlian_items[{{ $index }}][nama]" class="form-control" placeholder="Nama Keahlian (mis: Pemrograman Python)" value="{{ $keahlian->keahlian_nama }}">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="keahlian_items[{{ $index }}][sertifikasi]" class="form-control" placeholder="Sertifikasi (Opsional)" value="{{ $keahlian->sertifikasi }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-danger remove-item-btn">Hapus</button>
                                    </div>
                                </div>
                            @empty
                                <div class="row keahlian-item mb-2 align-items-center">
                                    <div class="col-md-5">
                                        <input type="text" name="keahlian_items[0][nama]" class="form-control" placeholder="Nama Keahlian (mis: Pemrograman Python)">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="keahlian_items[0][sertifikasi]" class="form-control" placeholder="Sertifikasi (Opsional)">
                                    </div>
                                    <div class="col-md-2">
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" id="add-keahlian-btn" class="btn btn-sm btn-outline-primary mt-2">Tambah Keahlian</button>
                    </div>
                    @endif

                    @if ($user->role === 'dosen' || $user->role === 'mahasiswa')
                    <div class="tab-pane fade" id="nav-pengalaman" role="tabpanel" aria-labelledby="nav-pengalaman-tab">
                        <h5>Pengalaman</h5>
                        <div id="pengalaman-fields-container">
                            @forelse ($selectedPengalaman as $index => $pengalaman)
                                <div class="pengalaman-item border rounded p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Nama Pengalaman</label>
                                            <input type="text" name="pengalaman_items[{{ $index }}][pengalaman_nama]" class="form-control" value="{{ $pengalaman->pengalaman_nama }}">
                                        </div>
                                        <div class="col-md-2 mb-2 align-self-end">
                                            <div class="form-check">
                                                <input class="form-check-input masih-bekerja-checkbox" type="checkbox" name="pengalaman_items[{{ $index }}][masih_bekerja]" id="masih_bekerja_{{ $index }}" {{ !$pengalaman->tanggal_selesai ? 'checked' : '' }}>
                                                <label class="form-check-label" for="masih_bekerja_{{ $index }}">Saat ini</label>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label class="form-label">Deskripsi (Opsional)</label>
                                            <textarea name="pengalaman_items[{{ $index }}][deskripsi]" class="form-control" rows="2">{{ $pengalaman->deskripsi }}</textarea>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger remove-item-btn mt-2">Hapus Pengalaman Ini</button>
                                    <hr>
                                </div>
                            @empty
                                <div class="pengalaman-item border rounded p-3 mb-3">
                                    <div class="row">
                                         <div class="col-md-6 mb-2"><label class="form-label">Nama Pengalaman</label><input type="text" name="pengalaman_items[0][pengalaman_nama]" class="form-control"></div>
                                         <div class="col-md-6 mb-2"><label class="form-label">Nama Perusahaan/Organisasi</label><input type="text" name="pengalaman_items[0][nama_perusahaan]" class="form-control"></div>
                                         <div class="col-md-6 mb-2"><label class="form-label">Lokasi (Opsional)</label><input type="text" name="pengalaman_items[0][lokasi]" class="form-control"></div>
                                         <div class="col-md-6 mb-2"><label class="form-label">Kategori</label><input type="text" name="pengalaman_items[0][pengalaman_kategori]" placeholder="Mis: Pekerjaan, Magang, Proyek" class="form-control"></div>
                                         <div class="col-md-5 mb-2"><label class="form-label">Tanggal Mulai</label><input type="date" name="pengalaman_items[0][tanggal_mulai]" class="form-control"></div>
                                         <div class="col-md-5 mb-2"><label class="form-label">Tanggal Selesai</label><input type="date" name="pengalaman_items[0][tanggal_selesai]" class="form-control tanggal-selesai-input"></div>
                                         <div class="col-md-2 mb-2 align-self-end"><div class="form-check"><input class="form-check-input masih-bekerja-checkbox" type="checkbox" name="pengalaman_items[0][masih_bekerja]" id="masih_bekerja_0"><label class="form-check-label" for="masih_bekerja_0">Saat ini</label></div></div>
                                         <div class="col-12 mb-2"><label class="form-label">Deskripsi (Opsional)</label><textarea name="pengalaman_items[0][deskripsi]" class="form-control" rows="2"></textarea></div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" id="add-pengalaman-btn" class="btn btn-sm btn-outline-primary mt-2">Tambah Pengalaman</button>
                    </div>
                    @endif

                    @if ($user->role === 'dosen' || $user->role === 'mahasiswa')
                    <div class="tab-pane fade" id="nav-minat" role="tabpanel" aria-labelledby="nav-minat-tab">
                        <h5>Minat</h5>
                        <div id="minat-fields-container">
                             @forelse ($selectedMinat as $index => $minat)
                                <div class="row minat-item mb-2 align-items-center">
                                    <div class="col-md-10">
                                        <input type="text" name="minat_items[]" class="form-control" placeholder="Nama Minat" value="{{ $minat->nama_minat }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-danger remove-item-btn">Hapus</button>
                                    </div>
                                </div>
                            @empty
                                <div class="row minat-item mb-2 align-items-center">
                                    <div class="col-md-10">
                                        <input type="text" name="minat_items[]" class="form-control" placeholder="Nama Minat">
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" id="add-minat-btn" class="btn btn-sm btn-outline-primary mt-2">Tambah Minat</button>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // --- Keahlian ---
    let keahlianIndex = {{ $selectedKeahlian->count() > 0 ? $selectedKeahlian->count() : 1 }};
    $('#add-keahlian-btn').click(function() {
        $('#keahlian-fields-container').append(`
            <div class="row keahlian-item mb-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="keahlian_items[${keahlianIndex}][nama]" class="form-control" placeholder="Nama Keahlian">
                </div>
                <div class="col-md-5">
                    <input type="text" name="keahlian_items[${keahlianIndex}][sertifikasi]" class="form-control" placeholder="Sertifikasi (Opsional)">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-danger remove-item-btn">Hapus</button>
                </div>
            </div>
        `);
        keahlianIndex++;
        updateRemoveButtons('#keahlian-fields-container');
    });

    // --- Minat ---
    $('#add-minat-btn').click(function() {
        $('#minat-fields-container').append(`
            <div class="row minat-item mb-2 align-items-center">
                <div class="col-md-10">
                    <input type="text" name="minat_items[]" class="form-control" placeholder="Nama Minat">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-danger remove-item-btn">Hapus</button>
                </div>
            </div>
        `);
        updateRemoveButtons('#minat-fields-container');
    });

    // --- Pengalaman ---
    let pengalamanIndex = {{ $selectedPengalaman->count() > 0 ? $selectedPengalaman->count() : 1 }};
    $('#add-pengalaman-btn').click(function() {
        const newItemHtml = `
            <div class="pengalaman-item border rounded p-3 mb-3">
                <div class="row">
                    <div class="col-md-6 mb-2"><label class="form-label">Nama Pengalaman</label><input type="text" name="pengalaman_items[${pengalamanIndex}][pengalaman_nama]" class="form-control"></div>
                    <div class="col-md-6 mb-2"><label class="form-label">Nama Perusahaan/Organisasi</label><input type="text" name="pengalaman_items[${pengalamanIndex}][nama_perusahaan]" class="form-control"></div>
                    <div class="col-md-6 mb-2"><label class="form-label">Lokasi (Opsional)</label><input type="text" name="pengalaman_items[${pengalamanIndex}][lokasi]" class="form-control"></div>
                    <div class="col-md-6 mb-2"><label class="form-label">Kategori</label><input type="text" name="pengalaman_items[${pengalamanIndex}][pengalaman_kategori]" placeholder="Mis: Pekerjaan, Magang, Proyek" class="form-control"></div>
                    <div class="col-md-5 mb-2"><label class="form-label">Tanggal Mulai</label><input type="date" name="pengalaman_items[${pengalamanIndex}][tanggal_mulai]" class="form-control"></div>
                    <div class="col-md-5 mb-2"><label class="form-label">Tanggal Selesai</label><input type="date" name="pengalaman_items[${pengalamanIndex}][tanggal_selesai]" class="form-control tanggal-selesai-input"></div>
                    <div class="col-md-2 mb-2 align-self-end"><div class="form-check"><input class="form-check-input masih-bekerja-checkbox" type="checkbox" name="pengalaman_items[${pengalamanIndex}][masih_bekerja]" id="masih_bekerja_${pengalamanIndex}"><label class="form-check-label" for="masih_bekerja_${pengalamanIndex}">Saat ini</label></div></div>
                    <div class="col-12 mb-2"><label class="form-label">Deskripsi (Opsional)</label><textarea name="pengalaman_items[${pengalamanIndex}][deskripsi]" class="form-control" rows="2"></textarea></div>
                </div>
                <button type="button" class="btn btn-sm btn-danger remove-item-btn mt-2">Hapus Pengalaman Ini</button>
                <hr>
            </div>`;
        $('#pengalaman-fields-container').append(newItemHtml);
        pengalamanIndex++;
        updateRemoveButtons('#pengalaman-fields-container');
    });

    // --- Prestasi ---
    let prestasiIndex = {{ $user->role === 'mahasiswa' && $selectedPrestasi->count() > 0 ? $selectedPrestasi->count() : 1 }};
    $('#add-prestasi-btn').click(function() {
        const newItemHtml = `
            <div class="prestasi-item border rounded p-3 mb-3">
                <div class="row">
                    <div class="col-md-6 mb-2"><label class="form-label">Nama Prestasi/Penghargaan</label><input type="text" name="prestasi_items[${prestasiIndex}][nama_prestasi]" class="form-control"></div>
                    <div class="col-md-6 mb-2"><label class="form-label">Tingkat</label><input type="text" name="prestasi_items[${prestasiIndex}][tingkat]" placeholder="Mis: Universitas, Nasional" class="form-control"></div>
                    <div class="col-md-6 mb-2"><label class="form-label">Tahun</label><input type="number" name="prestasi_items[${prestasiIndex}][tahun]" placeholder="YYYY" class="form-control"></div>
                    <div class="col-md-6 mb-2"><label class="form-label">Penyelenggara (Opsional)</label><input type="text" name="prestasi_items[${prestasiIndex}][penyelenggara]" class="form-control"></div>
                    <div class="col-12 mb-2"><label class="form-label">Deskripsi (Opsional)</label><textarea name="prestasi_items[${prestasiIndex}][deskripsi]" class="form-control" rows="2"></textarea></div>
                </div>
                <button type="button" class="btn btn-sm btn-danger remove-item-btn mt-2">Hapus Prestasi Ini</button>
                <hr>
            </div>`;
        $('#prestasi-fields-container').append(newItemHtml);
        prestasiIndex++;
        updateRemoveButtons('#prestasi-fields-container');
    });


    // Fungsi umum untuk menghapus item
    $('body').on('click', '.remove-item-btn', function() {
        $(this).closest('.keahlian-item, .minat-item, .pengalaman-item, .prestasi-item').remove();
        updateRemoveButtons('#keahlian-fields-container');
        updateRemoveButtons('#minat-fields-container');
        updateRemoveButtons('#pengalaman-fields-container');
        updateRemoveButtons('#prestasi-fields-container');
    });

    // Fungsi untuk disable/enable tanggal selesai
    $('body').on('change', '.masih-bekerja-checkbox', function() {
        const $tanggalSelesaiInput = $(this).closest('.row').find('.tanggal-selesai-input');
        if ($(this).is(':checked')) {
            $tanggalSelesaiInput.prop('disabled', true).val('');
        } else {
            $tanggalSelesaiInput.prop('disabled', false);
        }
    });
    $('.masih-bekerja-checkbox').trigger('change'); // Inisialisasi saat load

    // Fungsi untuk memastikan tombol hapus hanya muncul jika ada lebih dari 1 item
    function updateRemoveButtons(containerSelector) {
        const $container = $(containerSelector);
        if ($container.children().length <= 1) {
            $container.find('.remove-item-btn').first().hide(); // Sembunyikan tombol hapus untuk item pertama jika hanya satu
        } else {
            $container.find('.remove-item-btn').show();
        }
         // Untuk pengalaman dan prestasi, tombol hapus selalu ada per item block
        if (containerSelector === '#pengalaman-fields-container' || containerSelector === '#prestasi-fields-container') {
            $container.find('.remove-item-btn').show();
             if ($container.children().length === 0 && (containerSelector === '#pengalaman-fields-container')) { // Jika tidak ada item, tambahkan satu default
                $('#add-pengalaman-btn').click();
            }
            if ($container.children().length === 0 && (containerSelector === '#prestasi-fields-container')) {
                $('#add-prestasi-btn').click();
            }
        }
    }
    updateRemoveButtons('#keahlian-fields-container');
    updateRemoveButtons('#minat-fields-container');
    // Tidak perlu updateRemoveButtons untuk pengalaman & prestasi karena logicnya sedikit berbeda (selalu ada tombol hapus per item block)


    // Handle form submission
    $('#formUpdateProfile').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');


        $.ajax({
            url: $(this).attr('action'),
            method: "POST", // FormData dengan method PUT/PATCH memerlukan _method
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Pastikan CSRF token ada di meta
            },
            success: function(response) {
                if(response.success){
                    toastr.success(response.message || 'Profil berhasil diperbarui!');
                    $('#updateProfileModal').modal('hide');
                    // Opsi: reload halaman atau update UI secara dinamis
                    setTimeout(() => { location.reload(); }, 1500);
                } else {
                     toastr.error(response.message || 'Terjadi kesalahan.');
                     if(response.errors){
                        // Tampilkan error validasi (jika ada)
                        let errorMsg = '';
                        $.each(response.errors, function(key, value){
                            errorMsg += value.join('<br>') + '<br>';
                        });
                        // Anda bisa menampilkan error ini di suatu tempat di modal
                        console.warn("Validation errors:", response.errors);
                     }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                let errorMessage = 'Gagal memperbarui profil. Coba lagi nanti.';
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    errorMessage = jqXHR.responseJSON.message;
                    if(jqXHR.responseJSON.errors){
                         $.each(jqXHR.responseJSON.errors, function(key, value){
                            errorMessage += '<br>' + value.join('<br>');
                        });
                    }
                }
                toastr.error(errorMessage, 'Error ' + jqXHR.status);
                console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
            },
            complete: function(){
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });
});
</script> --}}

{{-- <form id="formUpdateProfile" method="POST" action="{{ route('profile.update_ajax') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title">Ubah Profil</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </button>
            </div>

            <div class="modal-body">
                <!-- Nama -->
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" value="{{ $user->nama }}" required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>

                <!-- Role Tertampil (tidak bisa diedit) -->
                <div class="form-group">
                    <label>Role</label>
                    <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                </div>

                @if ($user->role === 'dosen')
                    <!-- NIP (tidak bisa diedit) -->
                    <div class="form-group">
                        <label>NIP</label>
                        <input type="text" class="form-control" value="{{ $user->dosen->nip }}" disabled>
                    </div>

                    <!-- Gelar -->
                    <div class="form-group">
                        <label>Gelar</label>
                        <input type="text" name="gelar" class="form-control" value="{{ $user->dosen->gelar }}">
                    </div>

                    <!-- No HP -->
                    <div class="form-group">
                        <label>No. HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ $user->dosen->no_hp }}">
                    </div>
                @elseif($user->role === 'mahasiswa')
                    <!-- NIM (tidak bisa diedit) -->
                    <div class="form-group">
                        <label>NIM</label>
                        <input type="text" class="form-control" value="{{ $user->mahasiswa->nim }}" disabled>
                    </div>

                    <!-- Program Studi -->
                    <div class="form-group">
                        <label>Program Studi</label>
                        <input type="text" class="form-control" value="{{ $user->mahasiswa->prodi->nama ?? '-' }}"
                            disabled>
                    </div>

                    <!-- Periode -->
                    <div class="form-group">
                        <label>Periode</label>
                        <input type="text" class="form-control"
                            value="{{ $user->mahasiswa->periode->tahun_akademik ?? '-' }}" disabled>
                    </div>
                @elseif($user->role === 'admin')
                    <!-- NIP Admin (tidak bisa diedit) -->
                    <div class="form-group">
                        <label>NIP</label>
                        <input type="text" class="form-control" value="{{ $user->admin->nip }}" disabled>
                    </div>
                @endif

                <!-- Minat -->
                <div class="form-group">
                    <label>Minat</label>
                    <input type="text" name="minat" class="form-control"
                        value="{{ $user->minat->first()->minat ?? '' }}">
                </div>

                <!-- Pengalaman -->
                <div class="form-group">
                    <label>Pengalaman</label>
                    <textarea name="pengalaman" class="form-control" rows="2">{{ $user->pengalaman->first()->pengalaman_nama ?? '' }}</textarea>
                </div>

                <!-- Prestasi (khusus mahasiswa) -->
                @if ($user->role === 'mahasiswa')
                    <div class="form-group">
                        <label>Prestasi</label>
                        <textarea name="prestasi" class="form-control" rows="2">{{ $user->mahasiswa->prestasi->first()->nama_prestasi ?? '' }}</textarea>
                    </div>
                @endif

                <!-- Keahlian -->
                <div class="form-group">
                    <label>Keahlian</label>
                    <select name="keahlian_id[]" class="form-control" multiple>
                        @foreach ($keahlianList as $keahlian)
                            <option value="{{ $keahlian->id }}"
                                {{ in_array($keahlian->id, $selectedKeahlianIds ?? []) ? 'selected' : '' }}>
                                {{ $keahlian->keahlian_nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Foto Profil -->
                <div class="form-group">
                    <label>Foto Profil</label>
                    <input type="file" name="profile_photo" class="form-control-file">
                </div>
            </div>

            <div class="modal-footer justify-content-end">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $('#formUpdateProfile').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                toastr.success('Profil berhasil diperbarui');
                $('#updateProfileModal').modal('hide');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function(err) {
                toastr.error('Gagal memperbarui profil');
            }
        });
    });
</script> --}}
