<form id="formVerifikasiKeahlianAdmin" method="POST" action="{{ route('keahlian_user.admin.process_verification_ajax', $keahlianUser->keahlian_user_id) }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="status_verifikasi" id="hidden_status_verifikasi_keahlian" value="{{ $keahlianUser->status_verifikasi }}">

    <div class="modal-header">
        <h5 class="modal-title">Verifikasi Keahlian: {{ Str::limit($keahlianUser->bidang->bidang_nama ?? 'Keahlian', 40) }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <h6>Detail Pengajuan Keahlian:</h6>
        <table class="table table-sm table-bordered table-striped mb-3">
            <tr><th style="width:35%;">Nama Pengguna</th><td>{{ $keahlianUser->user->nama ?? 'N/A' }}</td></tr>
            <tr><th>Role Pengguna</th><td>{{ ucfirst($keahlianUser->user->role ?? 'N/A') }}</td></tr>
            @if($keahlianUser->user->role == 'mahasiswa' && $keahlianUser->user->mahasiswa)
            <tr><th>NIM</th><td>{{ $keahlianUser->user->mahasiswa->nim ?? 'N/A' }}</td></tr>
            <tr><th>Prodi</th><td>{{ $keahlianUser->user->mahasiswa->prodi->nama ?? 'N/A' }}</td></tr>
            {{-- @elseif($keahlianUser->user->role == 'dosen' && $keahlianUser->user->dosen)
            <tr><th>NIP</th><td>{{ $keahlianUser->user->dosen->nip ?? 'N/A' }}</td></tr>
            @endif --}}
            <tr><th>Bidang Keahlian</th><td>{{ $keahlianUser->bidang->bidang_nama ?? 'N/A' }}</td></tr>
            <tr><th>Nama Sertifikat/Keahlian</th><td>{{ $keahlianUser->nama_sertifikat ?? '-' }}</td></tr>
            <tr><th>Lembaga Penerbit</th><td>{{ $keahlianUser->lembaga_sertifikasi ?? '-' }}</td></tr>
            <tr><th>Tanggal Perolehan</th><td>{{ $keahlianUser->tanggal_perolehan_sertifikat ? $keahlianUser->tanggal_perolehan_sertifikat->format('d M Y') : '-' }}</td></tr>
            <tr><th>Tanggal Kadaluarsa</th><td>{{ $keahlianUser->tanggal_kadaluarsa_sertifikat ? $keahlianUser->tanggal_kadaluarsa_sertifikat->format('d M Y') : '-' }}</td></tr>
            <tr>
                <th>File Bukti/Sertifikat</th>
                <td>
                    @if($keahlianUser->sertifikasi && Storage::disk('public')->exists($keahlianUser->sertifikasi))
                        <a href="{{ asset('storage/' . $keahlianUser->sertifikasi) }}" target="_blank" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-eye me-1"></i> Lihat File
                        </a>
                    @else
                        <span class="text-muted">Tidak ada file.</span>
                    @endif
                </td>
            </tr>
            <tr><th>Tgl Pengajuan</th><td>{{ $keahlianUser->created_at ? $keahlianUser->created_at->format('d M Y H:i') : '-' }}</td></tr>
        </table>
        <hr>
        <h6>Form Verifikasi:</h6>
        <div class="form-group row mb-3">
            <label for="admin_catatan_verifikasi_keahlian" class="col-sm-3 col-form-label">Catatan Verifikasi</label>
            <div class="col-sm-9">
                <textarea class="form-control" id="admin_catatan_verifikasi_keahlian" name="catatan_verifikasi" rows="3" placeholder="Berikan catatan jika pengajuan ditolak atau ada hal yang perlu diperbaiki...">{{ old('catatan_verifikasi', $keahlianUser->catatan_verifikasi) }}</textarea>
                <small class="form-text text-muted">Catatan ini akan tampil kepada pengguna.</small>
            </div>
        </div>

        <div class="form-group row mt-4">
            <label class="col-sm-3 col-form-label">Aksi Verifikasi</label>
            <div class="col-sm-9">
                <button type="button" class="btn btn-success me-2 btn-verify-action-keahlian" data-status="disetujui" title="Setujui pengajuan keahlian ini">
                    <i class="fas fa-check-circle me-1"></i> Setujui
                </button>
                <button type="button" class="btn btn-danger btn-verify-action-keahlian" data-status="ditolak" title="Tolak pengajuan keahlian ini">
                    <i class="fas fa-times-circle me-1"></i> Tolak
                </button>
                @if($keahlianUser->status_verifikasi !== 'pending')
                <button type="button" class="btn btn-warning ms-2 btn-verify-action-keahlian" data-status="pending" title="Kembalikan status ke Menunggu (Pending)">
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
    const formVerifikasiAdmin = $('#formVerifikasiKeahlianAdmin');
    const hiddenStatusInputAdmin = $('#hidden_status_verifikasi_keahlian');
    const catatanTextareaAdmin = $('#admin_catatan_verifikasi_keahlian');

    formVerifikasiAdmin.validate({
        rules: {
            catatan_verifikasi: {
                maxlength: 1000,
                required: function(element) {
                    return hiddenStatusInputAdmin.val() === 'ditolak' && $(element).val().trim() === '';
                }
            }
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
            element.closest('.col-sm-9').append(error);
        },
        highlight: function (element) { $(element).addClass('is-invalid'); },
        unhighlight: function (element) { $(element).removeClass('is-invalid'); }
    });

    $('.btn-verify-action-keahlian').on('click', function() {
        const status = $(this).data('status');
        hiddenStatusInputAdmin.val(status);

        if (formVerifikasiAdmin.valid()) { // Cek validasi sebelum submit
            submitVerificationFormAdmin();
        } else if (status === 'ditolak' && catatanTextareaAdmin.val().trim() === '') {
            catatanTextareaAdmin.focus(); // Fokus jika catatan kosong saat menolak
            Swal.fire('Peringatan', 'Catatan verifikasi wajib diisi jika status ditolak.', 'warning');
        }
    });

    function submitVerificationFormAdmin() {
        var formData = formVerifikasiAdmin.serialize();
        const actionButtonsAdmin = $('.btn-verify-action-keahlian');

        $.ajax({
            url: formVerifikasiAdmin.attr('action'),
            method: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                actionButtonsAdmin.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');
            },
            success: function(response) {
                if (response.status) {
                    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('modalAdminKeahlian'));
                    if(modalInstance) modalInstance.hide();
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message });
                    if (typeof dataTableKeahlianAdmin !== 'undefined') {
                        dataTableKeahlianAdmin.ajax.reload();
                    }
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: response.message || 'Terjadi kesalahan saat verifikasi.' });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan server.';
                if (xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errorsHtml = '<ul>';
                    $.each(xhr.responseJSON.errors, function(key, value) { errorsHtml += `<li>${value[0]}</li>`; });
                    errorsHtml += '</ul>';
                    errorMessage = xhr.responseJSON.message + errorsHtml;
                }
                Swal.fire({ icon: 'error', title: 'Oops...', html: errorMessage });
            },
            complete: function() {
                // Mengembalikan teks tombol ke semula setelah selesai
                actionButtonsAdmin.prop('disabled', false);
                actionButtonsAdmin.filter('[data-status="disetujui"]').html('<i class="fas fa-check-circle me-1"></i> Setujui');
                actionButtonsAdmin.filter('[data-status="ditolak"]').html('<i class="fas fa-times-circle me-1"></i> Tolak');
                actionButtonsAdmin.filter('[data-status="pending"]').html('<i class="fas fa-history me-1"></i> Kembalikan ke Pending');
            }
        });
    }
});
</script>