{{-- @empty($user)
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
    </div>
@else
    <form action="{{ route('user.delete_ajax', $user->user_id) }}" method="POST" id="form-delete-ajax">
        @csrf
        @method('DELETE')

        <div class="modal-header">
            <h5 class="modal-title">Hapus Data User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning">
                <h5><i class="icon fas fa-exclamation-triangle"></i> Konfirmasi !!!</h5>
                Apakah Anda yakin ingin menghapus data user ini?
            </div>
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
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button> 
            <button type="submit" class="btn btn-danger">Ya, Hapus</button> 
        </div>
    </form>
@endempty --}}

@empty($user)
    {{-- Tampilan Error jika user tidak ditemukan --}}
    <div class="modal-header">
        <h5 class="modal-title text-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>Terjadi Kesalahan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
    </div>
    <div class="modal-body text-center py-4">
        <div class="alert alert-danger d-inline-block">
            <h5 class="alert-heading"><i class="icon fas fa-ban"></i> Gagal Memuat Data!</h5>
            Data user yang Anda cari tidak dapat ditemukan.
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
    </div>
@else
    {{-- Form Konfirmasi Hapus --}}
    <form action="{{ route('user.delete_ajax', $user->user_id) }}" method="POST" id="form-delete-ajax">
        @csrf
        @method('DELETE')

        <div class="modal-header">
            <h5 class="modal-title" id="ajaxModalLabel">
                <i class="fas fa-trash-alt me-2"></i>Konfirmasi Hapus Data User
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <div class="modal-body text-center py-4">
            <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>

            <h4 class="mb-3">Anda Yakin?</h4>
            <p class="text-muted mb-3">Anda akan menghapus data user berikut secara permanen:</p>

            <div class="card bg-light border-danger text-start d-inline-block p-3" style="min-width: 300px;">
                <div class="card-body py-2 px-3">
                    <div class="row">
                        <div class="col-4 fw-bold">Nama</div>
                        <div class="col-8 text-end">{{ $user->nama }}</div>
                    </div>
                    <hr class="my-1">
                    <div class="row">
                        <div class="col-4 fw-bold">Role</div>
                        <div class="col-8 text-end">{{ ucfirst($user->role) }}</div>
                    </div>
                    <hr class="my-1">
                    
                    {{-- Menampilkan NIP atau NIM berdasarkan Role --}}
                    @if ($user->role == 'admin' && $user->admin)
                    <div class="row">
                        <div class="col-4 fw-bold">NIP</div>
                        <div class="col-8 text-end">{{ $user->admin->nip ?: '-' }}</div>
                    </div>
                    <hr class="my-1">
                    @elseif ($user->role == 'dosen' && $user->dosen)
                    <div class="row">
                        <div class="col-4 fw-bold">NIP</div>
                        <div class="col-8 text-end">{{ $user->dosen->nip ?: '-' }}</div>
                    </div>
                    <hr class="my-1">
                    @elseif($user->role == 'mahasiswa' && $user->mahasiswa)
                    <div class="row">
                        <div class="col-4 fw-bold">NIM</div>
                        <div class="col-8 text-end">{{ $user->mahasiswa->nim ?: '-' }}</div>
                    </div>
                    <hr class="my-1">
                    @endif

                    <div class="row">
                        <div class="col-4 fw-bold">Email</div>
                        <div class="col-8 text-end text-break">{{ $user->email }}</div>
                    </div>
                </div>
            </div>
            <div class="text-danger fw-bold mt-4">Tindakan ini tidak dapat dibatalkan!</div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger" id="confirm-delete-btn">Ya, Hapus Data</button>
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
