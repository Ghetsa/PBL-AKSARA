{{-- resources/views/prestasi/dosen/verify_ajax.blade.php --}}
<form id="formVerifikasiPrestasi" method="POST"
    action="{{ route('prestasi.dosen.verify_process_ajax', $prestasi->prestasi_id) }}">
    @csrf
    @method('PUT')

    {{-- Hidden input status --}}
    <input type="hidden" name="status_verifikasi" id="hidden_status_verifikasi" value="">

    <div class="modal-header bg-light">
        <h5 class="modal-title"><i class="fas fa-trophy me-2"></i>Verifikasi Prestasi Mahasiswa:
            {{ Str::limit($prestasi->nama_prestasi, 45) }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body p-lg-4" style="max-height: 68vh; overflow-y: auto;">
        <div class="row">
            {{-- Kolom Kiri: Detail Pengajuan --}}
            <div class="col-12 col-lg-7 border-end-lg pe-lg-4">
                <div class="card mb-3">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-user-graduate me-2 text-primary"></i>Informasi
                            Mahasiswa</h6>
                    </div>
                    <div class="card-body py-2 px-3">
                        <dl class="row mb-0">
                            <dt class="col-sm-5 text-muted"><i class="fas fa-user fa-fw me-2"></i>Nama</dt>
                            <dd class="col-sm-7">{{ $prestasi->mahasiswa->user->nama ?? 'N/A' }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-id-card fa-fw me-2"></i>NIM</dt>
                            <dd class="col-sm-7">{{ $prestasi->mahasiswa->nim ?? 'N/A' }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-graduation-cap fa-fw me-2"></i>Prodi</dt>
                            <dd class="col-sm-7">{{ $prestasi->mahasiswa->prodi->nama ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>

                {{-- Card Detail Prestasi --}}
                <div class="card mb-3">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-award me-2 text-primary"></i>Detail Prestasi</h6>
                    </div>
                    <div class="card-body py-2 px-3">
                        <dl class="row mb-0">
                            <dt class="col-sm-5 text-muted"><i class="fas fa-medal fa-fw me-2"></i>Nama Prestasi</dt>
                            <dd class="col-sm-7">{{ ucfirst($prestasi->nama_prestasi) }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-swatchbook fa-fw me-2"></i>Kategori</dt>
                            <dd class="col-sm-7">{{ ucfirst($prestasi->kategori) }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-tags fa-fw me-2"></i>Bidang</dt>
                            <dd class="col-sm-7">{{ ucfirst($prestasi->bidang->bidang_nama) }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-signal fa-fw me-2"></i>Tingkat</dt>
                            <dd class="col-sm-7">{{ ucfirst($prestasi->tingkat) }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-calendar-alt fa-fw me-2"></i>Tahun</dt>
                            <dd class="col-sm-7">{{ $prestasi->tahun }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-university fa-fw me-2"></i>Penyelenggara
                            </dt>
                            <dd class="col-sm-7">{{ $prestasi->penyelenggara }}</dd>
                            <dt class="col-sm-5 text-muted"><i class="fas fa-chalkboard-teacher fa-fw me-2"></i>Dosen
                                Pembina</dt>
                            <dd class="col-sm-7">{{ $prestasi->dosen->user->nama ?? 'Tidak ada' }}</dd>
                        </dl>
                    </div>
                </div>

                {{-- Card Bukti Prestasi --}}
                <div class="card mb-3">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-file-alt me-2 text-primary"></i>Bukti Prestasi
                        </h6>
                    </div>
                    <div class="card-body text-center p-3">

                        @if ($prestasi->file_bukti && Storage::disk('public')->exists($prestasi->file_bukti))
                            @php
                                $filePath = $prestasi->file_bukti;
                                $fileUrl = asset('storage/' . $filePath);
                                $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                            @endphp

                            @if (in_array($fileExtension, $imageExtensions))
                                <a href="{{ $fileUrl }}" target="_blank" title="Klik untuk melihat gambar penuh">
                                    <img src="{{ $fileUrl }}" alt="Bukti Prestasi"
                                        class="img-fluid rounded border p-1 w-100"
                                        style="max-height: 450px; object-fit: contain;">
                                </a>
                            @else
                                <a href="{{ $fileUrl }}" target="_blank" class="btn btn-primary w-100">
                                    <i class="fas fa-file-pdf me-2"></i>Lihat / Unduh Bukti
                                    ({{ strtoupper($fileExtension) }})
                                </a>
                            @endif
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-1"></i> File bukti tidak ditemukan atau belum
                                diunggah.
                            </div>
                        @endif
                        {{-- 
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-1"></i> File sertifikat tidak ditemukan atau belum diunggah.
                            </div>
                        @endif --}}
                    </div>
                </div>

            </div>

            {{-- Kolom Kanan: Panel Verifikasi --}}
            <div class="col-12 col-lg-5 ps-lg-4 mt-4 mt-lg-0">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-dark">Form Verifikasi</h6>
                        <hr class="my-2">
                        <div class="mb-3">
                            <span class="fw-semibold">Status Saat Ini:</span>
                            {!! $prestasi->status_verifikasi_badge !!}
                        </div>

                        <div class="form-group mb-3">
                            <label for="catatan_verifikasi" class="form-label fw-semibold">Catatan Verifikasi:</label>
                            <textarea class="form-control" id="catatan_verifikasi" name="catatan_verifikasi" rows="4"
                                placeholder="Wajib diisi jika menolak pengajuan.">{{ old('catatan_verifikasi', $prestasi->catatan_verifikasi ?? '') }}</textarea>
                            <span id="error-catatan_verifikasi" class="invalid-feedback"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label fw-semibold">Aksi:</label>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-success btn-verify-prestasi-action"
                                    data-status="disetujui">
                                    <i class="fas fa-check-circle me-2"></i>Setujui
                                </button>
                                <button type="button" class="btn btn-danger btn-verify-prestasi-action"
                                    data-status="ditolak">
                                    <i class="fas fa-times-circle me-2"></i>Tolak
                                </button>
                                @if ($prestasi->status_verifikasi != 'pending')
                                    <button type="button" class="btn btn-warning btn-sm btn-verify-prestasi-action"
                                        data-status="pending">
                                        <i class="fas fa-history me-2"></i>Kembalikan ke Pending
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

<script>
    $(document).ready(function() {
        const formVerifikasi = $('#formVerifikasiPrestasi');
        const hiddenStatusInput = $('#hidden_status_verifikasi');
        const catatanTextarea = $('#verify_catatan_verifikasi');

        // Validasi form
        formVerifikasi.validate({
            rules: {
                catatan_verifikasi: {
                    maxlength: 1000,
                    required: function() {
                        return hiddenStatusInput.val() === 'ditolak';
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
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                if (element.is('textarea')) {
                    error.insertAfter(element.next('small').length ? element.next('small') :
                        element);
                } else {
                    element.closest('.col-sm-9').append(error);
                }
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });

        // Handle klik tombol verifikasi
       $('.btn-verify-prestasi-action').on('click', function() {
            const status = $(this).data('status');
            hiddenStatusInput.val(status);

            if (formVerifikasi.valid()) {
                submitVerificationForm();
            } else if (status === 'ditolak' && catatanTextarea.val().trim() === '') {
                catatanTextarea.focus();
            }
        });

        function submitVerificationForm() {
            const formData = formVerifikasi.serialize();
            const actionButtons = $('.btn-verify-action');

            $.ajax({
                url: formVerifikasi.attr('action'),
                method: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    actionButtons.prop('disabled', true);
                    $('.error-text').text('');
                    $('.form-control').removeClass('is-invalid');
                },
                success: function(response) {
                    if (response.status) {
                        const modalId = formVerifikasi.closest('.modal').attr('id');
                        const modalInstance = bootstrap.Modal.getInstance(document.getElementById(
                            modalId));
                        if (modalInstance) modalInstance.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });

                        if (typeof dataDaftarPrestasiDosen !== 'undefined') {
                            dataDaftarPrestasiDosen.ajax.reload();
                        } else {
                            console.warn('DataTable belum terdefinisi.');
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    let msg = 'Terjadi kesalahan.';

                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        $.each(xhr.responseJSON.errors, function(key, val) {
                            $('#error-' + key).text(val[0]).show();
                            $('[name="' + key + '"]').addClass('is-invalid');
                        });
                        msg = xhr.responseJSON.message || 'Validasi gagal.';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: msg
                    });
                },
                complete: function() {
                    actionButtons.prop('disabled', false);
                }
            });
        }
    });
</script>
