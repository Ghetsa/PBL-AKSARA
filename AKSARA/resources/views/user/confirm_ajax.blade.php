@empty($user)
    {{-- Konten jika user tidak ditemukan, akan dimuat ke dalam modal body --}}
    <div class="modal-header">
        <h5 class="modal-title">Kesalahan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
            <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
            Data yang anda cari tidak ditemukan
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        {{-- Link "Kembali" mungkin tidak relevan di dalam modal, bisa diubah jadi tombol tutup --}}
        {{-- <a href="{{ url('/user') }}" class="btn btn-warning">Kembali</a> --}}
    </div>
@else
    {{-- Konten konfirmasi hapus jika user ditemukan, akan dimuat ke dalam modal body --}}
    {{-- Form akan berada di dalam modal body --}}
    <form action="{{ route('user.delete_ajax', $user->user_id) }}" method="POST" id="form-delete-ajax">
        {{-- Ganti ID form agar lebih spesifik --}}
        @csrf
        @method('DELETE')

        <div class="modal-header">
            <h5 class="modal-title">Hapus Data User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning">
                <h5><i class="icon fas fa-exclamation-triangle"></i> Konfirmasi !!!</h5> {{-- Ubah ikon warning --}}
                Apakah Anda yakin ingin menghapus data user ini?
            </div>
            {{-- Tampilkan detail user yang akan dihapus --}}
            <p>Detail User:</p>
            <table class="table table-sm table-bordered table-striped">
                <tbody>
                    <tr>
                        <th class="text-end">Role User</th>
                        <td>{{ $user->role }}</td>
                    </tr>
                    <tr>
                        <th class="text-end">Nama</th>
                        <td>{{ $user->nama }}</td>
                    </tr>
                    <tr>
                        <th class="text-end">Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th class="text-end">No. Telepon</th>
                        <td>{{ $user->no_telepon }}</td>
                    </tr>
                    <tr>
                        <th class="text-end">Alamat</th>
                        <td>{{ $user->alamat }}</td>
                    </tr>
                    <tr>
                        <th class="text-end">Status</th>
                        <td>{{ $user->status }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button> {{-- Ubah jadi secondary --}}
            <button type="submit" class="btn btn-danger">Ya, Hapus</button> {{-- Ubah jadi danger --}}
        </div>
    </form>
@endempty

{{-- Script ini akan dieksekusi setelah konten dimuat ke dalam modal --}}
<script>
    $(document).ready(function() {
        // Gunakan ID form yang baru dan spesifik
        $("#form-delete-ajax").validate({
            rules: {}, // Tambahkan aturan validasi jika perlu
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        // Tutup modal yang sedang terbuka (yang ID-nya myModal)
                        $("#myModal").modal('hide');

                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            // Reload DataTables
                            dataUser.ajax.reload();
                        } else {
                            // Hapus pesan error sebelumnya
                            $('.error-text').text('');
                            // Tampilkan pesan error dari validasi (jika ada)
                            $.each(response.msgfield, function(prefix, val) {
                                $("#error-" + prefix).text(val[0]);
                            });
                            // Tampilkan SweetAlert error
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response
                                    .message // Pesan error umum atau dari backend
                            });
                        }
                    },
                    error: function(xhr) {
                        $("#myModal").modal(
                            'hide'); // Tutup modal juga saat ada error AJAX
                        let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            try {
                                const jsonError = JSON.parse(xhr.responseText);
                                if (jsonError.message) errorMessage = jsonError.message;
                            } catch (e) {
                                errorMessage = 'Error: ' + xhr.status + ' ' + xhr
                                    .statusText;
                            }
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    }
                });
                return false; // Mencegah submit form standar
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
