@extends('layouts.template')

@section('title', $breadcrumb->title ?? 'Dashboard Admin')

@push('css')
    {{-- CSS untuk Chart.js jika diperlukan --}}
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ $breadcrumb->title }}</h1>
        </div>

        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Lomba (Semua)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLomba ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Lomba Aktif
                                    (Disetujui)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lombaAktif ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pengajuan Lomba
                                    Pending</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lombaPengajuanPending ?? 0 }}</div>
                                <a href="{{ route('admin.lomba.verifikasi.index') }}" class="stretched-link"></a>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengguna</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUser ?? 0 }}</div>
                                <div class="text-xs mt-1">Mhs: {{ $totalMahasiswa ?? 0 }} | Dosen: {{ $totalDosen ?? 0 }}
                                </div>
                                <a href="{{ route('user.index') }}" class="stretched-link"></a>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Prestasi (Semua)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPrestasi ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-trophy fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Prestasi Disetujui
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $prestasiDisetujui ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-award fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Prestasi Pending
                                    Verifikasi</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $prestasiPending ?? 0 }}</div>
                                <a href="{{ route('prestasi.admin.index') }}?status_verifikasi=pending"
                                    class="stretched-link"></a>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        {{-- Judul Diperbaiki --}}
                        <h6 class="m-0 font-weight-bold text-primary">Distribusi Lomba berdasarkan Tingkat</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="lombaByTingkatChart" width="300" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        {{-- Judul Diperbaiki --}}
                        <h6 class="m-0 font-weight-bold text-primary">Distribusi Prestasi berdasarkan Tingkat</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="prestasiByTingkatChart" width="300" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRAFIK BARU DITAMBAHKAN DI SINI --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tren Perolehan Prestasi (12 Bulan Terakhir)</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area" style="height: 320px;">
                            <canvas id="prestasiPerBulanChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Grafik Lomba Berdasarkan Tingkat
            const ctxLomba = document.getElementById("lombaByTingkatChart");
            if (ctxLomba) {
                new Chart(ctxLomba, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($lombaByTingkat->keys()) !!},
                        datasets: [{
                            data: {!! json_encode($lombaByTingkat->values()) !!},
                            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return context.label + ": " + context.parsed;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Grafik Prestasi Berdasarkan Tingkat
            const ctxPrestasi = document.getElementById("prestasiByTingkatChart");
            if (ctxPrestasi) {
                new Chart(ctxPrestasi, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($prestasiByTingkat->keys()) !!},
                        datasets: [{
                            data: {!! json_encode($prestasiByTingkat->values()) !!},
                            backgroundColor: ['#1cc88a', '#36b9cc', '#4e73df', '#f6c23e', '#e74a3b'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return context.label + ": " + context.parsed;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const labels = @json(array_values($labelsBulan));
            const data = @json(array_values($dataBulan));
            const ctx = document.getElementById("prestasiPerBulanChart");

            if (ctx && data.length > 0 && data.some(d => d > 0)) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Jumlah Prestasi Disetujui",
                            data: data,
                            borderColor: "#4e73df",
                            backgroundColor: "rgba(78, 115, 223, 0.05)",
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: "#4e73df",
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: {
                            x: { grid: { display: false } },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: "rgba(234, 236, 244, 1)"
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: "white",
                                titleColor: "#6e707e",
                                bodyColor: "#858796",
                                borderColor: "#dddfeb",
                                borderWidth: 1
                            }
                        }
                    }
                });
            } else {
                ctx.parentNode.innerHTML = '<div class="text-center py-4 text-gray-500">Tidak ada data prestasi dalam 12 bulan terakhir.</div>';
            }
        });
    </script>
@endpush


