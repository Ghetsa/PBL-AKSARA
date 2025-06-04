{{-- resources/views/lomba/show_ajax.blade.php --}}
<div class="modal-header"> {{-- Mengubah warna header agar konsisten dengan modal detail lainnya --}}
    <h5 class="modal-title" id="modalDetailLombaPublikLabel">Detail Lomba: {{ Str::limit($lomba->nama_lomba, 45) }}</h5>
    <button type="button" class="btn-close btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
    <div class="row">
        @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
        <div class="col-md-5 mb-3 text-center">
            <img src="{{ asset('storage/'.$lomba->poster) }}" alt="Poster Lomba {{ $lomba->nama_lomba }}" class="img-fluid rounded shadow-sm" style="max-height: 350px; object-fit: contain; border: 1px solid #eee;">
        </div>
        <div class="col-md-7">
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
                <dt class="col-sm-5 col-lg-4">Hadiah:</dt>
                <dd class="col-sm-7 col-lg-8">
                    @if($lomba->daftarHadiah && $lomba->daftarHadiah->count() > 0)
                        <ul class="list-unstyled mb-0">
                            @foreach($lomba->daftarHadiah as $itemHadiah)
                                <li><i class="fas fa-gift text-warning me-2"></i>{{ e($itemHadiah->hadiah) }}</li>
                            @endforeach
                        </ul>
                    @else
                        Informasi hadiah tidak tersedia.
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
                <dd class="col-sm-7 col-lg-8"><a href="{{ $lomba->link_pendaftaran }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt me-1"></i> Kunjungi Link</a></dd>
                @endif

                @if($lomba->link_penyelenggara)
                <dt class="col-sm-5 col-lg-4">Link Penyelenggara:</dt>
                <dd class="col-sm-7 col-lg-8"><a href="{{ $lomba->link_penyelenggara }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary"><i class="fas fa-link me-1"></i> Website Resmi</a></dd>
                @endif

                 @if($lomba->inputBy)
                <dt class="col-sm-5 col-lg-4">Diajukan Oleh:</dt>
                <dd class="col-sm-7 col-lg-8">
                    {{ $lomba->inputBy->nama ?? 'N/A' }}
                    @if($lomba->inputBy->role)
                    ({{ ucfirst($lomba->inputBy->role) }})
                    @endif
                </dd>
                @endif

                <dt class="col-sm-5 col-lg-4">Status Verifikasi:</dt>
                <dd class="col-sm-7 col-lg-8">{!! $lomba->status_verifikasi_badge !!}</dd>

                @if($lomba->status_verifikasi == 'ditolak' && $lomba->catatan_verifikasi)
                <dt class="col-sm-5 col-lg-4 text-danger">Catatan Penolakan:</dt>
                <dd class="col-sm-7 col-lg-8 text-danger">{{ $lomba->catatan_verifikasi }}</dd>
                @endif
            </dl>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
</div>
