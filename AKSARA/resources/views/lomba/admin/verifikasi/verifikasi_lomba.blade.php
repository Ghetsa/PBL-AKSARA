<form id="formVerifikasiLombaAdmin" method="POST" action="{{ route('admin.lomba.verifikasi.proses', $lomba->lomba_id) }}">
    @csrf
    @method('PUT') 
    <input type="hidden" name="status_verifikasi" id="hidden_status_verifikasi_lomba" value="{{ $lomba->status_verifikasi }}">

    <div class="modal-header">
        <h5 class="modal-title" id="modalVerifikasiLombaLabel">Verifikasi Info Lomba: {{ Str::limit($lomba->nama_lomba, 40) }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">
        <h6>Detail Pengajuan Lomba:</h6>
        <div class="col-md-12">
            <dl class="row">
                <dt class="col-sm-5 col-lg-4">Nama Lomba:</dt>
                <dd class="col-sm-7 col-lg-8">{{ $lomba->nama_lomba }}</dd>

                <dt class="col-sm-5 col-lg-4">Penyelenggara:</dt>
                <dd class="col-sm-7 col-lg-8">{{ $lomba->penyelenggara }}</dd>

                <dt class="col-sm-5 col-lg-4">Tingkat:</dt>
                <dd class="col-sm-7 col-lg-8">{{ ucfirst($lomba->tingkat) }}</dd>

                <dt class="col-sm-5 col-lg-4">Kategori Peserta:</dt>
                <dd class="col-sm-7 col-lg-8">{{ ucfirst($lomba->kategori) }}</dd>

                <dt class="col-sm-5 col-lg-4">Bidang Lomba:</dt>
                <dd class="col-sm-7 col-lg-8">
                    @if($lomba->bidangKeahlian && $lomba->bidangKeahlian->count() > 0)
                        @foreach($lomba->bidangKeahlian as $detail)
                            @if($detail->bidang) {{-- Memastikan relasi bidang ada --}}
                                <span class="badge bg-light-secondary text-dark me-1 mb-1 p-2">{{ e($detail->bidang->bidang_nama) }}</span>
                            @endif
                        @endforeach
                    @else
                        -
                    @endif
                </dd>
                
                <dt class="col-sm-5 col-lg-4">Biaya Pendaftaran:</dt>
                <dd class="col-sm-7 col-lg-8">
                    @if($lomba->biaya > 0)
                        Rp {{ number_format($lomba->biaya, 0, ',', '.') }}
                    @else
                        <span class="badge bg-light-success text-success px-2 py-1">Gratis</span>
                    @endif
                </dd>

                <dt class="col-sm-5 col-lg-4">Pendaftaran Dibuka:</dt>
                <dd class="col-sm-7 col-lg-8">{{ $lomba->pembukaan_pendaftaran ? $lomba->pembukaan_pendaftaran->isoFormat('D MMMM YYYY') : '-' }}</dd>

                <dt class="col-sm-5 col-lg-4">Batas Pendaftaran:</dt>
                <dd class="col-sm-7 col-lg-8">{{ $lomba->batas_pendaftaran ? $lomba->batas_pendaftaran->isoFormat('D MMMM YYYY') : '-' }}</dd>

                @if($lomba->link_pendaftaran)
                <dt class="col-sm-5 col-lg-4">Link Pendaftaran:</dt>
                <dd class="col-sm-7 col-lg-8"><a href="{{ $lomba->link_pendaftaran }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt me-1"></i> Kunjungi Link</a></dd>
                @endif

                @if($lomba->link_penyelenggara)
                <dt class="col-sm-5 col-lg-4">Link Penyelenggara:</dt>
                <dd class="col-sm-7 col-lg-8"><a href="{{ $lomba->link_penyelenggara }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary"><i class="fas fa-link me-1"></i> Website Resmi</a></dd>
                @endif

                 @if($lomba->inputBy) {{-- Menggunakan relasi inputBy --}}
                <dt class="col-sm-5 col-lg-4">Diajukan Oleh:</dt>
                <dd class="col-sm-7 col-lg-8">
                    {{ $lomba->inputBy->nama ?? 'N/A' }}
                    @if($lomba->inputBy)
                    ({{ ucfirst($lomba->inputBy->role) }})
                    @endif
                </dd>
                @endif

                <dt class="col-sm-5 col-lg-4">Status Verifikasi:</dt>
                <dd class="col-sm-7 col-lg-8">{!! $lomba->status_verifikasi_badge !!}</dd>
            </dl>
        </div>
        {{-- <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped mb-3">
                <tbody> 
                    <tr><th style="width:35%;">Nama Lomba</th><td>{{ $lomba->nama_lomba }}</td></tr>
                    <tr><th>Diajukan Oleh</th><td>{{ $lomba->penginput->nama ?? 'N/A' }} ({{ ucfirst($lomba->penginput->role ?? 'N/A') }})</td></tr>
                    <tr><th>Penyelenggara</th><td>{{ $lomba->penyelenggara }}</td></tr>
                    <tr><th>Tingkat</th><td>{{ ucfirst($lomba->tingkat) }}</td></tr>
                    <tr><th>Kategori</th><td>{{ ucfirst($lomba->kategori) }}</td></tr>
                    <tr><th>Bidang Keahlian</th><td>{{ $lomba->bidang_keahlian }}</td></tr>
                    <tr><th>Biaya</th><td>{{ $lomba->biaya > 0 ? 'Rp ' . number_format($lomba->biaya, 0, ',', '.') : 'Gratis' }}</td></tr>
                    <tr><th>Pendaftaran Dibuka</th><td>{{ $lomba->pembukaan_pendaftaran ? \Carbon\Carbon::parse($lomba->pembukaan_pendaftaran)->format('d M Y') : '-' }}</td></tr>
                    <tr><th>Batas Pendaftaran</th><td>{{ $lomba->batas_pendaftaran ? \Carbon\Carbon::parse($lomba->batas_pendaftaran)->format('d M Y') : '-' }}</td></tr>
                    <tr><th>Link Pendaftaran</th><td>{!! $lomba->link_pendaftaran ? '<a href="'.htmlspecialchars($lomba->link_pendaftaran).'" target="_blank" rel="noopener noreferrer">Kunjungi</a>' : '-' !!}</td></tr>
                    <tr><th>Link Penyelenggara</th><td>{!! $lomba->link_penyelenggara ? '<a href="'.htmlspecialchars($lomba->link_penyelenggara).'" target="_blank" rel="noopener noreferrer">Kunjungi</a>' : '-' !!}</td></tr>
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
                    <tr><th>Tanggal Diajukan</th><td>{{ $lomba->created_at ? \Carbon\Carbon::parse($lomba->created_at)->format('d M Y H:i') : '-' }}</td></tr>
                </tbody>
            </table>
        </div> --}}
        <hr>
        <h6>Form Verifikasi:</h6>
        <div class="form-group row mb-3"> {{-- Consider using Bootstrap's mb-3 for spacing --}}
            <label for="admin_catatan_verifikasi_lomba" class="col-sm-3 col-form-label text-sm-end">Catatan Verifikasi</label>
            <div class="col-sm-9">
                <textarea class="form-control" id="admin_catatan_verifikasi_lomba" name="catatan_verifikasi" rows="3" placeholder="Berikan catatan jika pengajuan ditolak atau ada hal yang perlu diperbaiki...">{{ old('catatan_verifikasi', $lomba->catatan_verifikasi ?? '') }}</textarea>
                <small class="form-text text-muted">Catatan ini akan tampil kepada pengguna jika pengajuan ditolak.</small>
                {{-- Error message will be placed by jQuery Validate --}}
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
        {{-- Tombol submit utama tidak diperlukan jika aksi dikontrol oleh tombol Setujui/Tolak --}}
    </div>
</form>

<script>
// Pastikan skrip ini dieksekusi setelah jQuery dan jQuery Validate dimuat
// Jika file ini dimuat via AJAX ke dalam modal, pastikan skrip dieksekusi setelah konten dimuat
// atau gunakan event delegation jika tombol/form belum ada saat document ready awal.
// Namun, jika ini adalah bagian dari modal yang selalu ada, $(document).ready() cukup.

$(document).ready(function() {
    const formVerifikasiAdminLomba = $('#formVerifikasiLombaAdmin');
    const hiddenStatusInputAdminLomba = $('#hidden_status_verifikasi_lomba');
    const catatanTextareaAdminLomba = $('#admin_catatan_verifikasi_lomba');

    // Inisialisasi jQuery Validate
    if ($.fn.validate) { // Check if validate plugin is loaded
        formVerifikasiAdminLomba.validate({
            rules: {
                catatan_verifikasi: {
                    maxlength: 500,
                    required: function() {
                        // Hanya wajib jika status yang dipilih adalah 'ditolak'
                        return hiddenStatusInputAdminLomba.val() === 'ditolak';
                    }
                }
            },
            messages: {
                catatan_verifikasi: {
                    maxlength: "Catatan tidak boleh lebih dari 500 karakter.",
                    required: "Catatan verifikasi wajib diisi jika status ditolak."
                }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.col-sm-9').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            }
        });
    } else {
        console.warn('jQuery Validate plugin is not loaded.');
    }

    // Event listener untuk tombol aksi verifikasi
    $('.btn-verify-action-lomba').on('click', function() {
        const status = $(this).data('status');
        hiddenStatusInputAdminLomba.val(status); // Set status tersembunyi

        // Hapus status validasi sebelumnya pada catatan
        catatanTextareaAdminLomba.removeClass('is-invalid is-valid');
        catatanTextareaAdminLomba.closest('.col-sm-9').find('.invalid-feedback').remove();

        // Validasi form sebelum submit
        if (formVerifikasiAdminLomba.valid()) {
            submitVerificationFormAdminLomba();
        } else {
            // Jika validasi gagal, jQuery Validate akan menampilkan pesan error.
            // Tambahan Swal bisa jika diperlukan untuk UX, tapi biasanya pesan inline cukup.
            if (status === 'ditolak' && catatanTextareaAdminLomba.val().trim() === '') {
                 // Fokuskan jika field catatan kosong saat menolak,
                 // jQuery validate seharusnya sudah menghighlight errornya
                catatanTextareaAdminLomba.focus();
            }
        }
    });

    function submitVerificationFormAdminLomba() {
    const formVerifikasiAdminLomba = $('#formVerifikasiLombaAdmin'); // Pastikan ini didefinisikan
    const formData = formVerifikasiAdminLomba.serialize(); // Ini akan berisi _token dan data lainnya.
                                                          // _method=PUT juga akan ada, tapi tidak apa-apa,
                                                          // karena method AJAX akan jadi PUT.
    const actionButtonsAdminLomba = $('.btn-verify-action-lomba');
    const hiddenStatusInputAdminLomba = $('#hidden_status_verifikasi_lomba'); // Untuk tombol clicked
    const clickedButton = $('.btn-verify-action-lomba[data-status="'+hiddenStatusInputAdminLomba.val()+'"]');
    const originalButtonHTML = clickedButton.html();

    $.ajax({
        url: formVerifikasiAdminLomba.attr('action'),
        method: 'PUT', // <--- UBAH MENJADI 'PUT' SECARA LANGSUNG
        data: formData, // Kirim data form yang sudah diserialisasi.
                        // Laravel akan membaca _token dari sini.
        dataType: 'json',
        beforeSend: function() {
            actionButtonsAdminLomba.prop('disabled', true);
            clickedButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');
        },
        success: function(response) {
            if (response && response.status === true) {
                $('#modalVerifikasiLombaAdmin').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                });
                if (typeof dataTableVerifikasiLomba !== 'undefined' && dataTableVerifikasiLomba !== null) {
                    dataTableVerifikasiLomba.ajax.reload(null, false);
                } else {
                    console.warn('Variabel dataTableVerifikasiLomba tidak terdefinisi atau null.');
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: (response && response.message) ? response.message : 'Terjadi kesalahan saat memproses permintaan.'
                });
            }
        },
        error: function(xhr) {
            let errorMessage = 'Terjadi kesalahan server.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) { // Menampilkan pesan error dari server jika ada
                try {
                    // Mencoba parse responseText jika itu adalah JSON yang berisi pesan error Laravel
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    } else {
                         // Jika tidak bisa di-parse atau tidak ada .message, tampilkan responseText mentah
                        errorMessage = xhr.responseText;
                    }
                } catch (e) {
                    // Jika responseText bukan JSON valid, tampilkan apa adanya (mungkin HTML error page)
                    // Untuk kasus "MethodNotAllowedHttpException", pesan standarnya sudah cukup jelas.
                    if (xhr.status === 405) { // Method Not Allowed
                         errorMessage = "Metode yang digunakan tidak didukung untuk rute ini. Server mengharapkan PUT.";
                    } else {
                        errorMessage = `Error ${xhr.status}: ${xhr.statusText}. Silakan cek console untuk detail.`;
                    }
                    console.error("AJAX Error Details:", xhr);
                }
            }
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: errorMessage // Menggunakan html agar bisa menampilkan pesan error yang lebih panjang jika perlu
            });
        },
        complete: function() {
            actionButtonsAdminLomba.prop('disabled', false);
            clickedButton.html(originalButtonHTML);
        }
    });
}
});
</script>