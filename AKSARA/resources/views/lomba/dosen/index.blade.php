@extends('layouts.template') {{-- Sesuaikan dengan layout utama Anda --}}
@section('title', $breadcrumb->title ?? 'Informasi Lomba Terkini')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title mb-0">{{ $breadcrumb->title ?? 'Daftar Lomba' }}</h3>
                        {{-- <div class="alert alert-light-info bg-light-info text-info border-0 mt-2" role="alert">
                            <i class="fas fa-info-circle me-2"></i>Menampilkan daftar informasi lomba yang sudah
                            terverifikasi oleh admin.
                        </div> --}}
                    </div>
                    <div class="card-body">
                        {{-- <h3 class="card-title mb-0">{{ $breadcrumb->title ?? 'Daftar Lomba' }}</h3> --}}
                        <div class="alert alert-light-info bg-light-info text-info border-0 mt-0" role="alert">
                            <i class="fas fa-info-circle me-2"></i>Menampilkan daftar informasi lomba yang sudah
                            terverifikasi oleh admin.
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tingkat_filter_crud" class="form-label">Filter Tingkat Lomba:</label>
                                    <select class="form-select form-select-sm" id="tingkat_filter_crud">
                                        <option value="">- Semua Tingkat -</option>
                                        <option value="lokal">Lokal</option>
                                        <option value="nasional">Nasional</option>
                                        <option value="internasional">Internasional</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kategori_filter_crud" class="form-label">Filter Kategori Lomba:</label>
                                    <select class="form-select form-select-sm" id="kategori_filter_crud">
                                        <option value="">- Semua Kategori -</option>
                                        <option value="individu">Individu</option>
                                        <option value="kelompok">Kelompok</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover dt-responsive nowrap" id="dataLombaPublik"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 5%;">No.</th>
                                        <th>Nama Lomba</th>
                                        <th>Penyelenggara</th>
                                        <th>Tingkat</th>
                                        <th>Periode Pendaftaran</th>
                                        <th>Biaya</th>
                                        <th class="text-center" style="width: 5%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal untuk Detail Lomba (Publik) --}}
    <div class="modal fade" id="modalDetailLomba" tabindex="-1" aria-labelledby="modalDetailLombaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content"></div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        var dataLombaPublik;

        function modalActionLomba(url, title = 'Form', modalId = 'modalDetailLomba') {
            const targetModal = $(`#${modalId}`);
            const targetModalContent = targetModal.find('.modal-content');
            targetModalContent.html(
                '<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3 fs-5">Memuat...</p></div>'
                );
            const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));
            modalInstance.show();
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    targetModalContent.html(response);
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message ?? 'Gagal memuat konten.';
                    targetModalContent.html(
                        `<div class="modal-header"><h5 class="modal-title">${title}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p class="text-danger">${msg}</p></div>`
                        );
                }
            });
        }

        $(document).ready(function() {

            dataLombaPublik = $('#dataLombaPublik').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('lomba.dosen.list') }}", // Pastikan route ini benar
                    data: function (d) {
                        // Mengambil nilai dari filter, jika kosong, controller akan default ke 'disetujui'
                        d.tingkat_lomba_filter_crud = $('#tingkat_filter_crud').val();
                        d.kategori_lomba_filter_crud = $('#kategori_filter_crud').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_lomba',
                        name: 'nama_lomba'
                    },
                    {
                        data: 'penyelenggara',
                        name: 'penyelenggara'
                    },
                    {
                        data: 'tingkat',
                        name: 'tingkat',
                        render: function(data, type, row) { return data.charAt(0).toUpperCase() + data.slice(1); },
                        className: ''
                    },
                    {
                        data: 'periode_pendaftaran',
                        name: 'pembukaan_pendaftaran'
                    },
                    {
                        data: 'biaya_formatted',
                        name: 'biaya',
                        className: ''
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        className: '',
                        orderable: false,
                        searchable: false
                    }
                ],
            });
            
            $('#tingkat_filter_crud, #kategori_filter_crud').on('change', function () { 
                dataLombaPublik.ajax.reload(); 
            });
        });
    </script>
@endpush
