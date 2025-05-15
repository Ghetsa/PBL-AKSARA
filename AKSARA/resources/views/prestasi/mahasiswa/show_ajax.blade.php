{{-- resources/views/mahasiswa/prestasi/show_ajax.blade.php --}}
<div class="modal-header">
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
        {{-- Tombol Edit akan memanggil modalAction lagi dengan route edit --}}
        <button type="button" class="btn btn-primary"
                onclick="modalAction('{{ route('prestasi.mahasiswa.edit_ajax', $prestasi->prestasi_id) }}', 'Edit Prestasi')">
            <i class="fas fa-edit"></i> Edit Prestasi
        </button>
    @endif
</div>