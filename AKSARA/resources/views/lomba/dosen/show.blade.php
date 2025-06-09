@php
    // Logika untuk menentukan warna aksen berdasarkan status
    $statusClass = '';
    switch ($lomba->status_verifikasi) {
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
    <h5 class="modal-title" id="modalDetailLombaUserLabel"><i class="fas fa-award me-2"></i>Detail Pengajuan Lomba</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body bg-light" style="max-height: 68vh; overflow-y: auto;">
    <div class="container-fluid">

        {{-- KARTU STATUS PENGAJUAN (PALING PENTING) --}}
        <div class="card mb-4 shadow-sm {{ $statusClass }}" style="border-width: 0; border-left-width: 4px;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-title fw-bold">Status Pengajuan</h6>
                        {!! $lomba->status_verifikasi_badge !!}
                    </div>
                    {{-- Tombol Aksi Kontekstual --}}
                    @if($lomba->status_verifikasi == 'pending' || $lomba->status_verifikasi == 'ditolak')
                        <div>
                            {{-- <a href="{{ route('lomba.mhs.edit_form', $lomba->lomba_id) }}" class="btn btn-sm btn-outline-primary" title="Edit Pengajuan">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a> --}}
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="modalActionLomba('{{ route('lomba.dosen.edit_form', $lomba->lomba_id) }}', 'Edit Pengajuan', 'modalFormLombaUser')"><i class="fas fa-edit"></i> Edit</button>
                            {{-- <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $lomba->lomba_id }}" title="Hapus Pengajuan">
                                <i class="fas fa-trash-alt me-1"></i> Hapus
                            </button> --}}
                        </div>
                    @endif
                </div>

                @if($lomba->status_verifikasi == 'ditolak' && $lomba->catatan_verifikasi)
                    <hr>
                    <div class="alert alert-danger mb-0 mt-3 py-2">
                        <strong class="d-block"><i class="fas fa-exclamation-triangle me-1"></i> Catatan dari Admin:</strong>
                        <p class="mb-0">{{ $lomba->catatan_verifikasi }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- KARTU DETAIL LOMBA --}}
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-medal me-2"></i>Detail Informasi Lomba</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <img src="{{ asset('storage/'.$lomba->poster) }}" alt="Poster Lomba" class="img-fluid rounded border p-1" style="max-height: 250px; object-fit: contain;">
                        </div>
                        <div class="col-md-8">
                    @else
                        <div class="col-md-12">
                    @endif
                        <dl class="row">
                            <dt class="col-sm-5 text-muted">Nama Lomba</dt>
                            <dd class="col-sm-7">{{ $lomba->nama_lomba }}</dd>

                            <dt class="col-sm-5 text-muted">Penyelenggara</dt>
                            <dd class="col-sm-7">{{ $lomba->penyelenggara }}</dd>

                            <dt class="col-sm-5 text-muted">Tingkat</dt>
                            <dd class="col-sm-7">{{ ucfirst($lomba->tingkat) }}</dd>

                            <dt class="col-sm-5 text-muted">Kategori</dt>
                            <dd class="col-sm-7">{{ ucfirst($lomba->kategori) }}</dd>
                            
                            <dt class="col-sm-5 text-muted">Bidang</dt>
                            <dd class="col-sm-7">
                                @forelse($lomba->bidangKeahlian as $detail)
                                    @if($detail->bidang)
                                        <span class="badge bg-light-secondary text-dark">{{ e($detail->bidang->bidang_nama) }}</span>
                                    @endif
                                @empty
                                    -
                                @endforelse
                            </dd>

                            <dt class="col-sm-5 text-muted">Hadiah</dt>
                            <dd class="col-sm-7">
                                @forelse($lomba->daftarHadiah as $itemHadiah)
                                    <i class="fas fa-gift text-warning me-1"></i>{{ e($itemHadiah->hadiah) }}<br>
                                @empty
                                    -
                                @endforelse
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- KARTU JADWAL & TAUTAN --}}
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-calendar-alt me-2"></i>Jadwal & Tautan</h6>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-5 text-muted">Biaya Pendaftaran</dt>
                    <dd class="col-sm-7">
                        @if($lomba->biaya > 0)
                            Rp {{ number_format($lomba->biaya, 0, ',', '.') }}
                        @else
                            <span class="badge bg-light-success text-success px-2 py-1">Gratis</span>
                        @endif
                    </dd>

                    <dt class="col-sm-5 text-muted">Pendaftaran Dibuka</dt>
                    <dd class="col-sm-7">{{ $lomba->pembukaan_pendaftaran ? $lomba->pembukaan_pendaftaran->isoFormat('D MMMM YYYY') : '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Batas Pendaftaran</dt>
                    <dd class="col-sm-7">{{ $lomba->batas_pendaftaran ? $lomba->batas_pendaftaran->isoFormat('D MMMM YYYY') : '-' }}</dd>
                </dl>
                @if($lomba->link_pendaftaran || $lomba->link_penyelenggara)
                <hr>
                <div class="d-flex flex-wrap gap-2">
                     @if($lomba->link_pendaftaran)
                        <a href="{{ $lomba->link_pendaftaran }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary"><i class="fas fa-external-link-alt me-1"></i> Link Pendaftaran</a>
                    @endif
                    @if($lomba->link_penyelenggara)
                        <a href="{{ $lomba->link_penyelenggara }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary"><i class="fas fa-link me-1"></i> Website Resmi</a>
                    @endif
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
</div> --}}
