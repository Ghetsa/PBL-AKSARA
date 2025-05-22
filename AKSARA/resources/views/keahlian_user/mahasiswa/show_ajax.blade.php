<div class="modal-header">
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
          {!! $keahlianUser->status_verifikasi_badge !!} {{-- Menggunakan accessor untuk badge --}}
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
</div>