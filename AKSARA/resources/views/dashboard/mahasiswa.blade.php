@extends('layouts.template')

@section('title', $breadcrumb->title ?? 'Dashboard Mahasiswa')

@push('css')
    {{-- Tambahan CSS jika diperlukan --}}
    <style>
        .card-lomba .card-img-top, .card-prestasi .card-img-top {
            height: 180px;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $breadcrumb->title }}</h1>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Selamat Datang Kembali!</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->nama }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">🏆 Rekomendasi Lomba Untuk Anda</h4>
        </div>
        @forelse ($rekomendasiLomba as $lomba)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card card-lomba border-left-success shadow h-100 py-2">
                    @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
                        <img src="{{ asset('storage/'.$lomba->poster) }}" class="card-img-top" alt="Poster {{ $lomba->nama_lomba }}">
                    @else
                        <img src="{{ asset('path/to/default-lomba.jpg') }}" class="card-img-top" alt="Poster Default"> {{-- Ganti dengan path default poster lomba --}}
                    @endif
                    <div class="card-body">
                        <div class="row no-gutters align-items-center mb-2">
                            <div class="col">
                                <h5 class="card-title text-success font-weight-bold text-uppercase mb-1" style="font-size: 1.1rem;">{{ Str::limit($lomba->nama_lomba, 45) }}</h5>
                                <div class="text-xs mb-1">Penyelenggara: {{ $lomba->penyelenggara }}</div>
                                <div class="text-xs mb-1">Tingkat: {{ ucfirst($lomba->tingkat) }} | Kategori: {{ ucfirst($lomba->kategori) }}</div>
                                <div class="text-xs mb-2">Batas Daftar: <span class="font-weight-bold">{{ $lomba->batas_pendaftaran ? $lomba->batas_pendaftaran->isoFormat('D MMM YYYY') : 'N/A' }}</span></div>
                                @if(isset($lomba->score)) {{-- Jika score MOORA ada --}}
                                    <div class="text-xs mb-1">Skor Rekomendasi: <span class="badge bg-info text-white">{{ number_format($lomba->score, 4) }}</span></div>
                                @endif
                            </div>
                        </div>
                         <a href="{{-- route('lomba.show', $lomba->lomba_id) --}}" onclick="modalActionLombaAdminCrud('{{ route('lomba.publik.show_ajax', $lomba->lomba_id) }}', 'Detail Lomba', 'modalDetailLombaAdminCrud')" class="btn btn-success btn-sm mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted">Belum ada rekomendasi lomba yang sesuai untuk Anda saat ini.</p>
            </div>
        @endforelse
    </div>
    <hr class="my-4">

    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-3">🌟 Prestasi Anda</h4>
            <a href="{{ route('prestasi.mahasiswa.index') }}" class="btn btn-sm btn-outline-primary mb-3">Lihat Semua Prestasi Saya</a>
        </div>
        @forelse ($prestasiMahasiswa as $prestasi)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card card-prestasi border-left-info shadow h-100 py-2">
                     @if($prestasi->bukti_prestasi && Storage::disk('public')->exists($prestasi->bukti_prestasi))
                        <img src="{{ asset('storage/'.$prestasi->bukti_prestasi) }}" class="card-img-top" alt="Bukti {{ $prestasi->nama_prestasi }}">
                    @else
                        <img src="{{ asset('path/to/default-prestasi.jpg') }}" class="card-img-top" alt="Tidak Ada Bukti"> {{-- Ganti path default --}}
                    @endif
                    <div class="card-body">
                        <h5 class="card-title text-info font-weight-bold text-uppercase mb-1" style="font-size: 1.1rem;">{{ Str::limit($prestasi->nama_prestasi, 45) }}</h5>
                        <div class="text-xs mb-1">Penyelenggara: {{ $prestasi->penyelenggara_prestasi }}</div>
                        <div class="text-xs mb-1">Tingkat: {{ ucfirst($prestasi->tingkat_prestasi) }} | Kategori: {{ ucfirst($prestasi->kategori_prestasi) }}</div>
                        <div class="text-xs mb-2">Tanggal: {{ $prestasi->tanggal_pelaksanaan_prestasi ? Carbon\Carbon::parse($prestasi->tanggal_pelaksanaan_prestasi)->isoFormat('D MMM YYYY') : 'N/A' }}</div>
                        <div class="text-xs mb-1">Status: {!! $prestasi->status_verifikasi_badge !!}</div>
                         <a href="#" onclick="modalActionPrestasi('{{ route('prestasi.mahasiswa.show_ajax', $prestasi->prestasi_id) }}', 'Detail Prestasi', 'modalDetailPrestasi')" class="btn btn-info btn-sm mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted">Anda belum memiliki catatan prestasi. <a href="{{ route('prestasi.mahasiswa.create_ajax') }}" onclick="modalActionPrestasi('{{ route('prestasi.mahasiswa.create_ajax') }}', 'Tambah Prestasi', 'modalFormPrestasi')">Ajukan sekarang!</a></p>
            </div>
        @endforelse
    </div>
    <hr class="my-4">

    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">🏅 Prestasi Umum Mahasiswa</h4>
        </div>
        @forelse ($prestasiPublik as $prestasi)
             <div class="col-xl-4 col-md-6 mb-4">
                <div class="card card-prestasi border-left-secondary shadow h-100 py-2">
                     @if($prestasi->bukti_prestasi && Storage::disk('public')->exists($prestasi->bukti_prestasi))
                        <img src="{{ asset('storage/'.$prestasi->bukti_prestasi) }}" class="card-img-top" alt="Bukti {{ $prestasi->nama_prestasi }}">
                    @else
                        <img src="{{ asset('path/to/default-prestasi.jpg') }}" class="card-img-top" alt="Tidak Ada Bukti">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title text-secondary font-weight-bold text-uppercase mb-1" style="font-size: 1.1rem;">{{ Str::limit($prestasi->nama_prestasi, 45) }}</h5>
                        <div class="text-xs mb-1">Diraih oleh: {{ $prestasi->mahasiswa->user->nama ?? 'N/A' }} ({{ $prestasi->mahasiswa->prodi->nama_prodi ?? 'N/A' }})</div>
                        <div class="text-xs mb-1">Penyelenggara: {{ $prestasi->penyelenggara_prestasi }}</div>
                        <div class="text-xs mb-1">Tingkat: {{ ucfirst($prestasi->tingkat_prestasi) }}</div>
                         <a href="#" onclick="modalActionPrestasi('{{ route('prestasi.mahasiswa.show_ajax', $prestasi->prestasi_id) }}', 'Detail Prestasi', 'modalDetailPrestasi')" class="btn btn-secondary btn-sm mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted">Belum ada prestasi umum untuk ditampilkan.</p>
            </div>
        @endforelse
    </div>
    <hr class="my-4">

    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-3">📢 Info Lomba Terkini</h4>
             <a href="{{ route('lomba.publik.index') }}" class="btn btn-sm btn-outline-primary mb-3">Lihat Semua Lomba</a>
        </div>
        @forelse ($lombaUmum as $lomba)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card card-lomba border-left-primary shadow h-100 py-2">
                    @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
                        <img src="{{ asset('storage/'.$lomba->poster) }}" class="card-img-top" alt="Poster {{ $lomba->nama_lomba }}">
                    @else
                        <img src="{{ asset('path/to/default-lomba.jpg') }}" class="card-img-top" alt="Poster Default">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title text-primary font-weight-bold text-uppercase mb-1" style="font-size: 1.1rem;">{{ Str::limit($lomba->nama_lomba, 45) }}</h5>
                        <div class="text-xs mb-1">Penyelenggara: {{ $lomba->penyelenggara }}</div>
                        <div class="text-xs mb-2">Batas Daftar: <span class="font-weight-bold">{{ $lomba->batas_pendaftaran ? $lomba->batas_pendaftaran->isoFormat('D MMM YYYY') : 'N/A' }}</span></div>
                         <a href="{{-- route('lomba.show', $lomba->lomba_id) --}}" onclick="modalActionLombaAdminCrud('{{ route('lomba.publik.show_ajax', $lomba->lomba_id) }}', 'Detail Lomba', 'modalDetailLombaAdminCrud')" class="btn btn-primary btn-sm mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @empty
             <div class="col-12">
                <p class="text-center text-muted">Tidak ada info lomba terkini.</p>
            </div>
        @endforelse
    </div>

