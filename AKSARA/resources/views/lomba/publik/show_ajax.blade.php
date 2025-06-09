{{-- <div class="modal-header bg-primary text-white"> 
    <h5 class="modal-title" id="modalDetailLombaPublikLabel"><i class="fas fa-trophy me-2"></i> Detail Lomba: {{ Str::limit($lomba->nama_lomba, 45) }}</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
    <div class="row">
        @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
        <div class="col-md-6 mb-4 text-center"> 
            <img src="{{ as6et('storage/'.$lomba->poster) }}" alt="Poster Lomba {{ $lomba->nama_lomba }}" class="img-fluid rounded shadow-lg" style="max-height: 380px; object-fit: contain; border: 3px solid #dee2e6;"> 
        </div>
        <div class="col-md-7">
        @else
        <div class="col-md-12">
        @endif
            <h6 class="mb-3 text-primary">Informasi Umum Lomba</h6>
            <dl class="row mb-3"> 
                <dt class="col-lg-5 col-md-6 col-lg-6 text-muted">Nama Lomba:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6 text-dark">{{ $lomba->nama_lomba }}</dd>

                <dt class="col-lg-5 col-md-6 col-lg-6 text-muted">Penyelenggara:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6 text-dark">{{ $lomba->penyelenggara }}</dd>

                <dt class="col-lg-5 col-md-6 col-lg-6 text-muted">Tingkat:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6 text-dark"><span class="badge bg-info">{{ ucfirst($lomba->tingkat) }}</span></dd> 

                <dt class="col-lg-5 col-md-6 col-lg-6 text-muted">Batas Pendaftaran:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6 text-dark">{{ \Carbon\Carbon::parse($lomba->batas_pendaftaran)->format('d M Y') }}</dd>

                <dt class="col-lg-5 col-md-6 col-lg-6 text-muted">Biaya Pendaftaran:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6 text-dark">
                    @if ($lomba->biaya == 0)
                        Gratis
                    @else
                        Rp{{ number_format($lomba->biaya, 0, ',', '.') }}
                    @endif
                </dd>
            </dl>

            <h6 class="mb-3 text-primary">Linimasa & Kategori</h6> 
            <dl class="row mb-3">
                <dt class="col-lg-5 col-md-6 col-lg-6 text-muted">Pembukaan Pendaftaran:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6 text-dark">{{ \Carbon\Carbon::parse($lomba->pembukaan_pendaftaran)->format('d M Y') }}</dd>

                <dt class="col-lg-5 col-md-6 col-lg-6 text-muted">Tanggal Pelaksanaan:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6 text-dark">{{ \Carbon\Carbon::parse($lomba->tanggal_pelaksanaan)->format('d M Y') }}</dd>

                <dt class="col-lg-5 col-md-6 col-lg-6 text-muted">Batas Pengumpulan Karya:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6 text-dark">{{ $lomba->batas_pengumpulan_karya ? \Carbon\Carbon::parse($lomba->batas_pengumpulan_karya)->format('d M Y') : 'Tidak Ada' }}</dd>

                <dt class="col-lg-5 col-md-6 col-lg-6 text-muted">Bidang Lomba:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6 text-dark"><span class="badge bg-secondary">{{ $lomba->bidang->nama_bidang ?? 'N/A' }}</span></dd> 
            </dl>

            <dt class="col-lg-5 col-md-6 col-lg-6">Hadiah:</dt>
                <dd class="lgl-scol-md-6 m-7 col6lg-6">
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

            <h6 class="mb-3 text-primary">Deskripsi Lomba</h6> 
            <div class="card card-body bg-light mb-3"> 
                <p class="text-dark">{!! nl2br(e($lomba->deskripsi_lomba)) !!}</p>
            </div>

            <h6 class="mb-3 text-primary">Informasi Tambahan</h6> 
            <dl class="row mb-3">
                @if($lomba->link_pendaftaran)
                <dt class="col-lg-5 col-md-6 col-lg-6 text-muted">Link Pendaftaran:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6"><a href="{{ $lomba->link_pendaftaran }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success"><i class="fas fa-external-link-alt me-1"></i> Daftar Sekarang</a></dd> 
                @endif

                @if($lomba->link_penyelenggara)
                <dt class="col-lg-5 col-md-6 col-lg-6 text-muted">Link Penyelenggara:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6"><a href="{{ $lomba->link_penyelenggara }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info"><i class="fas fa-link me-1"></i> Website Resmi</a></dd> 
                @endif

                 @if($lomba->inputBy)
                <dt class="col-lg-5 col-md-6 col-lg-6 text-muted">Diajukan Oleh:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6 text-dark">
                    {{ $lomba->inputBy->nama ?? 'N/A' }}
                    @if($lomba->inputBy->role)
                    (<span class="fw-bold">{{ ucfirst($lomba->inputBy->role) }}</span>)
                    @endif
                </dd>
                @endif

                <dt class="col-lg-5 col-md-6 col-lg-6 text-muted">Status Verifikasi:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6">{!! $lomba->status_verifikasi_badge !!}</dd>

                @if($lomba->status_verifikasi == 'ditolak' && $lomba->catatan_verifikasi)
                <dt class="col-lg-5 col-md-6 col-lg-6 text-danger">Catatan Penolakan:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6 text-danger">{{ $lomba->catatan_verifikasi }}</dd>
                @endif
            </dl>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Tutup</button>
</div> --}}

<div class="modal-header">
    <h5 class="modal-title" id="modalDetailLombaPublikLabel"><i class="fas fa-award me-2"></i>Detail Lomba: {{ Str::limit($lomba->nama_lomba, 45) }}</h5>
    <button type="button" class="btn-close btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" style="max-height: 68vh; overflow-y: auto;">
    <div class="row">
        @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
        <div class="col-md-6 mb-3 text-center">
            <img src="{{ asset('storage/'.$lomba->poster) }}" alt="Poster Lomba {{ $lomba->nama_lomba }}" class="img-fluid rounded shadow-sm" style="max-height: 400px; object-fit: contain; border: 1px solid #eee;">
        </div>
        <div class="col-md-6">
        @else
        <div class="col-md-12">
        @endif
            <dl class="row">
                {{-- <dt class="col-lg-5 col-md-6 col-lg-6">Nama Lomba:</dt>
                <dd class="col-sm-7 lgl-md-6 col-lg-6">{{ $lomba->nama_lomba }}</dd>

                <dt class="col-lg-5 col-md-6 col-lg-6">Penyelenggara:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6">{{ $lomba->penyelenggara }}</dd>

                <dt class="col-lg-5 col-md-6 col-lg-6">Tingkat:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6">{{ ucfirst($lomba->tingkat) }}</dd>

                <dt class="col-lg-5 col-md-6 col-lg-6">Kategori Peserta:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6">{{ ucfirst($lomba->kategori) }}</dd>

                <dt class="col-lg-5 col-md-6 col-lg-6">Bidang Lomba:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6">
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
                
                <dt class="col-lg-5 col-md-6 col-lg-6">Hadiah:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6">
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

                <dt class="col-lg-5 col-md-6 col-lg-6">Biaya Pendaftaran:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6">
                    @if($lomba->biaya > 0)
                        Rp {{ number_format($lomba->biaya, 0, ',', '.') }}
                    @else
                        <span class="badge bg-light-success text-success px-2 py-1">Gratis</span>
                    @endif
                </dd>

                <dt class="col-lg-5 col-md-6 col-lg-6">Pendaftaran Dibuka:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6">{{ $lomba->pembukaan_pendaftaran ? $lomba->pembukaan_pendaftaran->isoFormat('D MMMM YYYY') : '-' }}</dd>

                <dt class="col-lg-5 col-md-6 col-lg-6">Batas Pendaftaran:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6">{{ $lomba->batas_pendaftaran ? $lomba->batas_pendaftaran->isoFormat('D MMMM YYYY') : '-' }}</dd>

                @if($lomba->link_pendaftaran)
                <dt class="col-lg-5 col-md-6 col-lg-6">Link Pendaftaran:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6"><a href="{{ $lomba->link_pendaftaran }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt me-1"></i> Kunjungi Link</a></dd>
                @endif

                @if($lomba->link_penyelenggara)
                <dt class="col-lg-5 col-md-6 col-lg-6">Link Penyelenggara:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6"><a href="{{ $lomba->link_penyelenggara }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info"><i class="fas fa-link me-1"></i> Website Resmi</a></dd>
                @endif

                 @if($lomba->inputBy)
                <dt class="col-lg-5 col-md-6 col-lg-6">Diajukan Oleh:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6">
                    {{ $lomba->inputBy->nama ?? 'N/A' }}
                    @if($lomba->inputBy->role)
                    ({{ ucfirst($lomba->inputBy->role) }})
                    @endif
                </dd>
                @endif

                <dt class="col-lg-5 col-md-6 col-lg-6">Status Verifikasi:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6">{!! $lomba->status_verifikasi_badge !!}</dd>

                @if($lomba->status_verifikasi == 'ditolak' && $lomba->catatan_verifikasi)
                <dt class="col-lg-5 col-md-6 col-lg-6 text-danger">Catatan Penolakan:</dt>
                <dd class="col-lg-7 col-md-6 col-lg-6 text-danger">{{ $lomba->catatan_verifikasi }}</dd>
                @endif --}}
                {{-- Card Informasi Utama --}}
                <div class="card mb-3">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Utama</h6>
                    </div>
                    <div class="card-body py-2 px-3">
                        <dl class="row mb-0">
                            <dt class="col-lg-5"><i class="fas fa-medal fa-fw me-1 text-muted"></i>Nama Lomba</dt>
                            <dd class="col-lg-7">{{ $lomba->nama_lomba }}</dd>

                            <dt class="col-lg-5"><i class="fas fa-university fa-fw me-1 text-muted"></i>Penyelenggara</dt>
                            <dd class="col-lg-7">{{ $lomba->penyelenggara }}</dd>

                            <dt class="col-lg-5"><i class="fas fa-signal fa-fw me-1 text-muted"></i>Tingkat</dt>
                            <dd class="col-lg-7">{{ ucfirst($lomba->tingkat) }}</dd>

                            <dt class="col-lg-5"><i class="fas fa-users fa-fw me-1 text-muted"></i>Kategori</dt>
                            <dd class="col-lg-7">{{ ucfirst($lomba->kategori) }}</dd>

                            <dt class="col-lg-5"><i class="fas fa-tags fa-fw me-1 text-muted"></i>Bidang</dt>
                            <dd class="col-lg-7">
                                @forelse($lomba->bidangKeahlian as $detail)
                                    @if($detail->bidang)
                                        <span class="badge bg-light-secondary text-dark me-1 mb-1 p-2">{{ e($detail->bidang->bidang_nama) }}</span>
                                    @endif
                                @empty
                                    -
                                @endforelse
                            </dd>
                        </dl>
                    </div>
                </div>

                {{-- Card Jadwal & Biaya --}}
                <div class="card mb-3">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-calendar-alt me-2 text-primary"></i>Jadwal Pendaftaran & Biaya</h6>
                    </div>
                    <div class="card-body py-2 px-3">
                        <dl class="row mb-0">
                            <dt class="col-lg-5"><i class="fas fa-calendar-plus fa-fw me-1 text-muted"></i>Buka Daftar</dt>
                            <dd class="col-lg-7">{{ $lomba->pembukaan_pendaftaran ? $lomba->pembukaan_pendaftaran->isoFormat('D MMM YYYY') : '-' }}</dd>
                            
                            <dt class="col-lg-5"><i class="fas fa-calendar-times fa-fw me-1 text-muted"></i>Batas Daftar</dt>
                            <dd class="col-lg-7">{{ $lomba->batas_pendaftaran ? $lomba->batas_pendaftaran->isoFormat('D MMM YYYY') : '-' }}</dd>
                            
                            <dt class="col-lg-5"><i class="fas fa-money-bill-wave fa-fw me-1 text-muted"></i>Biaya</dt>
                            <dd class="col-lg-7">
                                @if($lomba->biaya > 0)
                                    Rp {{ number_format($lomba->biaya, 0, ',', '.') }}
                                @else
                                    <span class="badge bg-light-success text-success px-2 py-1">Gratis</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                {{-- Card Hadiah & Tautan --}}
                <div class="card mb-3">
                    <div class="card-header bg-white py-2">
                         <h6 class="mb-0 fw-semibold"><i class="fas fa-gifts me-2 text-primary"></i>Hadiah & Tautan</h6>
                    </div>
                    <div class="card-body py-2 px-3">
                            <h6 class="fw-semibold">Hadiah:</h6>
                        @if($lomba->daftarHadiah && $lomba->daftarHadiah->count() > 0)
                            <ul class="list-unstyled mb-3 ps-0">
                                @foreach($lomba->daftarHadiah as $itemHadiah)
                                    <li><i class="fas fa-gift text-warning me-2"></i>{{ e($itemHadiah->hadiah) }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">Informasi hadiah tidak tersedia.</p>
                        @endif

                        @if($lomba->link_pendaftaran || $lomba->link_penyelenggara)
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @if($lomba->link_pendaftaran)
                                <a href="{{ $lomba->link_pendaftaran }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt me-1"></i> Link Pendaftaran</a>
                            @endif
                            @if($lomba->link_penyelenggara)
                                <a href="{{ $lomba->link_penyelenggara }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary"><i class="fas fa-link me-1"></i> Website Resmi</a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </dl>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
</div>
