<form id="formVerifikasiLombaAdmin" method="POST" action="{{ route('admin.lomba.verifikasi.proses', $lomba->lomba_id) }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="status_verifikasi" id="hidden_status_verifikasi_lomba" value="{{ $lomba->status_verifikasi }}">

    <div class="modal-header bg-light">
        <h5 class="modal-title" id="modalVerifikasiLombaLabel"><i class="fas fa-award me-2"></i>Verifikasi Pengajuan Lomba: {{ Str::limit($lomba->nama_lomba, 45) }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body p-lg-4" style="max-height: 68vh; overflow-y: auto;">
        <div class="row">
            {{-- Kolom Kiri: Detail Lomba. Style scroll dihilangkan dari sini. --}}
            <div class="col-12 col-lg-7 border-end-lg pe-lg-4">
                @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
                    <div class="mb-4 text-center">
                        <img src="{{ asset('storage/'.$lomba->poster) }}" alt="Poster Lomba" class="img-fluid rounded shadow-sm" style="max-height: 400px; object-fit: contain; border: 1px solid #ddd;">
                    </div>
                @endif

                {{-- Card Informasi Utama --}}
                <div class="card mb-3">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Utama</h6>
                    </div>
                    <div class="card-body py-2 px-3">
                        <dl class="row mb-0">
                            <dt class="col-sm-5"><i class="fas fa-medal fa-fw me-1 text-muted"></i>Nama Lomba</dt>
                            <dd class="col-sm-7">{{ $lomba->nama_lomba }}</dd>

                            <dt class="col-sm-5"><i class="fas fa-university fa-fw me-1 text-muted"></i>Penyelenggara</dt>
                            <dd class="col-sm-7">{{ $lomba->penyelenggara }}</dd>

                            <dt class="col-sm-5"><i class="fas fa-signal fa-fw me-1 text-muted"></i>Tingkat</dt>
                            <dd class="col-sm-7">{{ ucfirst($lomba->tingkat) }}</dd>

                            <dt class="col-sm-5"><i class="fas fa-users fa-fw me-1 text-muted"></i>Kategori</dt>
                            <dd class="col-sm-7">{{ ucfirst($lomba->kategori) }}</dd>

                            <dt class="col-sm-5"><i class="fas fa-tags fa-fw me-1 text-muted"></i>Bidang</dt>
                            <dd class="col-sm-7">
                                @forelse($lomba->bidangKeahlian as $detail)
                                    @if($detail->bidang)
                                        <span class="badge bg-light-secondary text-dark me-1 mb-1 p-2">{{ e($detail->bidang->bidang_nama) }}</span>
                                    @endif
                                @empty
                                    -
                                @endforelse
                            </dd>
                        </dl>
                    </div>
                </div>

                {{-- Card Jadwal & Biaya --}}
                <div class="card mb-3">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-calendar-alt me-2 text-primary"></i>Jadwal Pendaftaran & Biaya</h6>
                    </div>
                    <div class="card-body py-2 px-3">
                        <dl class="row mb-0">
                            <dt class="col-sm-5"><i class="fas fa-calendar-plus fa-fw me-1 text-muted"></i>Buka Daftar</dt>
                            <dd class="col-sm-7">{{ $lomba->pembukaan_pendaftaran ? $lomba->pembukaan_pendaftaran->isoFormat('D MMM YY') : '-' }}</dd>
                            
                            <dt class="col-sm-5"><i class="fas fa-calendar-times fa-fw me-1 text-muted"></i>Tutup Daftar</dt>
                            <dd class="col-sm-7">{{ $lomba->batas_pendaftaran ? $lomba->batas_pendaftaran->isoFormat('D MMM YY') : '-' }}</dd>
                            
                            <dt class="col-sm-5"><i class="fas fa-money-bill-wave fa-fw me-1 text-muted"></i>Biaya</dt>
                            <dd class="col-sm-7">
                                @if($lomba->biaya > 0)
                                    Rp {{ number_format($lomba->biaya, 0, ',', '.') }}
                                @else
                                    <span class="badge bg-light-success text-success px-2 py-1">Gratis</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                {{-- Card Deskripsi & Tautan --}}
                <div class="card mb-3">
                    <div class="card-header bg-white py-2">
                         <h6 class="mb-0 fw-semibold"><i class="fas fa-gifts me-2 text-primary"></i>Hadiah & Tautan</h6>
                    </div>
                    <div class="card-body py-2 px-3">
                        <h6 class="fw-semibold">Hadiah:</h6>
                        @if($lomba->daftarHadiah && $lomba->daftarHadiah->count() > 0)
                            <ul class="list-unstyled mb-3 ps-0">
                                @foreach($lomba->daftarHadiah as $itemHadiah)
                                    <li><i class="fas fa-gift text-warning me-2"></i>{{ e($itemHadiah->hadiah) }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">Informasi hadiah tidak tersedia.</p>
                        @endif

                        @if($lomba->link_pendaftaran || $lomba->link_penyelenggara)
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @if($lomba->link_pendaftaran)
                                <a href="{{ $lomba->link_pendaftaran }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt me-1"></i> Link Pendaftaran</a>
                            @endif
                            @if($lomba->link_penyelenggara)
                                <a href="{{ $lomba->link_penyelenggara }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary"><i class="fas fa-link me-1"></i> Website Resmi</a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            <div class="col-12 col-lg-5 ps-lg-4 mt-4 mt-lg-0">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-dark">Form Verifikasi</h6>
                        <hr class="my-2">
                        <dl class="row mb-2">
                             <dt class="col-12">Diajukan Oleh:</dt>
                             <dd class="col-12">{{ $lomba->inputBy->nama ?? 'N/A' }} ({{ ucfirst($lomba->inputBy->role ?? 'N/A') }})</dd>
                             <dt class="col-12">Tanggal Pengajuan:</dt>
                             <dd class="col-12 text-align-center">{{ $lomba->created_at ? $lomba->created_at->isoFormat('D MMMM YYYY') : '-' }}</dd>
                             <dt class="col-12">Status Saat Ini:</dt>
                             <dd class="col-12">{!! $lomba->status_verifikasi_badge !!}</dd>
                        </dl>
                        <hr class="my-2">
                        
                        <div class="form-group mb-3">
                            <label for="admin_catatan_verifikasi_lomba" class="form-label fw-semibold">Catatan Verifikasi:</label>
                            <textarea class="form-control" id="admin_catatan_verifikasi_lomba" name="catatan_verifikasi" rows="4" placeholder="Wajib diisi jika menolak pengajuan. Catatan ini akan terlihat oleh pengaju.">{{ old('catatan_verifikasi', $lomba->catatan_verifikasi ?? '') }}</textarea>
                            <span class="invalid-feedback error-catatan_verifikasi"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label fw-semibold">Aksi:</label>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-success btn-verify-action-lomba" data-status="disetujui" title="Setujui pengajuan lomba ini">
                                    <i class="fas fa-check-circle me-1"></i> Setujui Pengajuan
                                </button>
                                <button type="button" class="btn btn-danger btn-verify-action-lomba" data-status="ditolak" title="Tolak pengajuan lomba ini">
                                    <i class="fas fa-times-circle me-1"></i> Tolak Pengajuan
                                </button>
                                @if($lomba->status_verifikasi !== 'pending')
                                <button type="button" class="btn btn-warning btn-sm btn-verify-action-lomba" data-status="pending" title="Kembalikan status ke Menunggu (Pending)">
                                    <i class="fas fa-history me-1"></i> Kembalikan ke Pending
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
    </div>
</form>

{{-- Script tidak perlu diubah karena selector ID dan class masih sama. --}}
<script>
$(document).ready(function() {
    // ... (Seluruh kode JavaScript yang sudah ada sebelumnya tetap sama dan akan berfungsi)
    const formVerifikasiAdminLomba = $('#formVerifikasiLombaAdmin');
    const hiddenStatusInputAdminLomba = $('#hidden_status_verifikasi_lomba');
    const catatanTextareaAdminLomba = $('#admin_catatan_verifikasi_lomba');

    if ($.fn.validate) {
        formVerifikasiAdminLomba.validate({
            rules: {
                catatan_verifikasi: {
                    maxlength: 1000,
                    required: function() {
                        return hiddenStatusInputAdminLomba.val() === 'ditolak';
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
                element.closest('.form-group').find('span.error-catatan_verifikasi').html(error.html()).show();
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
                $(element).closest('.form-group').find('span.error-catatan_verifikasi').empty().hide();
            }
        });
    }

    $('.btn-verify-action-lomba').on('click', function() {
        const status = $(this).data('status');
        hiddenStatusInputAdminLomba.val(status); 

        catatanTextareaAdminLomba.removeClass('is-invalid is-valid');
        catatanTextareaAdminLomba.closest('.form-group').find('.error-catatan_verifikasi').empty().hide();

        if (formVerifikasiAdminLomba.valid()) {
            submitVerificationFormAdminLomba();
        } else {
            if (status === 'ditolak' && catatanTextareaAdminLomba.val().trim() === '') {
                catatanTextareaAdminLomba.focus();
            }
        }
    });

    function submitVerificationFormAdminLomba() {
        const formData = formVerifikasiAdminLomba.serialize(); 
        const actionButtonsAdminLomba = $('.btn-verify-action-lomba');
        const clickedButton = $('.btn-verify-action-lomba[data-status="'+hiddenStatusInputAdminLomba.val()+'"]');
        const originalButtonHTML = clickedButton.html();

        $.ajax({
            url: formVerifikasiAdminLomba.attr('action'),
            method: 'PUT', 
            data: formData, 
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
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: (response && response.message) ? response.message : 'Terjadi kesalahan.'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan server.';
                if (xhr.responseJSON) {
                    errorMessage = xhr.responseJSON.message || 'Error tidak diketahui.';
                     if (xhr.responseJSON.errors && xhr.responseJSON.errors.catatan_verifikasi) {
                         const errorSpan = $('#admin_catatan_verifikasi_lomba').closest('.form-group').find('.error-catatan_verifikasi');
                         errorSpan.text(xhr.responseJSON.errors.catatan_verifikasi[0]).show();
                         $('#admin_catatan_verifikasi_lomba').addClass('is-invalid');
                         errorMessage = "Terdapat kesalahan validasi, mohon periksa form.";
                     }
                }
                Swal.fire({ icon: 'error', title: 'Oops...', html: errorMessage });
            },
            complete: function() {
                actionButtonsAdminLomba.prop('disabled', false);
                clickedButton.html(originalButtonHTML);
            }
        });
    }
});
</script>