</div>

{{-- Modal untuk Detail Lomba (Reuse dari admin CRUD jika struktur sama atau buat baru) --}}
<div class="modal fade" id="modalDetailLombaAdminCrud" tabindex="-1" aria-labelledby="modalDetailLombaAdminCrudLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div>

{{-- Modal untuk Detail Prestasi --}}
<div class="modal fade" id="modalDetailPrestasi" tabindex="-1" aria-labelledby="modalDetailPrestasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div>
{{-- Modal untuk Form Prestasi --}}
<div class="modal fade" id="modalFormPrestasi" tabindex="-1" aria-labelledby="modalFormPrestasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div>

@endsection

@push('js')
<script>
    // Fungsi modalActionLombaAdminCrud sudah ada di lomba.admin.crud.index.
    // Jika halaman ini terpisah, Anda mungkin perlu mendefinisikannya di sini atau di template global.
    // Untuk saat ini, saya asumsikan itu tersedia secara global atau akan ditambahkan di layouts.script.
    // Jika belum, ini contohnya:
    if (typeof modalActionLombaAdminCrud === 'undefined') {
        function modalActionLombaAdminCrud(url, title = 'Detail', modalId = 'modalDetailLombaAdminCrud') {
            const targetModal = $(`#${modalId}`);
            const targetModalContent = targetModal.find('.modal-content');
            targetModalContent.html('<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat...</p></div>');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));
            modalInstance.show();
            $.ajax({
                url: url, type: 'GET',
                success: function (response) { targetModalContent.html(response); },
                error: function (xhr) { 
                    let msg = xhr.responseJSON?.message ?? 'Gagal memuat konten.';
                    targetModalContent.html(`<div class="modal-header"><h5 class="modal-title">${title}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p class="text-danger">${msg}</p></div>`);
                }
            });
        }
    }
    // Fungsi untuk modal prestasi (mirip dengan lomba)
    function modalActionPrestasi(url, title = 'Detail Prestasi', modalId = 'modalDetailPrestasi') {
        const targetModal = $(`#${modalId}`);
        const targetModalContent = targetModal.find('.modal-content');
        targetModalContent.html('<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat...</p></div>');
        const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));
        modalInstance.show();
        $.ajax({
            url: url, type: 'GET',
            success: function (response) { targetModalContent.html(response); },
            error: function (xhr) { 
                let msg = xhr.responseJSON?.message ?? 'Gagal memuat konten.';
                targetModalContent.html(`<div class="modal-header"><h5 class="modal-title">${title}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p class="text-danger">${msg}</p></div>`);
            }
        });
    }
