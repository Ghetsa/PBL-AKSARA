@php
    // Logika untuk menentukan warna aksen kartu berdasarkan status
    $statusClass = '';
    switch ($keahlianUser->status_verifikasi) {
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
    <h5 class="modal-title"><i class="fas fa-star me-2"></i>Detail Pengajuan Keahlian</h5>
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
                        {!! $keahlianUser->status_verifikasi_badge !!}
                    </div>
                    {{-- Tombol Aksi Kontekstual hanya muncul jika status 'pending' atau 'ditolak' --}}
                    @if(in_array($keahlianUser->status_verifikasi, ['pending', 'ditolak']))
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="openModalFromModal('{{ route('keahlian_user.edit', $keahlianUser->keahlian_user_id) }}', 'Edit Keahlian')"
                                    title="Edit Pengajuan">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                             <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $keahlianUser->keahlian_user_id }}" title="Hapus Pengajuan">
                                <i class="fas fa-trash-alt me-1"></i> Hapus
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Menampilkan catatan verifikasi jika status ditolak --}}
                @if($keahlianUser->status_verifikasi == 'ditolak' && $keahlianUser->catatan_verifikasi)
                    <hr>
                    <div class="alert alert-danger mb-0 mt-2 py-2 px-3">
                        <strong class="d-block"><i class="fas fa-exclamation-triangle me-1"></i> Catatan dari Verifikator:</strong>
                        <p class="mb-0 fst-italic">{{ $keahlianUser->catatan_verifikasi }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- KARTU DETAIL SERTIFIKASI --}}
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-certificate me-2"></i>Detail Sertifikasi/Keahlian</h6>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-5 text-muted">Bidang Keahlian</dt>
                    <dd class="col-sm-7">{{ $keahlianUser->bidang->bidang_nama ?? '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Nama Sertifikat</dt>
                    <dd class="col-sm-7">{{ $keahlianUser->nama_sertifikat ?? '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Lembaga</dt>
                    <dd class="col-sm-7">{{ $keahlianUser->lembaga_sertifikasi ?? '-' }}</dd>
                    
                    <dt class="col-sm-5 text-muted">Tanggal Perolehan</dt>
                    <dd class="col-sm-7">{{ $keahlianUser->tanggal_perolehan_sertifikat ? \Carbon\Carbon::parse($keahlianUser->tanggal_perolehan_sertifikat)->isoFormat('D MMMM YYYY') : '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Tanggal Kedaluwarsa</dt>
                    <dd class="col-sm-7">{{ $keahlianUser->tanggal_kadaluarsa_sertifikat ? \Carbon\Carbon::parse($keahlianUser->tanggal_kadaluarsa_sertifikat)->isoFormat('D MMMM YYYY') : 'Tidak Ada' }}</dd>
                </dl>
            </div>
        </div>

        {{-- KARTU BUKTI (DENGAN PREVIEW GAMBAR) --}}
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-file-contract me-2"></i>Bukti Sertifikasi</h6>
            </div>
            <div class="card-body p-3 text-center">
                @if($keahlianUser->sertifikasi && Storage::disk('public')->exists($keahlianUser->sertifikasi))
                    @php
                        $filePath = $keahlianUser->sertifikasi;
                        $fileUrl = asset('storage/' . $filePath);
                        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                    @endphp

                    @if(in_array($fileExtension, $imageExtensions))
                        <a href="{{ $fileUrl }}" target="_blank" title="Klik untuk melihat gambar penuh">
                            <img src="{{ $fileUrl }}" alt="Bukti Keahlian" class="img-fluid rounded border p-1" style="max-height: 400px; object-fit: contain;">
                        </a>
                    @else
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

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
</div>

{{-- <div class="modal-header">
  <h5 class="modal-title">Detail Keahlian: {{ $keahlianUser->bidang->bidang_nama ?? '-' }}</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
  <div class="table-responsive">
    <table class="table table-sm table-bordered table-striped">
      <tr>
        <th style="width: 35%;">Bidang Keahlian</th>
        <td>{{ $keahlianUser->bidang->bidang_nama ?? '-' }}</td>
      </tr>
      <tr>
        <th>Nama Sertifikat/Keahlian</th>
        <td>{{ $keahlianUser->nama_sertifikat ?? '-' }}</td>
      </tr>
      <tr>
        <th>Lembaga Sertifikasi</th>
        <td>{{ $keahlianUser->lembaga_sertifikasi ?? '-' }}</td>
      </tr>
      <tr>
        <th>Tanggal Perolehan</th>
        <td>{{ $keahlianUser->tanggal_perolehan_sertifikat ? $keahlianUser->tanggal_perolehan_sertifikat->format('d F Y') : '-' }}</td>
      </tr>
      <tr>
        <th>Tanggal Kadaluarsa</th>
        <td>{{ $keahlianUser->tanggal_kadaluarsa_sertifikat ? $keahlianUser->tanggal_kadaluarsa_sertifikat->format('d F Y') : '-' }}</td>
      </tr>
      <tr>
        <th>File Bukti/Sertifikat</th>
        <td>
          @if($keahlianUser->sertifikasi && Storage::disk('public')->exists($keahlianUser->sertifikasi))
            <a href="{{ asset('storage/' . $keahlianUser->sertifikasi) }}" target="_blank" class="btn btn-sm btn-outline-info">
              <i class="fas fa-eye"></i> Lihat File
            </a>
          @else
            <span class="text-muted">Tidak ada file.</span>
          @endif
        </td>
      </tr>
      <tr>
        <th>Status Verifikasi</th>
        <td>
          {!! $keahlianUser->status_verifikasi_badge !!} 
        </td>
      </tr>
      @if($keahlianUser->catatan_verifikasi)
      <tr>
        <th>Catatan Verifikasi</th>
        <td class="{{ $keahlianUser->status_verifikasi == 'ditolak' ? 'text-danger fst-italic' : '' }}">
            {{ $keahlianUser->catatan_verifikasi }}
        </td>
      </tr>
      @endif
    </table>
  </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
  @if(in_array($keahlianUser->status_verifikasi, ['pending', 'ditolak']) && $keahlianUser->user_id == auth()->id())
    <button type="button" class="btn btn-warning"
            onclick="modalAction('{{ route('keahlian_user.edit', $keahlianUser->keahlian_user_id) }}', 'Edit Keahlian')">
      <i class="fas fa-edit"></i> Edit Keahlian
    </button>
  @endif
</div> --}}