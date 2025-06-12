{{-- File: resources/views/prestasi/dosen/show_ajax.blade.php --}}

<div class="modal-header">
    <h5 class="modal-title" id="modalDetailPrestasiLabel">Detail Prestasi</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-5">
            <p class="text-center"><strong>Bukti Prestasi</strong></p>
            @if ($prestasi->bukti_prestasi && Storage::disk('public')->exists($prestasi->bukti_prestasi))
                <a href="{{ asset('storage/' . $prestasi->bukti_prestasi) }}" target="_blank">
                    <img src="{{ asset('storage/' . $prestasi->bukti_prestasi) }}" alt="Bukti Prestasi" class="img-fluid rounded shadow-sm">
                </a>
                <small class="d-block text-center mt-2">Klik gambar untuk memperbesar</small>
            @else
                <div class="text-center py-5 bg-light rounded">
                    <i class="fas fa-image fa-3x text-gray-400"></i>
                    <p class="mt-2 text-muted">Tidak ada bukti</p>
                </div>
            @endif
        </div>
        <div class="col-md-7">
            <h4 class="font-weight-bold text-primary">{{ $prestasi->nama_prestasi }}</h4>
            <hr>
            <table class="table table-sm table-borderless">
                <tr>
                    <td style="width: 150px;"><strong>Diraih oleh</strong></td>
                    <td>: {{ $prestasi->mahasiswa->user->nama ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>NIM</strong></td>
                    <td>: {{ $prestasi->mahasiswa->nim ?? 'N/A' }}</td>
                </tr>
                 <tr>
                    <td><strong>Program Studi</strong></td>
                    <td>: {{ $prestasi->mahasiswa->prodi->nama ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Pembimbing</strong></td>
                    <td>: {{ $prestasi->dosen->user->nama ?? 'Tidak ada' }}</td>
                </tr>
                <tr>
                    <td><strong>Tingkat</strong></td>
                    <td>: {{ ucfirst($prestasi->tingkat) }}</td>
                </tr>
                <tr>
                    <td><strong>Penyelenggara</strong></td>
                    <td>: {{ $prestasi->penyelenggara }}</td>
                </tr>
                <tr>
                    <td><strong>Tahun</strong></td>
                    <td>: {{ $prestasi->tahun }}</td>
                </tr>
                <tr>
                    <td><strong>Status Verifikasi</strong></td>
                    <td>: {!! $prestasi->status_verifikasi_badge !!}</td>
                </tr>
                @if($prestasi->catatan_verifikasi)
                <tr>
                    <td><strong>Catatan Admin</strong></td>
                    <td>: <span class="text-danger">{{ $prestasi->catatan_verifikasi }}</span></td>
                </tr>
                @endif
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>