{{-- @extends('layouts.template')

@section('content')
<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Users</h6>
                <h4 class="mb-0">78,250 <span class="badge bg-light-primary border border-primary"><i
                            class="ti ti-trending-up"></i> 70.5%</span></h4>
            </div>
            <div id="total-value-graph-1"></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Order</h6>
                <h4 class="mb-0">18,800 <span class="badge bg-light-warning border border-warning"><i
                            class="ti ti-trending-down"></i> 27.4%</span></h4>
            </div>
            <div id="total-value-graph-2"></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Sales</h6>
                <h4 class="mb-0">$35,078 <span class="badge bg-light-warning border border-warning"><i
                            class="ti ti-trending-down"></i> 27.4%</span></h4>
            </div>
            <div id="total-value-graph-3"></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Marketing</h6>
                <h4 class="mb-0">4,42,236 <span class="badge bg-light-primary border border-primary"><i
                            class="ti ti-trending-up"></i> 59.3%</span></h4>
            </div>
            <div id="total-value-graph-4"></div>
        </div>
    </div>

    <div class="col-md-12 col-xl-8">
        <h5 class="mb-3">Income Overview</h5>
        <div class="card">
            <div class="card-body">
                <div class="row mb-3 align-items-center">
                    <div class="col">
                        <p class="mb-1 text-danger">$1,12,900 (45.67%)</p>
                        <p class="mb-1 text-muted">Compare to : 01 Dec 2021-08 Jan 2022</p>
                    </div>
                    <div class="col-auto">
                        <ul class="nav nav-pills justify-content-end mb-0" id="income-tab-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="income-tab-profile-tab" data-bs-toggle="pill"
                                    data-bs-target="#income-tab-profile" type="button" role="tab"
                                    aria-controls="income-tab-profile" aria-selected="false">Week</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="income-tab-home-tab" data-bs-toggle="pill"
                                    data-bs-target="#income-tab-home" type="button" role="tab"
                                    aria-controls="income-tab-home" aria-selected="true">Month</button>
                            </li>
                        </ul>
                    </div>
                    <div class="col-auto">
                        <select class="form-select">
                            <option>By Volume</option>
                            <option>By Margin</option>
                            <option>By Sales</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <a href="#" class="avtar avtar-s btn btn-outline-secondary">
                            <i class="ti ti-download f-18"></i>
                        </a>
                    </div>
                </div>
                <div class="tab-content" id="income-tab-tabContent">
                    <div class="tab-pane show active" id="income-tab-profile" role="tabpanel"
                        aria-labelledby="income-tab-profile-tab" tabindex="0">
                        <div id="income-overview-tab-chart"></div>
                    </div>
                    <div class="tab-pane" id="income-tab-home" role="tabpanel" aria-labelledby="income-tab-home-tab"
                        tabindex="0">
                        <div id="income-overview-tab-chart-1"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-xl-4">
        <h5 class="mb-3">Page Views by Page Title</h5>
        <div class="card">
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex">
                        <div class="flex-grow-1 me-2">
                            <h6 class="mb-1">Admin Home</h6>
                            <p class="mb-0 text-muted">/demo/admin/index.html</P>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <h5 class="mb-1 text-primary">7755</h5>
                            <p class="mb-0 text-muted">31.74%</P>
                        </div>
                    </div>
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex">
                        <div class="flex-grow-1 me-2">
                            <h6 class="mb-1">Form Elements</h6>
                            <p class="mb-0 text-muted">/demo/admin/forms.html</P>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <h5 class="mb-1 text-primary">5215</h5>
                            <p class="mb-0 text-muted">28.53%</P>
                        </div>
                    </div>
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex">
                        <div class="flex-grow-1 me-2">
                            <h6 class="mb-1">Utilities</h6>
                            <p class="mb-0 text-muted">/demo/admin/util.html</P>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <h5 class="mb-1 text-primary">4848</h5>
                            <p class="mb-0 text-muted">25.35%</P>
                        </div>
                    </div>
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex">
                        <div class="flex-grow-1 me-2">
                            <h6 class="mb-1">Form Validation</h6>
                            <p class="mb-0 text-muted">/demo/admin/validation.html</P>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <h5 class="mb-1 text-primary">3275</h5>
                            <p class="mb-0 text-muted">23.17%</P>
                        </div>
                    </div>
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex">
                        <div class="flex-grow-1 me-2">
                            <h6 class="mb-1">Modals</h6>
                            <p class="mb-0 text-muted">/demo/admin/modals.html</P>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <h5 class="mb-1 text-primary">3003</h5>
                            <p class="mb-0 text-muted">22.21%</P>
                        </div>
                    </div>
                </a>
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
                                <th class="text-end">TOTAL ORDER</th>
                                <th>STATUS</th>
                                <th class="text-end">TOTAL AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><a href="#" class="text-muted">84564564</a></td>
                                <td>Camera Lens</td>
                                <td class="text-end">40</td>
                                <td><span class="px-4 d-block"><i
                                            class="fas fa-circle text-danger f-10 m-r-5"></i>Rejected</span>
                                </td>
                                <td class="text-end">$40,570</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-muted">84564564</a></td>
                                <td>Laptop</td>
                                <td class="text-end">300</td>
                                <td><span class="px-4 d-block"><i
                                            class="fas fa-circle text-warning f-10 m-r-5"></i>Pending</span>
                                </td>
                                <td class="text-end">$180,139</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-muted">84564564</a></td>
                                <td>Mobile</td>
                                <td class="text-end">355</td>
                                <td><span class="px-4 d-block"><i
                                            class="fas fa-circle text-success f-10 m-r-5"></i>Approved</span></td>
                                <td class="text-end">$180,139</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-muted">84564564</a></td>
                                <td>Camera Lens</td>
                                <td class="text-end">40</td>
                                <td><span class="px-4 d-block"><i
                                            class="fas fa-circle text-danger f-10 m-r-5"></i>Rejected</span>
                                </td>
                                <td class="text-end">$40,570</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-muted">84564564</a></td>
                                <td>Laptop</td>
                                <td class="text-end">300</td>
                                <td><span class="px-4 d-block"><i
                                            class="fas fa-circle text-warning f-10 m-r-5"></i>Pending</span>
                                </td>
                                <td class="text-end">$180,139</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-muted">84564564</a></td>
                                <td>Mobile</td>
                                <td class="text-end">355</td>
                                <td><span class="px-4 d-block"><i
                                            class="fas fa-circle text-success f-10 m-r-5"></i>Approved</span></td>
                                <td class="text-end">$180,139</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-muted">84564564</a></td>
                                <td>Camera Lens</td>
                                <td class="text-end">40</td>
                                <td><span class="px-4 d-block"><i
                                            class="fas fa-circle text-danger f-10 m-r-5"></i>Rejected</span>
                                </td>
                                <td class="text-end">$40,570</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-muted">84564564</a></td>
                                <td>Laptop</td>
                                <td class="text-end">300</td>
                                <td><span class="px-4 d-block"><i
                                            class="fas fa-circle text-warning f-10 m-r-5"></i>Pending</span>
                                </td>
                                <td class="text-end">$180,139</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-muted">84564564</a></td>
                                <td>Mobile</td>
                                <td class="text-end">355</td>
                                <td><span class="px-4 d-block"><i
                                            class="fas fa-circle text-success f-10 m-r-5"></i>Approved</span></td>
                                <td class="text-end">$180,139</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-muted">84564564</a></td>
                                <td>Mobile</td>
                                <td class="text-end">355</td>
                                <td><span class="px-4 d-block"><i
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
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-between">
                    <div class="col-auto">
                        <h5>Help & Support Chat</h5>
                        <p class="mb-3">Typical replay within 5 min</p>
                    </div>
                    <div class="col-auto">
                        <div class="user-group mb-3">
                            <img src="mantis/dist/assets/images/user/avatar-1.jpg" alt="image">
                            <img src="mantis/dist/assets/images/user/avatar-2.jpg" alt="image">
                            <img src="mantis/dist/assets/images/user/avatar-3.jpg" alt="image">
                        </div>
                    </div>
                </div>
                <div class="d-grid">
                    <div class="btn btn-primary">Need Help?</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-xl-8">
        <div class="card">
            <div class="row g-0">
                <div class="col-md-6">
                    <div class="card-body bg-primary position-relative h-100">
                        <h6 class="text-white">What would you want to learn today</h6>
                        <p class="text-white text-sm mb-4">Your learning capacity is 80% as daily analytics</p>
                        <div class="row align-items-end">
                            <div class="col-7">
                                <h4 class="text-white">35% Completed</h4>
                                <div class="progress bg-light-success">
                                    <div class="progress-bar bg-success" style="width: 40%"></div>
                                </div>
                            </div>
                            <div class="col-5">
                                <img src="mantis/dist/assets/images/widget/reader.svg" alt="img"
                                    class="img-fluid img-reader">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-body">
                        <p class="mb-2">Get started with new basic skills</p>
                        <p class="text-muted mb-3">Last Date 5th Nov 2020</p>
                        <hr class="my-3">
                        <div class="d-flex align-items-center justify-content-between my-3">
                            <div class="user-group">
                                <img src="mantis/dist/assets/images/user/avatar-1.jpg" alt="image">
                                <img src="mantis/dist/assets/images/user/avatar-2.jpg" alt="image">
                                <img src="mantis/dist/assets/images/user/avatar-3.jpg" alt="image">
                            </div>
                            <a href="#" class="avtar avtar-xs btn btn-primary">
                                <i class="ti ti-plus f-18"></i>
                            </a>
                        </div>
                        <p class="text-muted text-sm mb-0">Chrome fixed the bug several versions ago, thus rendering
                            this... </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center my-2">
                    <div class="col-5">
                        <p class="mb-0">Published Project</p>
                    </div>
                    <div class="col">
                        <div class="progress progress-primary">
                            <div class="progress-bar" style="width: 30%"></div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <p class="mb-0 text-sm text-muted">30%</p>
                    </div>
                </div>
                <div class="row align-items-center my-2">
                    <div class="col-5">
                        <p class="mb-0">Completed Task</p>
                    </div>
                    <div class="col">
                        <div class="progress progress-success">
                            <div class="progress-bar" style="width: 90%"></div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <p class="mb-0 text-sm text-muted">90%</p>
                    </div>
                </div>
                <div class="row align-items-center my-2">
                    <div class="col-5">
                        <p class="mb-0">Pending Task</p>
                    </div>
                    <div class="col">
                        <div class="progress progress-danger">
                            <div class="progress-bar" style="width: 50%"></div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <p class="mb-0 text-sm text-muted">50%</p>
                    </div>
                </div>
                <div class="row align-items-center my-2">
                    <div class="col-5">
                        <p class="mb-0">Issues</p>
                    </div>
                    <div class="col">
                        <div class="progress progress-warning">
                            <div class="progress-bar" style="width: 55%"></div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <p class="mb-0 text-sm text-muted">55%</p>
                    </div>
                </div>
                <hr class="my-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <img src="mantis/dist/assets/images/widget/target.svg" alt="img" class="img-fluid">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6>Income Salaries & Budget</h6>
                        <p class="text-muted mb-0">All your income salaries and budget comes here, you can track them or
                            manage them</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-2">Acquisition Channels</h6>
                        <p class="mb-0 text-muted">Marketing</P>
                    </div>
                    <h4 class="text-primary">-128</h4>
                </div>
                <div id="acquisition-chart"></div>
            </div>
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s rounded-circle text-secondary bg-light-secondary">
                                <i class="ti ti-device-analytics f-18"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Top Channels</h6>
                            <p class="mb-0 text-muted">Today, 2:00 AM</P>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <h6 class="mb-1">+ $1,430</h6>
                            <p class="mb-0 text-muted">35%</P>
                        </div>
                    </div>
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s rounded-circle text-primary bg-light-primary">
                                <i class="ti ti-file-text f-18"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Top Pages</h6>
                            <p class="mb-0 text-muted">Today 6:00 AM</P>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <h6 class="mb-1">- $1430</h6>
                            <p class="mb-0 text-muted">35%</P>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <!-- [ sample-page ] end -->
</div>
<!-- [ Main Content ] end -->
@endsection --}}