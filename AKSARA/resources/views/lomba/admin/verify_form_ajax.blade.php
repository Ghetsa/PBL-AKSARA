<form id="formVerifikasiLombaAdmin" method="POST" action="{{ route('admin.lomba.process.verification', $lomba->lomba_id) }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="status_verifikasi" id="hidden_status_verifikasi_lomba" value="{{ $lomba->status_verifikasi }}">

    <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Verifikasi Info Lomba: {{ Str::limit($lomba->nama_lomba, 40) }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
        <h6>Detail Pengajuan Lomba:</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped mb-3">
                <tr><th style="width:35%;">Nama Lomba</th><td>{{ $lomba->nama_lomba }}</td></tr>
                <tr><th>Diajukan Oleh</th><td>{{ $lomba->penginput->nama ?? 'N/A' }} ({{ ucfirst($lomba->penginput->role ?? 'N/A') }})</td></tr>
                <tr><th>Penyelenggara</th><td>{{ $lomba->penyelenggara }}</td></tr>
                <tr><th>Tingkat</th><td>{{ ucfirst($lomba->tingkat) }}</td></tr>
                <tr><th>Kategori</th><td>{{ ucfirst($lomba->kategori) }}</td></tr>
                <tr><th>Bidang Keahlian</th><td>{{ $lomba->bidang_keahlian }}</td></tr>
                <tr><th>Biaya</th><td>{{ $lomba->biaya > 0 ? 'Rp ' . number_format($lomba->biaya, 0, ',', '.') : 'Gratis' }}</td></tr>
                <tr><th>Pendaftaran Dibuka</th><td>{{ $lomba->pembukaan_pendaftaran ? $lomba->pembukaan_pendaftaran->format('d M Y') : '-' }}</td></tr>
                <tr><th>Batas Pendaftaran</th><td>{{ $lomba->batas_pendaftaran ? $lomba->batas_pendaftaran->format('d M Y') : '-' }}</td></tr>
                <tr><th>Link Pendaftaran</th><td>{!! $lomba->link_pendaftaran ? '<a href="'.$lomba->link_pendaftaran.'" target="_blank" rel="noopener noreferrer">Kunjungi</a>' : '-' !!}</td></tr>
                <tr><th>Link Penyelenggara</th><td>{!! $lomba->link_penyelenggara ? '<a href="'.$lomba->link_penyelenggara.'" target="_blank" rel="noopener noreferrer">Kunjungi</a>' : '-' !!}</td></tr>
                <tr>
                    <th>Poster</th>
                    <td>
                        @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
                            <a href="{{ asset('storage/' . $lomba->poster) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-image me-1"></i> Lihat Poster
                            </a>
                        @else
                            <span class="text-muted">Tidak ada poster.</span>
                        @endif
                    </td>
                </tr>
                <tr><th>Tanggal Diajukan</th><td>{{ $lomba->created_at ? $lomba->created_at->format('d M Y H:i') : '-' }}</td></tr>
            </table>
        </div>
        <hr>
        <h6>Form Verifikasi:</h6>
        <div class="form-group row mb-3">
            <label for="admin_catatan_verifikasi_lomba" class="col-sm-3 col-form-label text-sm-end">Catatan Verifikasi</label>
            <div class="col-sm-9">
                <textarea class="form-control" id="admin_catatan_verifikasi_lomba" name="catatan_verifikasi" rows="3" placeholder="Berikan catatan jika pengajuan ditolak atau ada hal yang perlu diperbaiki...">{{ old('catatan_verifikasi', $lomba->catatan_verifikasi ?? '') }}</textarea>
                <small class="form-text text-muted">Catatan ini akan tampil kepada pengguna jika pengajuan ditolak.</small>
                <span class="invalid-feedback error-catatan_verifikasi"></span>
            </div>
        </div>

        <div class="form-group row mt-4">
            <label class="col-sm-3 col-form-label text-sm-end">Aksi Verifikasi</label>
            <div class="col-sm-9">
                <button type="button" class="btn btn-success me-2 btn-verify-action-lomba" data-status="disetujui" title="Setujui pengajuan lomba ini">
                    <i class="fas fa-check-circle me-1"></i> Setujui
                </button>
                <button type="button" class="btn btn-danger btn-verify-action-lomba" data-status="ditolak" title="Tolak pengajuan lomba ini">
                    <i class="fas fa-times-circle me-1"></i> Tolak
                </button>
                @if($lomba->status_verifikasi !== 'pending')
                <button type="button" class="btn btn-warning ms-2 btn-verify-action-lomba" data-status="pending" title="Kembalikan status ke Menunggu (Pending)">
                    <i class="fas fa-history me-1"></i> Kembalikan ke Pending
                </button>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
    </div>
</form>

<script>
$(document).ready(function() {
    const formVerifikasiAdminLomba = $('#formVerifikasiLombaAdmin');
    const hiddenStatusInputAdminLomba = $('#hidden_status_verifikasi_lomba');
    const catatanTextareaAdminLomba = $('#admin_catatan_verifikasi_lomba');

    formVerifikasiAdminLomba.validate({
        rules: {
            catatan_verifikasi: {
                maxlength: 500,
                required: function() {
                    return hiddenStatusInputAdminLomba.val() === 'ditolak' && catatanTextareaAdminLomba.val().trim() === '';
                }
            }
        },
        messages: {
            catatan_verifikasi: {
                maxlength: "Catatan tidak boleh lebih dari 500 karakter.",
                required: "Catatan verifikasi wajib diisi jika status ditolak."
            }
        },
        // ... (errorPlacement, highlight, unhighlight sama seperti form lainnya) ...
        errorElement: 'span',
        errorPlacement: function (error, element) { error.addClass('invalid-feedback'); element.closest('.col-sm-9').append(error); },
        highlight: function (element) { $(element).addClass('is-invalid'); },
        unhighlight: function (element) { $(element).removeClass('is-invalid').addClass('is-valid'); }
    });

    $('.btn-verify-action-lomba').on('click', function() {
        const status = $(this).data('status');
        hiddenStatusInputAdminLomba.val(status);

        if (formVerifikasiAdminLomba.valid()) {
            submitVerificationFormAdminLomba();
        } else if (status === 'ditolak' && catatanTextareaAdminLomba.val().trim() === '') {
            catatanTextareaAdminLomba.focus();
            Swal.fire('Peringatan', 'Catatan verifikasi wajib diisi jika status ditolak.', 'warning');
        }
    });

    function submitVerificationFormAdminLomba() {
        var formData = formVerifikasiAdminLomba.serialize();
        const actionButtonsAdminLomba = $('.btn-verify-action-lomba');
        const clickedButton = $('.btn-verify-action-lomba[data-status="'+hiddenStatusInputAdminLomba.val()+'"]');
        const originalButtonHTML = clickedButton.html();

        $.ajax({
            url: formVerifikasiAdminLomba.attr('action'),
            method: 'POST', data: formData, dataType: 'json',
            beforeSend: function() {
                actionButtonsAdminLomba.prop('disabled', true);
                clickedButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');
            },
            success: function(response) {
                if (response.status) {
                    $('#modalVerifikasiLomba').modal('hide');
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message });
                    if (typeof dataTableLombaAdmin !== 'undefined') { dataTableLombaAdmin.ajax.reload(null, false); }
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: response.message || 'Terjadi kesalahan.' });
                }
            },
            error: function(xhr) {
                // ... (error handling Anda) ...
                let errorMessage = 'Terjadi kesalahan server.';
                if (xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
                Swal.fire({ icon: 'error', title: 'Oops...', html: errorMessage });
            },
            complete: function() {
                actionButtonsAdminLomba.prop('disabled', false);
                clickedButton.html(originalButtonHTML); // Kembalikan ke teks tombol awal
            }
        });
    }
});
</script>