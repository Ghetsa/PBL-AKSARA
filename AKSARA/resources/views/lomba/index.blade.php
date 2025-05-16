@extends('layouts.template')
@section('title', 'Daftar Lomba')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Lomba</h3>
                </div>
                <div class="card-body">
                    {{-- Flash messages akan ditampilkan oleh SweetAlert --}}
                    <form method="GET" id="filterFormAdminLomba" class="row g-3 mb-3 align-items-center">
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" id="search_nama_admin" name="search_nama" placeholder="Cari Nama Prestasi/Mahasiswa/NIM..." value="{{ request('search_nama') }}">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" id="filter_status_admin" name="filter_status">
                                <option value="">-- Semua Status --</option>
                                <option value="pending" {{ request('filter_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="disetujui" {{ request('filter_status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ request('filter_status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                        </div>
                         <div class="col-md-2">
                            <a href="{{ route('lomba.index') }}" class="btn btn-secondary btn-sm w-100">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataDaftarPrestasiAdmin" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th>Nama Lomba</th>
                                    <th>Kategori</th>
                                    <th>Bidang Keahlian</th>
                                    <th>Pembukaan Pendaftaran</th>
                                    <th>Penutupan Pendaftaran</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DataTable akan mengisi ini --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Umum untuk form AJAX (pastikan ID ini unik atau gunakan yang sudah ada di layout) --}}
<div class="modal fade" id="myModalAdmin" tabindex="-1" role="dialog" aria-labelledby="myModalAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {{-- Konten modal akan dimuat di sini --}}
        </div>
    </div>
</div>
@endsection

@push('js')
{{-- Dependensi JS yang diperlukan --}}
<script>
    var dataDaftarLombaAdmin;

    function modalAction(url, modalId = 'myModalAdmin') { // Default ke myModalAdmin
        const targetModalContent = $(`#${modalId} .modal-content`);
        targetModalContent.html(''); // Kosongkan dulu
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                targetModalContent.html(response);
                const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));
                modalInstance.show();
            },
            error: function (xhr) {
                let errorMessage = 'Gagal memuat konten modal.';
                if(xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
                Swal.fire({ icon: 'error', title: 'Error', text: errorMessage });
            }
        });
    }

    $(document).ready(function() {
        dataDaftarLombaAdmin = $('#dataDaftarLombaAdmin').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('prestasi.admin.list') }}",
                data: function (d) { // Mengirim data filter
                    d.search_nama = $('#search_nama_admin').val();
                    d.filter_status = $('#filter_status_admin').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_lomba', name: 'nama_lomba' }, // Untuk searching di server-side
                { data: 'kategori', name: 'kategori' },       // Untuk searching di server-side
                { data: 'bidang_keahlian', name: 'bidang_keahlian' },
                { data: 'pembukaan_pendaftaran', name: 'pembukaan_pendaftaran' },
                { data: 'batas_pendaftaran', name: 'batas_pendaftaran' },
                { data: 'status_verifikasi', name: 'status_verifikasi', className: 'text-center' },
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" }
        });

        // Submit form filter akan me-reload datatable
        $('#filterFormAdminLomba').on('submit', function(e) {
            e.preventDefault();
            dataDaftarLombaAdmin.ajax.reload();
        });
    });
</script>
@endpush