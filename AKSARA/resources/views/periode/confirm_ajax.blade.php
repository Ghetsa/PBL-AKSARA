@empty($periode)
    {{-- Konten jika periode tidak ditemukan, akan dimuat ke dalam modal body --}}
    <div class="modal-header">
        <h5 class="modal-title">Kesalahan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
            <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
            Data yang anda cari tidak ditemukan
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-secondary">Tutup</button>
        {{-- Link "Kembali" mungkin tidak relevan di dalam modal, bisa diubah jadi tombol tutup --}}
        {{-- <a href="{{ url('/periode') }}" class="btn btn-warning">Kembali</a> --}}
    </div>
@else
    {{-- Konten konfirmasi hapus jika periode ditemukan, akan dimuat ke dalam modal body --}}
    {{-- Form akan berada di dalam modal body --}}
    <form action="{{ route('periode.delete_ajax', $periode->periode_id) }}" method="POST" id="form-delete-ajax"> 
        @csrf
        @method('DELETE')

        <div class="modal-header">
            <h5 class="modal-title">Hapus Data periode</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning">
                <h5><i class="icon fas fa-exclamation-triangle"></i> Konfirmasi !!!</h5> {{-- Ubah ikon warning --}}
                Apakah Anda yakin ingin menghapus data program studi ini?
            </div>
            {{-- Tampilkan detail periode yang akan dihapus --}}
            <p>Detail periode semester:</p>
            <ul>
                <li><strong>Semester:</strong> {{ $periode->semester }}</li>
                <li><strong>Tahun Akademik:</strong> {{ $periode->tahun_akademik }}</li>
            </ul>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-secondary">Batal</button> {{-- Ubah jadi secondary --}}
            <button type="submit" class="btn btn-danger">Ya, Hapus</button> {{-- Ubah jadi danger --}}
        </div>
    </form>
@endempty

{{-- Script ini akan dieksekusi setelah konten dimuat ke dalam modal --}}
<script>
    $(document).ready(function () {
        // Gunakan ID form yang baru dan spesifik
        $("#form-delete-ajax").validate({
            rules: {}, // Tambahkan aturan validasi jika perlu
            submitHandler: function (form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function (response) {
                        // Tutup modal yang sedang terbuka (yang ID-nya myModal)
                        $("#myModal").modal('hide');

                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            // Reload DataTables
                            dataPeriode.ajax.reload();
                        } else {
                             // Hapus pesan error sebelumnya
                            $('.error-text').text('');
                            // Tampilkan pesan error dari validasi (jika ada)
                            $.each(response.msgfield, function (prefix, val) {
                                $("#error-" + prefix).text(val[0]);
                            });
                             // Tampilkan SweetAlert error
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message // Pesan error umum atau dari backend
                            });
                        }
                    },
                    error: function(xhr) {
                        $("#myModal").modal('hide'); // Tutup modal juga saat ada error AJAX
                        let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                         if (xhr.responseJSON && xhr.responseJSON.message) {
                             errorMessage = xhr.responseJSON.message;
                         } else if (xhr.responseText) {
                            try {
                                const jsonError = JSON.parse(xhr.responseText);
                                if (jsonError.message) errorMessage = jsonError.message;
                            } catch (e) {
                                errorMessage = 'Error: ' + xhr.status + ' ' + xhr.statusText;
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
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>