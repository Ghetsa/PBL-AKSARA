{{-- resources/views/admin/prestasi/verify_ajax.blade.php --}}
<form id="formVerifikasiPrestasi" method="POST" action="{{ route('prestasi.admin.process_verification_ajax', $prestasi->prestasi_id) }}">
    @csrf
    @method('PUT')
    {{-- Input tersembunyi untuk menyimpan status verifikasi yang dipilih --}}
    <input type="hidden" name="status_verifikasi" id="hidden_status_verifikasi" value="{{ $prestasi->status_verifikasi }}">

    <div class="modal-header">
        <h5 class="modal-title">Verifikasi Prestasi: {{ Str::limit($prestasi->nama_prestasi, 45) }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <h6>Detail Pengajuan:</h6>
        <table class="table table-sm table-bordered mb-3">
            <tr><th style="width:30%;">Nama Mahasiswa</th><td>{{ $prestasi->mahasiswa->user->nama ?? 'N/A' }}</td></tr>
            <tr><th>NIM</th><td>{{ $prestasi->mahasiswa->nim ?? 'N/A' }}</td></tr>
            <tr><th>Prodi</th><td>{{ $prestasi->mahasiswa->prodi->nama ?? 'N/A' }}</td></tr>
            <tr><th>Nama Prestasi</th><td>{{ $prestasi->nama_prestasi }}</td></tr>
            <tr><th>Kategori</th><td>{{ ucfirst($prestasi->kategori) }}</td></tr>
            <tr><th>Penyelenggara</th><td>{{ $prestasi->penyelenggara }}</td></tr>
            <tr><th>Tingkat</th><td>{{ ucfirst($prestasi->tingkat) }}</td></tr>
            <tr><th>Tahun</th><td>{{ $prestasi->tahun }}</td></tr>
            <tr>
                <th>Bukti</th>
                <td>
                    @if($prestasi->file_bukti)
                        <a href="{{ asset(Storage::url($prestasi->file_bukti)) }}" target="_blank" class="btn btn-info btn-sm">
                            <i class="fas fa-file-alt me-1"></i> Lihat Bukti
                        </a>
                    @else
                        Tidak ada bukti.
                    @endif
                </td>
            </tr>
            <tr><th>Tgl Pengajuan</th><td>{{ $prestasi->created_at ? $prestasi->created_at->format('d M Y H:i') : '-' }}</td></tr>
             @if($prestasi->dosenPembimbing) 
            <tr><th>Dosen Pembimbing</th><td>{{ $prestasi->dosenPembimbing->user->nama ?? ($prestasi->dosenPembimbing->nama ?? 'N/A') }}</td></tr>
            @endif
        </table>
        <hr>
        <h6>Form Verifikasi:</h6>

        <div class="form-group row mb-3">
            <label for="verify_catatan_verifikasi" class="col-sm-3 col-form-label">Catatan Verifikasi</label>
            <div class="col-sm-9">
                <textarea class="form-control" id="verify_catatan_verifikasi" name="catatan_verifikasi" rows="3" placeholder="Berikan catatan jika prestasi ditolak atau ada hal yang perlu diperbaiki...">{{ old('catatan_verifikasi', $prestasi->catatan_verifikasi) }}</textarea>
                <small class="form-text text-muted">Catatan ini akan tampil kepada mahasiswa jika prestasi ditolak.</small>
                <span class="invalid-feedback error-text" id="error-catatan_verifikasi"></span>
            </div>
        </div>

        <div class="form-group row mt-4">
            <label class="col-sm-3 col-form-label">Aksi</label>
            <div class="col-sm-9">
                <button type="button" class="btn btn-success me-2 btn-verify-action" data-status="disetujui">
                    <i class="fas fa-check-circle me-1"></i> Setujui Prestasi
                </button>
                <button type="button" class="btn btn-danger btn-verify-action" data-status="ditolak">
                    <i class="fas fa-times-circle me-1"></i> Tolak Prestasi
                </button>
                @if($prestasi->status_verifikasi !== 'pending')
                 <button type="button" class="btn btn-warning ms-2 btn-verify-action" data-status="pending">
                    <i class="fas fa-history me-1"></i> Kembalikan ke Pending
                </button>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
    </div>
</form>

<script>
$(document).ready(function() {
    const formVerifikasi = $('#formVerifikasiPrestasi');
    const hiddenStatusInput = $('#hidden_status_verifikasi');
    const catatanTextarea = $('#verify_catatan_verifikasi');

    // Validasi jQuery
    formVerifikasi.validate({
        // 'status_verifikasi' tidak lagi dari select, tapi dari hidden input yang di-set oleh tombol
        rules: {
            catatan_verifikasi: {
                maxlength: 1000,
                required: function(element) {
                    // Catatan wajib jika statusnya 'ditolak'
                    return hiddenStatusInput.val() === 'ditolak';
                }
            }
            // Tidak perlu validasi 'status_verifikasi' di sini karena akan di-set via tombol
        },
        messages: {
            catatan_verifikasi: {
                maxlength: "Catatan tidak boleh lebih dari 1000 karakter.",
                required: "Catatan verifikasi wajib diisi jika status ditolak."
            }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            // Untuk textarea, tempatkan error setelahnya
            if (element.is('textarea')) {
                error.insertAfter(element.next('small').length ? element.next('small') : element);
            } else {
                 element.closest('.col-sm-9').append(error);
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid').removeClass('is-valid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid').addClass('is-valid');
        }
    });

    $('.btn-verify-action').on('click', function() {
        const status = $(this).data('status');
        hiddenStatusInput.val(status); // Set nilai status pada input tersembunyi

        // Trigger validasi sebelum submit, terutama untuk catatan jika menolak
        if (formVerifikasi.valid()) {
            submitVerificationForm();
        } else {
            // Jika validasi gagal (misalnya catatan kosong saat menolak),
            // jQuery validate akan menampilkan pesan error.
            // Anda bisa menambahkan fokus ke field yang error jika mau.
            if (status === 'ditolak' && catatanTextarea.val().trim() === '') {
                catatanTextarea.focus();
            }
        }
    });

    function submitVerificationForm() {
        var formData = formVerifikasi.serialize();
        const submitButtonOriginalText = 'Simpan Verifikasi'; // Atau teks dari tombol yang diklik
        const actionButtons = $('.btn-verify-action'); // Semua tombol aksi

        $.ajax({
            url: formVerifikasi.attr('action'),
            method: 'POST', // Form method adalah PUT, tapi AJAX tetap POST dengan _method di formData
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                actionButtons.prop('disabled', true);
                // Anda bisa menambahkan spinner ke tombol yang diklik
                // $('.btn-verify-action[data-status="'+hiddenStatusInput.val()+'"]').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');
                $('.error-text').text('');
                $('.form-control, .form-select').removeClass('is-invalid');
            },
            success: function(response) {
                if (response.status) {
                    const modalId = formVerifikasi.closest('.modal').attr('id'); // Dapatkan ID modal secara dinamis
                    const modalInstance = bootstrap.Modal.getInstance(document.getElementById(modalId));
                    if(modalInstance) modalInstance.hide();

                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message });

                    // Reload DataTable yang relevan
                    if (typeof dataDaftarPrestasiAdmin !== 'undefined') { // Jika ini variabel global untuk DataTable Admin
                        dataDaftarPrestasiAdmin.ajax.reload();
                    } else if (typeof dataPrestasiMahasiswa !== 'undefined') { // Jika ini DataTable Mahasiswa (kurang relevan di sini)
                        dataPrestasiMahasiswa.ajax.reload();
                    } else {
                         // Fallback jika nama DataTable tidak diketahui, coba reload halaman
                        // location.reload();
                        console.warn('Variabel DataTable tidak ditemukan, silakan sesuaikan nama variabel DataTable untuk reload.');
                    }
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: response.message || 'Terjadi kesalahan.' });
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            $('#error-' + key).text(value[0]).show();
                             $('[name="'+key+'"]').addClass('is-invalid');
                        });
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan server. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                     $.each(xhr.responseJSON.errors, function(key, value) {
                        $('#error-' + key).text(value[0]).show();
                        $('[name="'+key+'"]').addClass('is-invalid');
                    });
                    errorMessage = xhr.responseJSON.message || 'Periksa kembali isian Anda.';
                }
                Swal.fire({ icon: 'error', title: 'Oops...', text: errorMessage });
            },
            complete: function() {
                actionButtons.prop('disabled', false);
                // Kembalikan teks tombol jika Anda mengubahnya menjadi spinner
            }
        });
    }
});
</script>

