{{-- profil/change_password.blade.php --}}
<div class="modal-header">
    <h5 class="modal-title" id="changePasswordModalLabel">Ubah Password</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="formChangePassword" method="POST" action="{{ route('profil.update_password') }}">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="form-group mb-3">
            <label for="current_password" class="form-label">Password Lama <span class="text-danger">*</span></label>
            <input type="password" name="current_password" id="current_password" class="form-control" required>
            <span class="invalid-feedback error-current_password"></span>
        </div>
        <div class="form-group mb-3">
            <label for="new_password" class="form-label">Password Baru <span class="text-danger">*</span></label>
            <input type="password" name="new_password" id="new_password" class="form-control" required>
            <small class="form-text text-muted">Minimal 8 karakter, kombinasi huruf besar, huruf kecil, angka, dan simbol.</small>
            <span class="invalid-feedback error-new_password"></span>
        </div>
        <div class="form-group mb-3">
            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
            <span class="invalid-feedback error-new_password_confirmation"></span>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#formChangePassword').on('submit', function(e) {
        e.preventDefault();
        let form = this;
        let formData = new FormData(form);
        const submitButton = $(form).find('button[type="submit"]');
        const originalButtonText = submitButton.html();

        // Hapus pesan error sebelumnya dan class invalid
        $(form).find('.invalid-feedback').text('').hide();
        $(form).find('.form-control').removeClass('is-invalid');

        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

        $.ajax({
            url: $(form).attr('action'),
            method: 'POST', 
            data: formData,
            processData: false,
            contentType: false,
            // headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, // Sudah dihandle global jika ada $.ajaxSetup

            success: function(response) {
                if (response.success) {
                    $('#changePasswordModal').modal('hide'); // Asumsi ID modal adalah 'changePasswordModal'
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: response.message,
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else {
                    // Jika server mengembalikan success:false tapi status 200 OK
                     Swal.fire({ icon: 'error', title: 'Gagal', text: response.message || 'Tidak dapat mengubah password.' });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) { // Error validasi
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, messages) {
                        let inputElement = $(form).find(`[name="${key}"]`);
                        inputElement.addClass('is-invalid');
                        // Cari span.invalid-feedback dengan class error-{key} atau fallback ke next()
                        let errorContainer = inputElement.closest('.form-group').find('.invalid-feedback.error-' + key);
                        if (!errorContainer.length) {
                             errorContainer = inputElement.next('.invalid-feedback');
                        }
                        if (errorContainer.length) {
                            errorContainer.text(messages[0]).show();
                        } else {
                             // Fallback jika tidak ada elemen invalid-feedback yang sesuai
                            inputElement.after('<span class="invalid-feedback d-block error-' + key + '">' + messages[0] + '</span>');
                        }
                    });
                    Swal.fire({ icon: 'error', title: 'Validasi Gagal', html: xhr.responseJSON.message || 'Periksa kembali isian Anda.<br>Pastikan semua field yang wajib terisi dengan benar.' });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan server. Silakan coba lagi.'
                    });
                }
            },
            complete: function() {
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });
});
</script>