{{-- resources/views/mahasiswa/lomba/partials/detail_lomba_modal.blade.php --}}
<div class="modal-header">
    <h5 class="modal-title" id="modalDetailLombaLabel">Detail Lomba: {{ $lomba->nama_lomba }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <img src="{{ asset('storage/' . $lomba->poster) }}" class="img-fluid mb-3" alt="Poster Lomba" style="max-height: 300px; object-fit: contain;">
    <p><strong>Penyelenggara:</strong> {{ $lomba->penyelenggara }}</p>
    <p><strong>Tingkat:</strong> {{ ucfirst($lomba->tingkat) }}</p>
    <p><strong>Kategori:</strong> {{ ucfirst($lomba->kategori) }}</p>
    <p><strong>Deskripsi:</strong> {{ $lomba->deskripsi_lomba }}</p>
    <p><strong>Batas Pendaftaran:</strong> {{ $lomba->batas_pendaftaran ? $lomba->batas_pendaftaran->isoFormat('D MMM YYYY') : 'N/A' }}</p>
    @if ($lomba->link_pendaftaran)
        <p><strong>Link Pendaftaran:</strong> <a href="{{ $lomba->link_pendaftaran }}" target="_blank">{{ $lomba->link_pendaftaran }}</a></p>
    @endif
    {{-- Tambahkan detail lain yang ingin Anda tampilkan --}}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>e