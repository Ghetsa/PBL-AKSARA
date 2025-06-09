@empty($lomba)
    {{-- Tampilan Error jika lomba tidak ditemukan --}}
    <div class="modal-header">
        <h5 class="modal-title text-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>Terjadi Kesalahan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
    </div>
    <div class="modal-body text-center py-4">
        <div class="alert alert-danger d-inline-block">
            <h5 class="alert-heading"><i class="icon fas fa-ban"></i> Gagal Memuat Data!</h5>
            Data lomba yang Anda cari tidak dapat ditemukan.
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
    </div>
@else
    {{-- Form Konfirmasi Hapus --}}
    <form action="{{ route('admin.lomba.crud.destroy_ajax', $lomba->lomba_id) }}" method="POST" id="formDeleteLombaAdminCrud">
        @csrf
        @method('DELETE')

        <div class="modal-header">
            <h5 class="modal-title" id="ajaxModalLabel">
                <i class="fas fa-trash-alt me-2"></i>Konfirmasi Hapus Data lomba
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <div class="modal-body text-center py-4">
            <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>

            <h4 class="mb-3">Anda Yakin?</h4>
            <p class="text-muted mb-3">Anda akan menghapus data lomba berikut secara permanen:</p>

            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-sm-10 col-md-8 col-lg-7">
                        <div class="card bg-light border-danger text-start p-3">
                            <div class="card-body py-2 px-3">
                                <div class="row">
                                    <div class="col-5 fw-bold">Nama Lomba</div>
                                    <div class="col-7 text-end text-break">{{ $lomba->nama_lomba }}</div>
                                </div>
                                <hr class="my-1">
                                <div class="row">
                                    <div class="col-5 fw-bold">Penyelenggara</div>
                                    <div class="col-7 text-end text-break">{{ $lomba->penyelenggara }}</div>
                                </div>
                                <hr class="my-1">
                                <div class="row">
                                    <div class="col-5 fw-bold">Tingkat</div>
                                    <div class="col-7 text-end text-break">{{ ucfirst($lomba->tingkat) }}</div>
                                </div>
                                <hr class="my-1">
                                <div class="row">
                                    <div class="col-5 fw-bold">Batas Daftar</div>
                                    <div class="col-7 text-end text-break">{{ $lomba->batas_pendaftaran->isoFormat('D MMMM YYYY') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-danger fw-bold mt-4"><i class="fas fa-exclamation-circle flex-shrink-0 me-2"></i>Data lomba yang dihapus tidak dapat dipulihkan!</div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger" id="confirm-delete-btn">Ya, Hapus Data</button>
        </div>
    </form>
@endempty

<script>
$(document).ready(function() {
    $('#formDeleteLombaAdminCrud').on('submit', function(e) {
        e.preventDefault();
        let form = this;
        const submitButton = $(form).find('button[type="submit"]');
        const originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menghapus...');

        $.ajax({
            url: $(form).attr('action'),
            method: 'DELETE', // Method tetap POST, Laravel handle @method('DELETE')
            data: $(form).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#modalConfirmDeleteLombaAdminCrud').modal('hide'); // Tutup modal konfirmasi
                    Swal.fire('Berhasil!', response.message, 'success');
                    if (typeof dtLombaCrudAdmin !== 'undefined') { dtLombaCrudAdmin.ajax.reload(null, false); }
                } else {
                    Swal.fire('Gagal!', response.message || 'Gagal menghapus data.', 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error!', (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan server.', 'error');
            },
            complete: function() {
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });
});
</script>