{{-- <form id="formVerifikasiPrestasi" method="POST" action="{{ route('prestasi.admin.process_verification_ajax', $prestasi->prestasi_id) }}">
    @csrf
    @method('PUT')
    <div class="modal-header">
        <h5 class="modal-title">Verifikasi Prestasi: {{ Str::limit($prestasi->nama_prestasi, 50) }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <h6>Detail Pengajuan:</h6>
        <table class="table table-sm table-bordered mb-3">
            <tr><th style="width:30%;">Nama Mahasiswa</th><td>{{ $prestasi->mahasiswa->user->nama ?? 'N/A' }}</td></tr>
            <tr><th>NIM</th><td>{{ $prestasi->mahasiswa->nim ?? 'N/A' }}</td></tr>
            <tr><th>Prodi</th><td>{{ $prestasi->mahasiswa->prodi->nama ?? 'N/A' }}</td></tr>
            <tr><th>Nama Prestasi</th><td>{{ $prestasi->nama_prestasi }}</td></tr>
            <tr><th>Kategori</th><td>{{ ucfirst($prestasi->kategori) }}</td></tr>
            <tr><th>Penyelenggara</th><td>{{ $prestasi->penyelenggara }}</td></tr>
            <tr><th>Tingkat</th><td>{{ ucfirst($prestasi->tingkat) }}</td></tr>
            <tr><th>Tahun</th><td>{{ $prestasi->tahun }}</td></tr>
            <tr>
                <th>Bukti</th>
                <td>
                    @if($prestasi->file_bukti)
                        <a href="{{ asset(Storage::url($prestasi->file_bukti)) }}" target="_blank" class="btn btn-info btn-sm">
                            <i class="fas fa-file-alt"></i> Lihat Bukti
                        </a>
                    @else
                        Tidak ada bukti.
                    @endif
                </td>
            </tr>
             <tr><th>Tgl Pengajuan</th><td>{{ $prestasi->created_at ? $prestasi->created_at->format('d M Y H:i') : '-' }}</td></tr>
        </table>
        <hr>
        <h6>Form Verifikasi:</h6>
        <div class="form-group row mb-3">
            <label for="verify_status_verifikasi" class="col-sm-3 col-form-label">Status Verifikasi</label>
            <div class="col-sm-9">
                <select class="form-select" id="verify_status_verifikasi" name="status_verifikasi" required>
                    <option value="pending" {{ $prestasi->status_verifikasi == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="disetujui" {{ $prestasi->status_verifikasi == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ $prestasi->status_verifikasi == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <span class="invalid-feedback error-text" id="error-status_verifikasi"></span>
            </div>
        </div>
        <div class="form-group row mb-3">
            <label for="verify_catatan_verifikasi" class="col-sm-3 col-form-label">Catatan Verifikasi</label>
            <div class="col-sm-9">
                <textarea class="form-control" id="verify_catatan_verifikasi" name="catatan_verifikasi" rows="3" placeholder="Catatan jika ditolak atau ada koreksi...">{{ $prestasi->catatan_verifikasi }}</textarea>
                <span class="invalid-feedback error-text" id="error-catatan_verifikasi"></span>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan Verifikasi</button>
    </div>
</form>

<script>
$(document).ready(function() {
    const formVerifikasi = $('#formVerifikasiPrestasi');

    formVerifikasi.validate({
        rules: {
            status_verifikasi: { required: true },
            catatan_verifikasi: { maxlength: 1000 }
        },
        messages: {
            status_verifikasi: "Status verifikasi wajib dipilih.",
            catatan_verifikasi: { maxlength: "Catatan tidak boleh lebih dari 1000 karakter."}
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.col-sm-9').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        },
        submitHandler: function(form) {
            var formData = $(form).serialize(); // Tidak perlu FormData jika tidak ada file upload
            const submitButton = $(form).find('button[type="submit"]');

            $.ajax({
                url: $(form).attr('action'),
                method: 'POST', // Tetap POST, karena @method('PUT') di form
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
                     $('.error-text').text(''); // Hapus error text lama
                    $('.form-control, .form-select').removeClass('is-invalid');
                },
                success: function(response) {
                    if (response.status) {
                        // Bootstrap 5: bootstrap.Modal.getInstance(document.getElementById('myModalAdmin')).hide();
                        // Bootstrap 4: $('#myModalAdmin').modal('hide');
                        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('myModalAdmin'));
                        if(modalInstance) modalInstance.hide();

                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message });
                        if (typeof dataDaftarPrestasiAdmin !== 'undefined') {
                            dataDaftarPrestasiAdmin.ajax.reload(); // Reload DataTable admin
                        }
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal!', text: response.message || 'Terjadi kesalahan.' });
                         if (response.errors) {
                            $.each(response.errors, function(key, value) {
                                $('#error-' + key).text(value[0]).show(); // ID error span "error-nama_field"
                                $('#verify_' + key).addClass('is-invalid'); // ID input "verify_nama_field"
                            });
                        }
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan server. Silakan coba lagi.';
                     if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $('#error-' + key).text(value[0]).show();
                            $('#verify_' + key).addClass('is-invalid');
                        });
                        errorMessage = xhr.responseJSON.message || 'Periksa kembali isian Anda.';
                    }
                    Swal.fire({ icon: 'error', title: 'Oops...', text: errorMessage });
                },
                complete: function() {
                    submitButton.prop('disabled', false).text('Simpan Verifikasi');
                }
            });
        }
    });
});
</script> --}}