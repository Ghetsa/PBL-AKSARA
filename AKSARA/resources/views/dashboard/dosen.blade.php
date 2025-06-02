@extends('layouts.template')

@section('title', $breadcrumb->title ?? 'Dashboard Dosen')

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
                                Selamat Datang!</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->nama }}</div>
                            <div class="text-xs text-muted mt-1">NIP: {{ $dosen->nip ?? '-'}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Mahasiswa Bimbingan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahMahasiswaBimbingan }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Prestasi Mhs. Bimbingan (Disetujui)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahPrestasiBimbingan }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-award fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Prestasi Terbaru Mahasiswa Bimbingan (Top 5)</h6>
                </div>
                <div class="card-body">
                    @if($prestasiMahasiswaBimbingan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>Nama Prestasi</th>
                                        <th>Tingkat</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prestasiMahasiswaBimbingan as $key => $prestasi)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $prestasi->mahasiswa->user->nama ?? 'N/A' }} <br><small class="text-muted">NIM: {{ $prestasi->mahasiswa->nim ?? 'N/A' }}</small></td>
                                        <td>{{ $prestasi->nama_prestasi }}</td>
                                        <td>{{ ucfirst($prestasi->tingkat_prestasi) }}</td>
                                        <td>{{ Carbon\Carbon::parse($prestasi->tanggal_pelaksanaan_prestasi)->isoFormat('D MMM YYYY') }}</td>
                                        <td>{!! $prestasi->status_verifikasi_badge !!}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                         <div class="text-center mt-3">
                            <a href="{{-- {{ route('dosen.prestasi.bimbingan.index') }} --}}" class="btn btn-sm btn-primary">Lihat Semua Prestasi Bimbingan</a>
                        </div>
                    @else
                        <p class="text-center text-muted">Belum ada prestasi dari mahasiswa bimbingan Anda.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-2">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pengajuan Info Lomba dari Mahasiswa Bimbingan (Top 5)</h6>
                </div>
                <div class="card-body">
                    @if($lombaDiajukanBimbingan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Lomba</th>
                                        <th>Diajukan Oleh</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Status Verifikasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lombaDiajukanBimbingan as $key => $lomba)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $lomba->nama_lomba }}</td>
                                        <td>{{ $lomba->inputBy->nama ?? 'N/A' }}</td>
                                        <td>{{ $lomba->created_at->isoFormat('D MMM YYYY, HH:mm') }}</td>
                                        <td>{!! $lomba->status_verifikasi_badge !!}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                         <div class="text-center mt-3">
                            <a href="{{-- {{ route('dosen.lomba.bimbingan.index') }} --}}" class="btn btn-sm btn-primary">Lihat Semua Pengajuan Lomba Bimbingan</a>
                        </div>
                    @else
                        <p class="text-center text-muted">Belum ada pengajuan lomba dari mahasiswa bimbingan Anda.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')
{{-- Jika ada JS spesifik untuk halaman ini --}}
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