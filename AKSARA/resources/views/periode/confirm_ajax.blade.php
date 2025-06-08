@empty($periode)
    {{-- Tampilan Error jika periode tidak ditemukan --}}
    <div class="modal-header">
        <h5 class="modal-title text-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>Terjadi Kesalahan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
    </div>
    <div class="modal-body text-center py-4">
        <div class="alert alert-danger d-inline-block">
            <h5 class="alert-heading"><i class="icon fas fa-ban"></i> Gagal Memuat Data!</h5>
            Data periode semester yang Anda cari tidak dapat ditemukan.
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
    </div>
@else
    {{-- Form Konfirmasi Hapus --}}
    <form action="{{ route('periode.delete_ajax', $periode->periode_id) }}" method="POST" id="form-delete-ajax">
        @csrf
        @method('DELETE')

        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="ajaxModalLabel">
                <i class="fas fa-trash-alt me-2"></i>Hapus Data Periode Semester
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <div class="modal-body text-center py-4">
            {{-- Ikon Peringatan --}}
            <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>

            {{-- Pesan Konfirmasi --}}
            <h4 class="mb-3">Anda Yakin?</h4>
            <p class="text-muted mb-3">Anda akan menghapus data periode semester berikut:</p>
            {{-- <p class="text-muted mb-3">Anda akan menghapus data periode semester berikut. Menghapus data ini dapat mempengaruhi data mahasiswa terkait.</p> --}}

            {{-- Card Detail periode --}}
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                        <div class="card bg-light border-danger text-start p-3">
                            <div class="card-body py-2 px-3">
                                <div class="row">
                                    <div class="col-6 fw-bold">Semester</div>
                                    <div class="col-6 text-end text-break">{{ $periode->semester }}</div>
                                </div>
                                <hr class="my-1">
                                <div class="row">
                                    <div class="col-6 fw-bold">Tahun Akademik</div>
                                    <div class="col-6 text-end text-break">{{ $periode->tahun_akademik }}</div>
                                </div>
                                <hr class="my-1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-danger fw-bold mt-3">Tindakan ini tidak dapat dibatalkan!</div>
            {{-- <div class="text-danger fw-bold mt-4">Menghapus data ini dapat mempengaruhi data mahasiswa terkait!</div> --}}
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger" id="confirm-delete-btn">Ya, Hapus Data</button>
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