</script>
@endpush

{{-- @extends('layouts.template')

@section('content')
<!-- [ Main Content ] start -->
      <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-md-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-2 f-w-400 text-muted">Total Page Views</h6>
              <h4 class="mb-3">4,42,236 <span class="badge bg-light-primary border border-primary"><i
                    class="ti ti-trending-up"></i> 59.3%</span></h4>
              <p class="mb-0 text-muted text-sm">You made an extra <span class="text-primary">35,000</span> this year
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-2 f-w-400 text-muted">Total Users</h6>
              <h4 class="mb-3">78,250 <span class="badge bg-light-success border border-success"><i
                    class="ti ti-trending-up"></i> 70.5%</span></h4>
              <p class="mb-0 text-muted text-sm">You made an extra <span class="text-success">8,900</span> this year</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-2 f-w-400 text-muted">Total Order</h6>
              <h4 class="mb-3">18,800 <span class="badge bg-light-warning border border-warning"><i
                    class="ti ti-trending-down"></i> 27.4%</span></h4>
              <p class="mb-0 text-muted text-sm">You made an extra <span class="text-warning">1,943</span> this year</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-2 f-w-400 text-muted">Total Sales</h6>
              <h4 class="mb-3">$35,078 <span class="badge bg-light-danger border border-danger"><i
                    class="ti ti-trending-down"></i> 27.4%</span></h4>
              <p class="mb-0 text-muted text-sm">You made an extra <span class="text-danger">$20,395</span> this year
              </p>
            </div>
          </div>
        </div>

        <div class="col-md-12 col-xl-8">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0">Unique Visitor</h5>
            <ul class="nav nav-pills justify-content-end mb-0" id="chart-tab-tab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="chart-tab-home-tab" data-bs-toggle="pill" data-bs-target="#chart-tab-home"
                  type="button" role="tab" aria-controls="chart-tab-home" aria-selected="true">Month</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="chart-tab-profile-tab" data-bs-toggle="pill"
                  data-bs-target="#chart-tab-profile" type="button" role="tab" aria-controls="chart-tab-profile"
                  aria-selected="false">Week</button>
              </li>
            </ul>
          </div>
          <div class="card">
            <div class="card-body">
              <div class="tab-content" id="chart-tab-tabContent">
                <div class="tab-pane" id="chart-tab-home" role="tabpanel" aria-labelledby="chart-tab-home-tab"
                  tabindex="0">
                  <div id="visitor-chart-1"></div>
                </div>
                <div class="tab-pane show active" id="chart-tab-profile" role="tabpanel"
                  aria-labelledby="chart-tab-profile-tab" tabindex="0">
                  <div id="visitor-chart"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12 col-xl-4">
          <h5 class="mb-3">Income Overview</h5>
          <div class="card">
            <div class="card-body">
              <h6 class="mb-2 f-w-400 text-muted">This Week Statistics</h6>
              <h3 class="mb-3">$7,650</h3>
              <div id="income-overview-chart"></div>
            </div>
          </div>
        </div>

        <div class="col-md-12 col-xl-8">
          <h5 class="mb-3">Recent Orders</h5>
          <div class="card tbl-card">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-borderless mb-0">
                  <thead>
                    <tr>
                      <th>TRACKING NO.</th>
                      <th>PRODUCT NAME</th>
                      <th>TOTAL ORDER</th>
                      <th>STATUS</th>
                      <th class="text-end">TOTAL AMOUNT</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><a href="#" class="text-muted">84564564</a></td>
                      <td>Camera Lens</td>
                      <td>40</td>
                      <td><span class="d-flex align-items-center gap-2"><i
                            class="fas fa-circle text-danger f-10 m-r-5"></i>Rejected</span>
                      </td>
                      <td class="text-end">$40,570</td>
                    </tr>
                    <tr>
                      <td><a href="#" class="text-muted">84564564</a></td>
                      <td>Laptop</td>
                      <td>300</td>
                      <td><span class="d-flex align-items-center gap-2"><i
                            class="fas fa-circle text-warning f-10 m-r-5"></i>Pending</span>
                      </td>
                      <td class="text-end">$180,139</td>
                    </tr>
                    <tr>
                      <td><a href="#" class="text-muted">84564564</a></td>
                      <td>Mobile</td>
                      <td>355</td>
                      <td><span class="d-flex align-items-center gap-2"><i
                            class="fas fa-circle text-success f-10 m-r-5"></i>Approved</span></td>
                      <td class="text-end">$180,139</td>
                    </tr>
                    <tr>
                      <td><a href="#" class="text-muted">84564564</a></td>
                      <td>Camera Lens</td>
                      <td>40</td>
                      <td><span class="d-flex align-items-center gap-2"><i
                            class="fas fa-circle text-danger f-10 m-r-5"></i>Rejected</span>
                      </td>
                      <td class="text-end">$40,570</td>
                    </tr>
                    <tr>
                      <td><a href="#" class="text-muted">84564564</a></td>
                      <td>Laptop</td>
                      <td>300</td>
                      <td><span class="d-flex align-items-center gap-2"><i
                            class="fas fa-circle text-warning f-10 m-r-5"></i>Pending</span>
                      </td>
                      <td class="text-end">$180,139</td>
                    </tr>
                    <tr>
                      <td><a href="#" class="text-muted">84564564</a></td>
                      <td>Mobile</td>
                      <td>355</td>
                      <td><span class="d-flex align-items-center gap-2"><i
                            class="fas fa-circle text-success f-10 m-r-5"></i>Approved</span></td>
                      <td class="text-end">$180,139</td>
                    </tr>
                    <tr>
                      <td><a href="#" class="text-muted">84564564</a></td>
                      <td>Camera Lens</td>
                      <td>40</td>
                      <td><span class="d-flex align-items-center gap-2"><i
                            class="fas fa-circle text-danger f-10 m-r-5"></i>Rejected</span>
                      </td>
                      <td class="text-end">$40,570</td>
                    </tr>
                    <tr>
                      <td><a href="#" class="text-muted">84564564</a></td>
                      <td>Laptop</td>
                      <td>300</td>
                      <td><span class="d-flex align-items-center gap-2"><i
                            class="fas fa-circle text-warning f-10 m-r-5"></i>Pending</span>
                      </td>
                      <td class="text-end">$180,139</td>
                    </tr>
                    <tr>
                      <td><a href="#" class="text-muted">84564564</a></td>
                      <td>Mobile</td>
                      <td>355</td>
                      <td><span class="d-flex align-items-center gap-2"><i
                            class="fas fa-circle text-success f-10 m-r-5"></i>Approved</span></td>
                      <td class="text-end">$180,139</td>
                    </tr>
                    <tr>
                      <td><a href="#" class="text-muted">84564564</a></td>
                      <td>Mobile</td>
                      <td>355</td>
                      <td><span class="d-flex align-items-center gap-2"><i
                            class="fas fa-circle text-success f-10 m-r-5"></i>Approved</span></td>
                      <td class="text-end">$180,139</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12 col-xl-4">
          <h5 class="mb-3">Analytics Report</h5>
          <div class="card">
            <div class="list-group list-group-flush">
              <a href="#"
                class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">Company
                Finance Growth<span class="h5 mb-0">+45.14%</span></a>
              <a href="#"
                class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">Company
                Expenses Ratio<span class="h5 mb-0">0.58%</span></a>
              <a href="#"
                class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">Business
                Risk Cases<span class="h5 mb-0">Low</span></a>
            </div>
            <div class="card-body px-2">
              <div id="analytics-report-chart"></div>
            </div>
          </div>
        </div>

        <div class="col-md-12 col-xl-8">
          <h5 class="mb-3">Sales Report</h5>
          <div class="card">
            <div class="card-body">
              <h6 class="mb-2 f-w-400 text-muted">This Week Statistics</h6>
              <h3 class="mb-0">$7,650</h3>
              <div id="sales-report-chart"></div>
            </div>
          </div>
        </div>
        <div class="col-md-12 col-xl-4">
          <h5 class="mb-3">Transaction History</h5>
          <div class="card">
            <div class="list-group list-group-flush">
              <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex">
                  <div class="flex-shrink-0">
                    <div class="avtar avtar-s rounded-circle text-success bg-light-success">
                      <i class="ti ti-gift f-18"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1">Order #002434</h6>
                    <p class="mb-0 text-muted">Today, 2:00 AM</P>
                  </div>
                  <div class="flex-shrink-0 text-end">
                    <h6 class="mb-1">+ $1,430</h6>
                    <p class="mb-0 text-muted">78%</P>
                  </div>
                </div>
              </a>
              <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex">
                  <div class="flex-shrink-0">
                    <div class="avtar avtar-s rounded-circle text-primary bg-light-primary">
                      <i class="ti ti-message-circle f-18"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1">Order #984947</h6>
                    <p class="mb-0 text-muted">5 August, 1:45 PM</P>
                  </div>
                  <div class="flex-shrink-0 text-end">
                    <h6 class="mb-1">- $302</h6>
                    <p class="mb-0 text-muted">8%</P>
                  </div>
                </div>
              </a>
              <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex">
                  <div class="flex-shrink-0">
                    <div class="avtar avtar-s rounded-circle text-danger bg-light-danger">
                      <i class="ti ti-settings f-18"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1">Order #988784</h6>
                    <p class="mb-0 text-muted">7 hours ago</P>
                  </div>
                  <div class="flex-shrink-0 text-end">
                    <h6 class="mb-1">- $682</h6>
                    <p class="mb-0 text-muted">16%</P>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
  <!-- [ Main Content ] end -->
@endsection --}}