@extends('layouts.template') {{-- Sesuaikan dengan layout utama Anda --}}
@section('title', $breadcrumb->title ?? 'Informasi Lomba Terkini')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="alert alert-light-info bg-light-info text-info border-0" role="alert">
                            <i class="fas fa-info-circle me-2"></i>Menampilkan daftar informasi lomba yang sudah
                            terverifikasi oleh admin.
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
                                        <th class="text-center" style="width: 10%;">Aksi</th>
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

            $('#dataLombaPublik').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('lomba.dosen.list') }}",
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
                        className: 'text-center'
                    },
                    {
                        data: 'periode_pendaftaran',
                        name: 'pembukaan_pendaftaran'
                    },
                    {
                        data: 'biaya_formatted',
                        name: 'biaya',
                        className: 'text-end'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                }
            });
        });
    </script>
@endpush
