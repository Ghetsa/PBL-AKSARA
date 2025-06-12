{{-- resources/views/bimbingan/verify_ajax.blade.php --}}
<form id="formVerifikasiBimbingan" method="POST" action="{{ route('bimbingan.verify_process', $prestasi->prestasi_id) }}">
    @csrf
    @method('PUT')

    {{-- Hidden input status --}}
    <input type="hidden" name="status_verifikasi" id="hidden_status_verifikasi" value="">

    <div class="modal-header">
        <h5 class="modal-title">Prestasi Mahasiswa: {{ Str::limit($prestasi->nama_prestasi, 50) }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body p-lg-4" style="max-height: 68vh; overflow-y: auto;">
        <div class="row g-3">

            {{-- Informasi Mahasiswa --}}
            <div class="col-12">
                <div class="card w-100 h-100">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-user-graduate me-2 text-primary"></i>Informasi Mahasiswa
                        </h6>
                    </div>
                    <div class="card-body py-2 px-3">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted"><i class="fas fa-user fa-fw me-2"></i>Nama</dt>
                            <dd class="col-sm-8">{{ $prestasi->mahasiswa->user->nama ?? 'N/A' }}</dd>
                            <dt class="col-sm-4 text-muted"><i class="fas fa-id-card fa-fw me-2"></i>NIM</dt>
                            <dd class="col-sm-8">{{ $prestasi->mahasiswa->nim ?? 'N/A' }}</dd>
                            <dt class="col-sm-4 text-muted"><i class="fas fa-graduation-cap fa-fw me-2"></i>Prodi</dt>
                            <dd class="col-sm-8">{{ $prestasi->mahasiswa->prodi->nama ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Detail Prestasi --}}
            <div class="col-12">
                <div class="card w-100 h-100">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-award me-2 text-primary"></i>Detail Prestasi
                        </h6>
                    </div>
                    <div class="card-body py-2 px-3">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted"><i class="fas fa-medal fa-fw me-2"></i>Nama Prestasi</dt>
                            <dd class="col-sm-8">{{ ucfirst($prestasi->nama_prestasi) }}</dd>
                            <dt class="col-sm-4 text-muted"><i class="fas fa-swatchbook fa-fw me-2"></i>Kategori</dt>
                            <dd class="col-sm-8">{{ ucfirst($prestasi->kategori) }}</dd>
                            <dt class="col-sm-4 text-muted"><i class="fas fa-tags fa-fw me-2"></i>Bidang</dt>
                            <dd class="col-sm-8">{{ ucfirst($prestasi->bidang->bidang_nama) }}</dd>
                            <dt class="col-sm-4 text-muted"><i class="fas fa-signal fa-fw me-2"></i>Tingkat</dt>
                            <dd class="col-sm-8">{{ ucfirst($prestasi->tingkat) }}</dd>
                            <dt class="col-sm-4 text-muted"><i class="fas fa-calendar-alt fa-fw me-2"></i>Tahun</dt>
                            <dd class="col-sm-8">{{ $prestasi->tahun }}</dd>
                            <dt class="col-sm-4 text-muted"><i class="fas fa-university fa-fw me-2"></i>Penyelenggara
                            </dt>
                            <dd class="col-sm-8">{{ $prestasi->penyelenggara }}</dd>
                            <dt class="col-sm-4 text-muted"><i class="fas fa-chalkboard-teacher fa-fw me-2"></i>Dosen
                                Pembina</dt>
                            <dd class="col-sm-8">{{ $prestasi->dosen->user->nama ?? 'Tidak ada' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Bukti Prestasi --}}
            <div class="col-12">
                <div class="card w-100 h-100">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-file-alt me-2 text-primary"></i>Bukti Prestasi
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
                                        class="img-fluid rounded border p-1"
                                        style="width: 100%; max-height: 450px; object-fit: contain;">
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
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
    </div>
    </div>
    </div>
</form>


<script>
    $(document).ready(function() {
        const form = $('#formVerifikasiBimbingan');
        const hiddenStatus = $('#hidden_status_verifikasi');
        const catatan = $('#verify_catatan_verifikasi');

        form.validate({
            rules: {
                catatan_verifikasi: {
                    maxlength: 1000,
                    required: function() {
                        return hiddenStatus.val() === 'ditolak';
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

        $('.btn-verify-action').on('click', function() {
            const status = $(this).data('status');
            hiddenStatus.val(status);

            if (form.valid()) {
                kirimForm();
            } else if (status === 'ditolak' && catatan.val().trim() === '') {
                catatan.focus();
            }
        });

        function kirimForm() {
            const formData = form.serialize();
            const buttons = $('.btn-verify-action');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    buttons.prop('disabled', true);
                    $('.error-text').text('');
                    $('.form-control').removeClass('is-invalid');
                },
                success: function(res) {
                    if (res.status) {
                        const modalId = form.closest('.modal').attr('id');
                        const modalInstance = bootstrap.Modal.getInstance(document.getElementById(
                            modalId));
                        if (modalInstance) modalInstance.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message
                        });

                        if (typeof dataDaftarMahasiswaBimbingan !== 'undefined') {
                            dataDaftarMahasiswaBimbingan.ajax.reload();
                        } else {
                            console.warn('DataTable belum terdefinisi.');
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message
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
                    buttons.prop('disabled', false);
                }
            });
        }
    });
</script>
