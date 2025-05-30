<form action="{{ route('admin.lomba.crud.destroy_ajax', $lomba->lomba_id) }}" method="POST" id="formDeleteLombaAdminCrud">
    @csrf
    @method('DELETE')
    <div class="modal-header bg-warning">
        <h5 class="modal-title">Konfirmasi Hapus Lomba</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
    </div>
    <div class="modal-body">
        <p>Anda yakin ingin menghapus data lomba berikut secara permanen?</p>
        <dl class="row">
            <dt class="col-sm-4">Nama Lomba:</dt>
            <dd class="col-sm-8">{{ $lomba->nama_lomba }}</dd>
            <dt class="col-sm-4">Penyelenggara:</dt>
            <dd class="col-sm-8">{{ $lomba->penyelenggara }}</dd>
            <dt class="col-sm-4">Tingkat:</dt>
            <dd class="col-sm-8">{{ ucfirst($lomba->tingkat) }}</dd>
        </dl>
        <div class="alert alert-warning small p-2">
            <strong>Perhatian!</strong> Tindakan ini tidak dapat diurungkan.
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Ya, Hapus Lomba Ini</button>
    </div>
</form>

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