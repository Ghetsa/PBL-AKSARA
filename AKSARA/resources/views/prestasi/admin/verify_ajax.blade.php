<form id="formVerifikasiPrestasi" method="POST" action="{{ route('prestasi.admin.process_verification_ajax', $prestasi->prestasi_id) }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="status_verifikasi" id="hidden_status_verifikasi" value="{{ $prestasi->status_verifikasi }}">

    <div class="modal-header bg-light">
        <h5 class="modal-title"><i class="fas fa-trophy me-2"></i>Verifikasi Prestasi Mahasiswa: {{ Str::limit($prestasi->nama_prestasi, 45) }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body p-lg-4" style="max-height: 68vh; overflow-y: auto;">
        <div class="row">
            {{-- Kolom Kiri: Detail Pengajuan --}}
            <div class="col-12 col-lg-7 border-end-lg pe-lg-4">
                <h5 class="fw-bold mb-3 text-center font-weight-bold"><i class="fas fa-award me-2 text-warning"></i>{{ $prestasi->nama_prestasi }}</h5>
                            {{-- @if($prestasi->file_bukti && Storage::disk('public')->exists($prestasi->file_bukti))
                            @php
                                $filePath = $prestasi->file_bukti;
                                $fileUrl = asset('storage/' . $filePath);
                                $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                            @endphp

                                @if(in_array($fileExtension, $imageExtensions))
                                <div class="mb-4 text-center">
                                    <a href="{{ $fileUrl }}" target="_blank" title="Klik untuk melihat gambar penuh">
                                        <img src="{{ $fileUrl }}" alt="Bukti Prestasi" class="img-fluid p-1 w-100" style="max-height: 450px; object-fit: contain;">
                                    </a>
                                </div>
                                @else
                                    <a href="{{ $fileUrl }}" target="_blank" class="btn btn-primary w-100">
                                        <i class="fas fa-file-pdf me-2"></i>Lihat / Unduh Bukti ({{ strtoupper($fileExtension) }})
                                    </a>
                                @endif
                            @else
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-1"></i> File bukti tidak ditemukan atau belum diunggah.
                            </div>
                            @endif --}}

                {{-- Card Informasi Mahasiswa --}}
                <div class="card mb-3">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-user-graduate me-2 text-primary"></i>Informasi Mahasiswa</h6>
                    </div>
                    <div class="card-body py-2 px-3">
                        <dl class="row mb-0">
                            <dt class="col-sm-5 text-muted"><i class="fas fa-user fa-fw me-2"></i>Nama</dt>
                            <dd class="col-sm-7">{{ $prestasi->mahasiswa->user->nama ?? 'N/A' }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-id-card fa-fw me-2"></i>NIM</dt>
                            <dd class="col-sm-7">{{ $prestasi->mahasiswa->nim ?? 'N/A' }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-graduation-cap fa-fw me-2"></i>Prodi</dt>
                            <dd class="col-sm-7">{{ $prestasi->mahasiswa->prodi->nama ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>

                {{-- Card Detail Prestasi --}}
                <div class="card mb-3">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-award me-2 text-primary"></i>Detail Prestasi</h6>
                    </div>
                    <div class="card-body py-2 px-3">
                        <dl class="row mb-0">
                            <dt class="col-sm-5 text-muted"><i class="fas fa-medal fa-fw me-2"></i>Nama Prestasi</dt>
                            <dd class="col-sm-7">{{ ucfirst($prestasi->nama_prestasi) }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-swatchbook fa-fw me-2"></i>Kategori</dt>
                            <dd class="col-sm-7">{{ ucfirst($prestasi->kategori) }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-tags fa-fw me-2"></i>Bidang</dt>
                            <dd class="col-sm-7">{{ ucfirst($prestasi->bidang->bidang_nama) }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-signal fa-fw me-2"></i>Tingkat</dt>
                            <dd class="col-sm-7">{{ ucfirst($prestasi->tingkat) }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-calendar-alt fa-fw me-2"></i>Tahun</dt>
                            <dd class="col-sm-7">{{ $prestasi->tahun }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-university fa-fw me-2"></i>Penyelenggara</dt>
                            <dd class="col-sm-7">{{ $prestasi->penyelenggara }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-chalkboard-teacher fa-fw me-2"></i>Dosen Pembina</dt>
                            <dd class="col-sm-7">{{ $prestasi->dosen->user->nama ?? 'Tidak ada' }}</dd>
                        </dl>
                    </div>
                </div>
                
                {{-- Card Bukti Prestasi --}}
                <div class="card mb-3">
                     <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-file-alt me-2 text-primary"></i>Bukti Prestasi</h6>
                    </div>
                    <div class="card-body text-center p-3">
                        {{-- @if($prestasi->file_bukti && Storage::disk('public')->exists($prestasi->file_bukti))
                            <a href="{{ asset('storage/' . $prestasi->file_bukti) }}" target="_blank" class="btn btn-primary w-100">
                                <i class="fas fa-search-plus me-2"></i>Lihat / Unduh Sertifikat
                            </a> --}}
                            @if($prestasi->file_bukti && Storage::disk('public')->exists($prestasi->file_bukti))
                            @php
                                $filePath = $prestasi->file_bukti;
                                $fileUrl = asset('storage/' . $filePath);
                                $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                            @endphp

                                @if(in_array($fileExtension, $imageExtensions))
                                    <a href="{{ $fileUrl }}" target="_blank" title="Klik untuk melihat gambar penuh">
                                        <img src="{{ $fileUrl }}" alt="Bukti Prestasi" class="img-fluid rounded border p-1 w-100" style="max-height: 450px; object-fit: contain;">
                                    </a>
                                @else
                                    <a href="{{ $fileUrl }}" target="_blank" class="btn btn-primary w-100">
                                        <i class="fas fa-file-pdf me-2"></i>Lihat / Unduh Bukti ({{ strtoupper($fileExtension) }})
                                    </a>
                                @endif
                            @else
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-1"></i> File bukti tidak ditemukan atau belum diunggah.
                            </div>
                            @endif
                        {{-- 
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-1"></i> File sertifikat tidak ditemukan atau belum diunggah.
                            </div>
                        @endif --}}
                    </div>
                </div>

            </div>

            {{-- Kolom Kanan: Panel Verifikasi --}}
            <div class="col-12 col-lg-5 ps-lg-4 mt-4 mt-lg-0">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-dark">Form Verifikasi</h6>
                        <hr class="my-2">
                        <div class="mb-3">
                            <span class="fw-semibold">Status Saat Ini:</span>
                            {!! $prestasi->status_verifikasi_badge !!}
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="catatan_verifikasi" class="form-label fw-semibold">Catatan Verifikasi:</label>
                            <textarea class="form-control" id="catatan_verifikasi" name="catatan_verifikasi" rows="4" placeholder="Wajib diisi jika menolak pengajuan.">{{ old('catatan_verifikasi', $prestasi->catatan_verifikasi ?? '') }}</textarea>
                            <span id="error-catatan_verifikasi" class="invalid-feedback"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label fw-semibold">Aksi:</label>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-success btn-verify-prestasi-action" data-status="disetujui">
                                    <i class="fas fa-check-circle me-2"></i>Setujui
                                </button>
                                <button type="button" class="btn btn-danger btn-verify-prestasi-action" data-status="ditolak">
                                    <i class="fas fa-times-circle me-2"></i>Tolak
                                </button>
                                @if($prestasi->status_verifikasi != 'pending')
                                <button type="button" class="btn btn-warning btn-sm btn-verify-prestasi-action" data-status="pending">
                                    <i class="fas fa-history me-2"></i>Kembalikan ke Pending
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
    </div>
</form>

{{-- Script AJAX dari file asli tidak perlu diubah karena ID dan Class tetap sama --}}
<script>
$(document).ready(function() {
    // Handler untuk membersihkan error validasi saat input berubah
    $('#formVerifikasiPrestasi .form-control').on('input', function() {
        if ($(this).hasClass('is-invalid')) {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').text('').hide();
        }
    });
    
    // Handler untuk tombol aksi
    $('.btn-verify-prestasi-action').on('click', function(e) {
        e.preventDefault();
        const status = $(this).data('status');
        $('#hidden_status_verifikasi').val(status);
        
        // Reset validasi
        $('#catatan_verifikasi').removeClass('is-invalid');
        $('#error-catatan_verifikasi').hide();

        // Validasi frontend sederhana
        if (status === 'ditolak' && $('#catatan_verifikasi').val().trim() === '') {
            $('#catatan_verifikasi').addClass('is-invalid');
            $('#error-catatan_verifikasi').text('Catatan verifikasi wajib diisi jika status ditolak.').show();
            Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: 'Mohon isi catatan mengapa pengajuan ini ditolak.' });
            return; // Hentikan proses jika validasi gagal
        }

        submitVerificationForm();
    });

    function submitVerificationForm() {
        const form = $('#formVerifikasiPrestasi');
        const submitButton = $('.btn-verify-prestasi-action[data-status="' + $('#hidden_status_verifikasi').val() + '"]');
        const originalButtonHtml = submitButton.html();

        $.ajax({
            url: form.attr('action'),
            method: 'POST', // Method tetap POST karena form, @method('PUT') dihandle oleh Laravel
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function() {
                $('.btn-verify-prestasi-action').prop('disabled', true);
                submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');
            },
            success: function(response) {
                if (response.status === true) {
                    $('#myModalAdmin').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                    });
                    if (typeof dataDaftarPrestasiAdmin !== 'undefined') {
                        dataDaftarPrestasiAdmin.ajax.reload(null, false); // Reload datatable
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Operasi Gagal',
                        text: response.message || 'Terjadi kesalahan yang tidak diketahui.'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan server.';
                if (xhr.status === 422 && xhr.responseJSON) {
                    // Handle validation errors from backend
                    errorMessage = xhr.responseJSON.message || 'Periksa kembali isian Anda.';
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        $('#' + key).addClass('is-invalid');
                        $('#error-' + key).text(value[0]).show();
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({ icon: 'error', title: 'Oops...', text: errorMessage });
            },
            complete: function() {
                $('.btn-verify-prestasi-action').prop('disabled', false);
                submitButton.html(originalButtonHtml);
            }
        });
    }
});
</script>