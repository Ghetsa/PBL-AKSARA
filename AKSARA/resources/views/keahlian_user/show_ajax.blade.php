<div class="modal-header">
  <h5 class="modal-title">Detail Keahlian: {{ $keahlianUser->keahlian->keahlian_nama ?? '-' }}</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
  <div class="table-responsive">
    <table class="table table-sm table-bordered">
      {{-- <tr>
        <th style="width: 30%;">Nama Pengguna</th>
        <td>{{ $keahlianUser->user->nama ?? '-' }}</td>
      </tr> --}}
      <tr>
        <th>Keahlian</th>
        <td>{{ $keahlianUser->keahlian->keahlian_nama ?? '-' }}</td>
      </tr>
      <tr>
        <th>Status Verifikasi</th>
        <td>
          @if($keahlianUser->status_verifikasi == 'disetujui')
        <span class="badge bg-success">Terverifikasi</span>
      @elseif($keahlianUser->status_verifikasi == 'ditolak')
        <span class="badge bg-danger">Ditolak</span>
      @else
        <span class="badge bg-warning text-dark">Menunggu</span>
      @endif
        </td>
      </tr>
      @if($keahlianUser->status_verifikasi == 'ditolak' && !empty($keahlianUser->catatan_verifikasi))
      <tr>
      <th>Catatan Verifikasi</th>
      <td class="text-danger fst-italic">{{ $keahlianUser->catatan_verifikasi }}</td>
      </tr>
    @endif
      <tr>
        <th>Sertifikasi</th>
        <td>
          @if($keahlianUser->sertifikasi)
        <a href="{{ asset('storage/' . $keahlianUser->sertifikasi) }}" target="_blank" class="btn btn-sm btn-info">
        <i class="fas fa-eye"></i> Lihat Sertifikasi
        </a>
      @else
        Tidak ada file sertifikasi.
      @endif
        </td>
      </tr>
    </table>
  </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
  @if($keahlianUser->status_verifikasi == 'ditolak')
    <button type="button" class="btn btn-primary"
    onclick="modalAction('{{ route('keahlian_user.edit', $keahlianUser->keahlian_user_id) }}', 'Edit Keahlian')">
    <i class="fas fa-edit"></i> Edit Keahlian
    </button>
  @endif
</div>