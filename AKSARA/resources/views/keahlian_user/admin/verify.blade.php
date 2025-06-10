<form id="formVerifikasiKeahlianAdmin" method="POST" action="{{ route('keahlian_user.admin.process_verification_ajax', $keahlianUser->keahlian_user_id) }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="status_verifikasi" id="hidden_status_verifikasi_keahlian" value="{{ $keahlianUser->status_verifikasi }}">

    <div class="modal-header bg-light">
        <h5 class="modal-title"><i class="fas fa-user-check me-2"></i>Verifikasi Keahlian Mahasiswa: {{ Str::limit($keahlianUser->bidang->bidang_nama, 45) }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body p-lg-4" style="max-height: 68vh; overflow-y: auto;">
        <div class="row">
            {{-- Kolom Kiri: Detail Pengajuan --}}
            <div class="col-12 col-lg-7 border-end-lg pe-lg-4">
                {{-- <h5 class="fw-bold mb-3"><i class="fas fa-star me-2 text-warning"></i>{{ $keahlianUser->bidang->bidang_nama ?? 'Keahlian' }}</h5> --}}

                {{-- Card Informasi Pengguna --}}
                <div class="card mb-3">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-user-graduate me-2 text-primary"></i>Informasi Pengguna</h6>
                    </div>
                    <div class="card-body py-2 px-3">
                        <dl class="row mb-0">
                            <dt class="col-sm-5 text-muted"><i class="fas fa-user fa-fw me-2"></i>Nama</dt>
                            <dd class="col-sm-7">{{ $keahlianUser->user->nama ?? 'N/A' }}</dd>

                            @if($keahlianUser->user->role == 'mahasiswa' && $keahlianUser->user->mahasiswa)
                                <dt class="col-sm-5 text-muted"><i class="fas fa-id-card fa-fw me-2"></i>NIM</dt>
                                <dd class="col-sm-7">{{ $keahlianUser->user->mahasiswa->nim ?? 'N/A' }}</dd>

                                <dt class="col-sm-5 text-muted"><i class="fas fa-graduation-cap fa-fw me-2"></i>Prodi</dt>
                                <dd class="col-sm-7">{{ $keahlianUser->user->mahasiswa->prodi->nama ?? 'N/A' }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>

                {{-- KARTU DETAIL SERTIFIKASI (BARU) --}}
                <div class="card mb-3">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-certificate me-2 text-primary"></i>Detail Sertifikasi</h6>
                    </div>
                    <div class="card-body py-2 px-3">
                        <dl class="row mb-0">
                            <dt class="col-sm-5 text-muted"><i class="fas fa-award fa-fw me-2"></i>Nama Sertifikat</dt>
                            <dd class="col-sm-7">{{ $keahlianUser->nama_sertifikat ?? '-' }}</dd>

                            <dt class="col-sm-5 text-muted"><i class="fas fa-tag fa-fw me-2"></i>Bidang</dt>
                            <dd class="col-sm-7">{{ $keahlianUser->bidang->bidang_nama ?? '-' }}</dd>

                            <dt class="col-sm-5 text-muted"><i class="fas fa-building fa-fw me-2"></i>Lembaga</dt>
                            <dd class="col-sm-7">{{ $keahlianUser->lembaga_sertifikasi ?? '-' }}</dd>

                            <dt class="col-sm-5 text-muted"><i class="fas fa-calendar-check fa-fw me-2"></i>Tgl. Perolehan</dt>
                            <dd class="col-sm-7">{{ $keahlianUser->tanggal_perolehan_sertifikat ? \Carbon\Carbon::parse($keahlianUser->tanggal_perolehan_sertifikat)->isoFormat('D MMMM YYYY') : '-' }}</dd>

                            <dt class="col-sm-5 text-muted"><i class="fas fa-calendar-times fa-fw me-2"></i>Tgl. Kedaluwarsa</dt>
                            <dd class="col-sm-7">{{ $keahlianUser->tanggal_kadaluarsa_sertifikat ? \Carbon\Carbon::parse($keahlianUser->tanggal_kadaluarsa_sertifikat)->isoFormat('D MMMM YYYY') : 'Tidak ada' }}</dd>
                        </dl>
                    </div>
                </div>

                {{-- Card Bukti Keahlian/Sertifikat --}}
                <div class="card mb-3">
                     <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-file-contract me-2 text-primary"></i>File Bukti</h6>
                    </div>
                    <div class="card-body p-3 text-center">
                        @if($keahlianUser->sertifikasi && Storage::disk('public')->exists($keahlianUser->sertifikasi))
                            @php
                                $filePath = $keahlianUser->sertifikasi;
                                $fileUrl = asset('storage/' . $filePath);
                                $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                            @endphp

                            @if(in_array($fileExtension, $imageExtensions))
                                <a href="{{ $fileUrl }}" target="_blank" title="Klik untuk melihat gambar penuh">
                                    <img src="{{ $fileUrl }}" alt="Bukti Keahlian" class="img-fluid rounded border p-1 w-100" style="max-height: 400px; object-fit: contain;">
                                </a>
                            @else
                                <a href="{{ $fileUrl }}" target="_blank" class="btn btn-primary w-100">
                                    <i class="fas fa-file-alt me-2"></i>Lihat / Unduh Bukti ({{ strtoupper($fileExtension) }})
                                </a>
                            @endif
                        @else
                            <div class="alert alert-secondary mb-0">
                                <i class="fas fa-times-circle me-1"></i> Tidak ada bukti yang diunggah.
                            </div>
                        @endif
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
                            {!! $keahlianUser->status_verifikasi_badge !!}
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="catatan_verifikasi_keahlian" class="form-label fw-semibold">Catatan Verifikasi:</label>
                            <textarea class="form-control" id="catatan_verifikasi_keahlian" name="catatan_verifikasi" rows="4" placeholder="Wajib diisi jika menolak pengajuan.">{{ old('catatan_verifikasi', $keahlianUser->catatan_verifikasi ?? '') }}</textarea>
                            <span class="invalid-feedback error-catatan_verifikasi"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label fw-semibold">Aksi:</label>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-success btn-verify-action" data-status="disetujui">
                                    <i class="fas fa-check-circle me-2"></i>Setujui
                                </button>
                                <button type="button" class="btn btn-danger btn-verify-action" data-status="ditolak">
                                    <i class="fas fa-times-circle me-2"></i>Tolak
                                </button>
                                @if($keahlianUser->status_verifikasi != 'pending')
                                <button type="button" class="btn btn-warning btn-sm btn-verify-action" data-status="pending">
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

{{-- Script AJAX dari file asli tidak perlu diubah --}}
<script>
$(document).ready(function() {
    const formVerifikasiAdmin = $('#formVerifikasiKeahlianAdmin');
    const hiddenStatusInput = $('#hidden_status_verifikasi_keahlian');
    const catatanTextarea = $('#catatan_verifikasi_keahlian');
    const actionButtonsAdmin = $('.btn-verify-action');

    $('.btn-verify-action').on('click', function() {
        const status = $(this).data('status');
        hiddenStatusInput.val(status); 

        catatanTextarea.removeClass('is-invalid');
        catatanTextarea.next('.invalid-feedback').empty().hide();

        if (status === 'ditolak' && catatanTextarea.val().trim() === '') {
            catatanTextarea.addClass('is-invalid');
            catatanTextarea.next('.invalid-feedback').text('Catatan verifikasi wajib diisi jika status ditolak.').show();
            Swal.fire('Validasi Gagal', 'Mohon isi catatan mengapa pengajuan ini ditolak.', 'error');
            return;
        }
        submitVerificationForm();
    });

    function submitVerificationForm() {
        const formData = formVerifikasiAdmin.serialize();
        const clickedButton = actionButtonsAdmin.filter('[data-status="' + hiddenStatusInput.val() + '"]');
        const originalButtonHTML = clickedButton.html();

        $.ajax({
            url: formVerifikasiAdmin.attr('action'),
            method: 'PUT',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                actionButtonsAdmin.prop('disabled', true);
                clickedButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');
            },
            success: function(response) {
                if (response.status === true) {
                    $('#modalAdminKeahlian').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                    });
                    if (typeof dataTableKeahlianAdmin !== 'undefined') {
                        dataTableKeahlianAdmin.ajax.reload(null, false);
                    }
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: response.message || 'Terjadi kesalahan.' });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan server.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({ icon: 'error', title: 'Oops...', html: errorMessage });
            },
            complete: function() {
                actionButtonsAdmin.prop('disabled', false);
                clickedButton.html(originalButtonHTML);
            }
        });
    }
});
</script>