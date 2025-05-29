{{-- resources/views/lomba/show_ajax.blade.php --}}
<div class="modal-header">
    <h5 class="modal-title" id="modalDetailLombaPublikLabel">Lomba {{ Str::limit($lomba->nama_lomba, 50) }}</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
    <div class="row">
        @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
        <div class="col-md-5 mb-3 text-center">
            <img src="{{ asset('storage/'.$lomba->poster) }}" alt="Poster Lomba {{ $lomba->nama_lomba }}" class="img-fluid rounded shadow-sm" style="max-height: 350px; object-fit: contain;">
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

                <dt class="col-sm-5 col-lg-4">Bidang Keahlian Relevan:</dt>
                <dd class="col-sm-7 col-lg-8">{{ $lomba->bidang_keahlian ?: '-' }}</dd>
                
                <dt class="col-sm-5 col-lg-4">Biaya Pendaftaran:</dt>
                <dd class="col-sm-7 col-lg-8">
                    @if($lomba->biaya > 0)
                        Rp {{ number_format($lomba->biaya, 0, ',', '.') }}
                    @else
                        <span class="badge bg-light-success text-success">Gratis</span>
                    @endif
                </dd>

                <dt class="col-sm-5 col-lg-4">Pendaftaran Dibuka:</dt>
                <dd class="col-sm-7 col-lg-8">{{ $lomba->pembukaan_pendaftaran ? $lomba->pembukaan_pendaftaran->format('d F Y') : '-' }}</dd>

                <dt class="col-sm-5 col-lg-4">Batas Pendaftaran:</dt>
                <dd class="col-sm-7 col-lg-8">{{ $lomba->batas_pendaftaran ? $lomba->batas_pendaftaran->format('d F Y') : '-' }}</dd>

                @if($lomba->link_pendaftaran)
                <dt class="col-sm-5 col-lg-4">Link Pendaftaran:</dt>
                <dd class="col-sm-7 col-lg-8"><a href="{{ $lomba->link_pendaftaran }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt me-1"></i> Kunjungi Link</a></dd>
                @endif

                @if($lomba->link_penyelenggara)
                <dt class="col-sm-5 col-lg-4">Link Penyelenggara:</dt>
                <dd class="col-sm-7 col-lg-8"><a href="{{ $lomba->link_penyelenggara }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary"><i class="fas fa-link me-1"></i> Website Resmi</a></dd>
                @endif

                 @if($lomba->diinput_oleh)
                <dt class="col-sm-5 col-lg-4">Diajukan Oleh:</dt>
                <dd class="col-sm-7 col-lg-8">
                    {{ $lomba->penginput->nama ?? 'N/A' }}
                    @if($lomba->penginput)
                    ({{ ucfirst($lomba->penginput->role) }})
                    @endif
                </dd>
                @endif

                <dt class="col-sm-5 col-lg-4">Status Verifikasi:</dt>
                <dd class="col-sm-7 col-lg-8">{!! $lomba->status_verifikasi_badge !!}</dd>
            </dl>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>