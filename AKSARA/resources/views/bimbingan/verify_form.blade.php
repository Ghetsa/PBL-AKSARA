{{-- resources/views/bimbingan/verify_ajax.blade.php --}}
<form id="formVerifikasiBimbingan" method="POST" action="{{ route('bimbingan.verify_process', $prestasi->prestasi_id) }}">
    @csrf
    @method('PUT')

    {{-- Hidden input status --}}
    <input type="hidden" name="status_verifikasi" id="hidden_status_verifikasi" value="">

    <div class="modal-header">
        <h5 class="modal-title">Verifikasi Prestasi Mahasiswa Bimbingan: {{ Str::limit($prestasi->nama_prestasi, 50) }}</h5>
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
            <tr><th>Tgl Pengajuan</th><td>{{ $prestasi->created_at?->format('d M Y H:i') ?? '-' }}</td></tr>
        </table>

        <hr>
        <h6>Form Verifikasi:</h6>

        <div class="form-group row mb-3">
            <label for="verify_catatan_verifikasi" class="col-sm-3 col-form-label">Catatan Verifikasi</label>
            <div class="col-sm-9">
                <textarea class="form-control" id="verify_catatan_verifikasi" name="catatan_verifikasi" rows="3" placeholder="Berikan catatan jika prestasi ditolak...">{{ old('catatan_verifikasi', $prestasi->catatan_verifikasi) }}</textarea>
                <small class="form-text text-muted">Catatan ini akan tampil jika prestasi ditolak.</small>
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
    const form = $('#formVerifikasiBimbingan');
    const hiddenStatus = $('#hidden_status_verifikasi');
    const catatan = $('#verify_catatan_verifikasi');

    form.validate({
        rules: {
            catatan_verifikasi: {
                maxlength: 1000,
                required: function () {
                    return hiddenStatus.val() === 'ditolak';
                }
            }
        },
        messages: {
            catatan_verifikasi: {
                required: "Catatan wajib diisi jika prestasi ditolak.",
                maxlength: "Catatan maksimal 1000 karakter."
            }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            if (element.is('textarea')) {
                error.insertAfter(element.next('small').length ? element.next('small') : element);
            } else {
                element.closest('.col-sm-9').append(error);
            }
        },
        highlight: function (element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        }
    });

    $('.btn-verify-action').on('click', function () {
        const status = $(this).data('status');
        hiddenStatus.val(status);

        if (form.valid()) {
            kirimForm();
        } else if (status === 'ditolak' && catatan.val().trim() === '') {
            catatan.focus();
        }
    });

    function kirimForm() {
        const formData = form.serialize();
        const buttons = $('.btn-verify-action');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function () {
                buttons.prop('disabled', true);
                $('.error-text').text('');
                $('.form-control').removeClass('is-invalid');
            },
            success: function (res) {
                if (res.status) {
                    const modalId = form.closest('.modal').attr('id');
                    const modalInstance = bootstrap.Modal.getInstance(document.getElementById(modalId));
                    if (modalInstance) modalInstance.hide();

                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message });

                    if (typeof dataDaftarMahasiswaBimbingan !== 'undefined') {
                        dataDaftarMahasiswaBimbingan.ajax.reload();
                    } else {
                        console.warn('DataTable belum terdefinisi.');
                    }
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            },
            error: function (xhr) {
                let msg = 'Terjadi kesalahan.';
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    $.each(xhr.responseJSON.errors, function (key, val) {
                        $('#error-' + key).text(val[0]).show();
                        $('[name="' + key + '"]').addClass('is-invalid');
                    });
                    msg = xhr.responseJSON.message || 'Validasi gagal.';
                }

                Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
            },
            complete: function () {
                buttons.prop('disabled', false);
            }
        });
    }
});
</script>
