@php
    // Logika untuk menentukan warna aksen berdasarkan status
    $statusClass = '';
    switch ($prestasi->status_verifikasi) {
        case 'disetujui':
            $statusClass = 'border-success';
            break;
        case 'ditolak':
            $statusClass = 'border-danger';
            break;
        case 'pending':
        default:
            $statusClass = 'border-warning';
            break;
    }
@endphp

<div class="modal-header">
    <h5 class="modal-title"><i class="fas fa-trophy me-2"></i>Detail Pengajuan Prestasi</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body bg-light" style="max-height: 68vh; overflow-y: auto;">
    <div class="container-fluid">

        {{-- KARTU STATUS PENGAJUAN (PALING PENTING) --}}
        <div class="card mb-4 shadow-sm {{ $statusClass }}" style="border-width: 0; border-left-width: 4px;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <h6 class="card-title fw-bold">Status Pengajuan</h6>
                        {!! $prestasi->status_verifikasi_badge !!}
                    </div>
                    {{-- Tombol Aksi Kontekstual --}}
                    @if(in_array($prestasi->status_verifikasi, ['pending', 'ditolak']))
                        <div>
                            {{-- Tombol Edit akan memanggil modalAction lagi dengan route edit --}}
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="openModalFromModal('{{ route('prestasi.mahasiswa.edit_ajax', $prestasi->prestasi_id) }}', 'Edit Prestasi')"
                                    title="Edit Pengajuan">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                        </div>
                    @endif
                </div>

                @if($prestasi->status_verifikasi == 'ditolak' && $prestasi->catatan_verifikasi)
                    <hr>
                    <div class="alert alert-danger mb-0 mt-2 py-2 px-3">
                        <strong class="d-block"><i class="fas fa-exclamation-triangle me-1"></i> Catatan dari Verifikator:</strong>
                        <p class="mb-0 fst-italic">{{ $prestasi->catatan_verifikasi }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- KARTU BUKTI PRESTASI (DENGAN PREVIEW GAMBAR) --}}
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-file-contract me-2"></i>Bukti Prestasi</h6>
            </div>
            <div class="card-body p-3 text-center">
                @if($prestasi->file_bukti && Storage::disk('public')->exists($prestasi->file_bukti))
                    @php
                        $filePath = $prestasi->file_bukti;
                        $fileUrl = asset('storage/' . $filePath);
                        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                    @endphp

                    @if(in_array($fileExtension, $imageExtensions))
                        {{-- Jika file adalah gambar, tampilkan preview --}}
                        <a href="{{ $fileUrl }}" target="_blank" title="Klik untuk melihat gambar penuh">
                            <img src="{{ $fileUrl }}" alt="Bukti Prestasi" class="img-fluid rounded border p-1" style="max-height: 400px; object-fit: contain;">
                        </a>
                    @else
                        {{-- Jika bukan gambar (PDF, dll), tampilkan tombol --}}
                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-file-alt me-2"></i>Lihat / Unduh Bukti ({{ strtoupper($fileExtension) }})
                        </a>
                    @endif
                @else
                    <div class="alert alert-secondary mb-0">
                        Tidak ada bukti terunggah.
                    </div>
                @endif
            </div>
        </div>

        {{-- KARTU DETAIL PRESTASI --}}
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-trophy me-2"></i>Detail Prestasi</h6>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-5 text-muted">Nama Prestasi</dt>
                    <dd class="col-sm-7">{{ $prestasi->nama_prestasi }}</dd>

                    <dt class="col-sm-5 text-muted">Kategori</dt>
                    <dd class="col-sm-7">{{ ucfirst($prestasi->kategori) }}</dd>

                    <dt class="col-sm-5 text-muted">Bidang</dt>
                    <dd class="col-sm-7">{{ ucfirst($prestasi->bidang->bidang_nama) }}</dd>

                    <dt class="col-sm-5 text-muted">Penyelenggara</dt>
                    <dd class="col-sm-7">{{ $prestasi->penyelenggara }}</dd>

                    <dt class="col-sm-5 text-muted">Tingkat</dt>
                    <dd class="col-sm-7">{{ ucfirst($prestasi->tingkat) }}</dd>

                    <dt class="col-sm-5 text-muted">Tahun</dt>
                    <dd class="col-sm-7">{{ $prestasi->tahun }}</dd>

                    <dt class="col-sm-5 text-muted">Dosen Pembina</dt>
                    <dd class="col-sm-7">{{ $prestasi->dosenPembimbing->user->nama ?? ($prestasi->dosen_pembimbing ?? '-') }}</dd>
                </dl>
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
</div>
{{-- <div class="modal-header">
    <h5 class="modal-title">Detail Prestasi: {{ $prestasi->nama_prestasi }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <tr>
                <th style="width: 30%;">Nama Prestasi/Kegiatan</th>
                <td>{{ $prestasi->nama_prestasi }}</td>
            </tr>
            <tr>
                <th>Kategori</th>
                <td>{{ ucfirst($prestasi->kategori) }}</td>
            </tr>
            <tr>
                <th>Penyelenggara</th>
                <td>{{ $prestasi->penyelenggara }}</td>
            </tr>
            <tr>
                <th>Tingkat</th>
                <td>{{ ucfirst($prestasi->tingkat) }}</td>
            </tr>
            <tr>
                <th>Tahun</th>
                <td>{{ $prestasi->tahun }}</td>
            </tr>
            <tr>
                <th>Dosen Pembimbing</th>
                <td>{{ $prestasi->dosenPembimbing->user->nama ?? ($prestasi->dosenPembimbing->nama ?? '-') }}</td>
            </tr>
            <tr>
                <th>Status Verifikasi</th>
                <td>
                    @if($prestasi->status_verifikasi == 'disetujui')
                        <span class="badge bg-success">Disetujui</span>
                    @elseif($prestasi->status_verifikasi == 'ditolak')
                        <span class="badge bg-danger">Ditolak</span>
                    @else
                        <span class="badge bg-warning">Pending</span>
                    @endif
                </td>
            </tr>
            @if($prestasi->status_verifikasi == 'ditolak' && !empty($prestasi->catatan_verifikasi))
            <tr>
                <th>Catatan Verifikasi</th>
                <td class="text-danger">
                    <p class="mb-0 fst-italic">{{ $prestasi->catatan_verifikasi }}</p>
                </td>
            </tr>
            @endif
            <tr>
                <th>Bukti</th>
                <td>
                    @if($prestasi->file_bukti)
                        <a href="{{ asset(Storage::url($prestasi->file_bukti)) }}" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Lihat Bukti
                        </a>
                    @else
                        Tidak ada bukti terunggah.
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
    @if($prestasi->status_verifikasi == 'ditolak')
        <button type="button" class="btn btn-primary"
                onclick="modalAction('{{ route('prestasi.mahasiswa.edit_ajax', $prestasi->prestasi_id) }}', 'Edit Prestasi')">
            <i class="fas fa-edit"></i> Edit Prestasi
        </button>
    @endif
</div> --}}