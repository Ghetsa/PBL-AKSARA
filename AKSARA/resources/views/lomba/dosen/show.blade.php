{{-- File ini akan dimuat di dalam modal, jadi tidak perlu @extends layout --}}
{{-- Pastikan variabel $lomba sudah di-pass ke view ini dari controller --}}

<div class="modal-header">
    <h5 class="modal-title" id="modalDetailLombaUserLabel">Detail Pengajuan Lomba: {{ Str::limit($lomba->nama_lomba, 40) }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
    <div class="row">
        @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
        <div class="col-md-4 mb-3 text-center">
            <img src="{{ asset('storage/'.$lomba->poster) }}" alt="Poster Lomba {{ $lomba->nama_lomba }}" class="img-fluid rounded shadow-sm" style="max-height: 300px; object-fit: contain; border: 1px solid #eee;">
        </div>
        <div class="col-md-8">
        @else
        <div class="col-md-12">
        @endif
            <dl class="row">
                <dt class="col-sm-5 col-lg-4">Nama Lomba:</dt>
                <dd class="col-sm-7 col-lg-8">{{ $lomba->nama_lomba }}</dd>

                <dt class="col-sm-5 col-lg-4">Penyelenggara:</dt>
                <dd class="col-sm-7 col-lg-8">{{ $lomba->penyelenggara }}</dd>

                <dt class="col-sm-5 col-lg-4">Tingkat:</dt>
                <dd class="col-sm-7 col-lg-8">{{ ucfirst($lomba->tingkat) }}</dd>

                <dt class="col-sm-5 col-lg-4">Kategori Peserta:</dt>
                <dd class="col-sm-7 col-lg-8">{{ ucfirst($lomba->kategori) }}</dd>

                <dt class="col-sm-5 col-lg-4">Bidang Lomba:</dt>
                <dd class="col-sm-7 col-lg-8">
                    @if($lomba->bidangKeahlian && $lomba->bidangKeahlian->count() > 0)
                        @foreach($lomba->bidangKeahlian as $detail)
                            @if($detail->bidang)
                                <span class="badge bg-light-secondary text-dark me-1 mb-1 p-2">{{ e($detail->bidang->bidang_nama) }}</span>
                            @endif
                        @endforeach
                    @else
                        -
                    @endif
                </dd>
                
                {{-- Menampilkan Daftar Hadiah --}}
                <dt class="col-sm-5 col-lg-4">Hadiah yang Diajukan:</dt>
                <dd class="col-sm-7 col-lg-8">
                    @if($lomba->daftarHadiah && $lomba->daftarHadiah->count() > 0)
                        <ul class="list-unstyled mb-0 ps-0">
                            @foreach($lomba->daftarHadiah as $itemHadiah)
                                <li><i class="fas fa-medal text-info me-2"></i>{{ e($itemHadiah->hadiah) }}</li>
                            @endforeach
                        </ul>
                    @else
                        Tidak ada informasi hadiah yang diajukan.
                    @endif
                </dd>

                <dt class="col-sm-5 col-lg-4">Biaya Pendaftaran:</dt>
                <dd class="col-sm-7 col-lg-8">
                    @if($lomba->biaya > 0)
                        Rp {{ number_format($lomba->biaya, 0, ',', '.') }}
                    @else
                        <span class="badge bg-light-success text-success px-2 py-1">Gratis</span>
                    @endif
                </dd>

                <dt class="col-sm-5 col-lg-4">Pendaftaran Dibuka:</dt>
                <dd class="col-sm-7 col-lg-8">{{ $lomba->pembukaan_pendaftaran ? $lomba->pembukaan_pendaftaran->isoFormat('D MMMM YYYY') : '-' }}</dd>

                <dt class="col-sm-5 col-lg-4">Batas Pendaftaran:</dt>
                <dd class="col-sm-7 col-lg-8">{{ $lomba->batas_pendaftaran ? $lomba->batas_pendaftaran->isoFormat('D MMMM YYYY') : '-' }}</dd>

                @if($lomba->link_pendaftaran)
                <dt class="col-sm-5 col-lg-4">Link Pendaftaran:</dt>
                <dd class="col-sm-7 col-lg-8"><a href="{{ $lomba->link_pendaftaran }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary"><i class="fas fa-external-link-alt me-1"></i> Kunjungi</a></dd>
                @endif

                @if($lomba->link_penyelenggara)
                <dt class="col-sm-5 col-lg-4">Link Penyelenggara:</dt>
                <dd class="col-sm-7 col-lg-8"><a href="{{ $lomba->link_penyelenggara }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary"><i class="fas fa-link me-1"></i> Website</a></dd>
                @endif

                <dt class="col-sm-5 col-lg-4">Status Pengajuan:</dt>
                <dd class="col-sm-7 col-lg-8">{!! $lomba->status_verifikasi_badge !!}</dd>

                @if($lomba->status_verifikasi == 'ditolak' && $lomba->catatan_verifikasi)
                <dt class="col-sm-5 col-lg-4 text-danger">Catatan dari Admin:</dt>
                <dd class="col-sm-7 col-lg-8 text-danger fst-italic">{{ $lomba->catatan_verifikasi }}</dd>
                @endif
            </dl>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
</div>
