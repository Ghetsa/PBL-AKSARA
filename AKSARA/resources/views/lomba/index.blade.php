@extends('layouts.template')
@section('title', $breadcrumb->title ?? 'Informasi Lomba')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">{{ $breadcrumb->title ?? 'Informasi Lomba Terverifikasi' }}</h3>
                    {{-- Tombol Ajukan Lomba hanya untuk Mahasiswa dan Dosen --}}
                    @if(in_array(Auth::user()->role, ['mahasiswa', 'dosen']))
                        <button class="btn btn-sm btn-success" onclick="modalActionLomba('{{ route('lomba.user.create_form') }}', 'Ajukan Info Lomba Baru', 'modalFormLombaUser')">
                            <i class="fas fa-plus-circle me-1"></i> Ajukan Info Lomba
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="alert alert-light-info bg-light-info text-info border-0" role="alert">
                        <i class="fas fa-info-circle me-2"></i>Menampilkan daftar informasi lomba yang sudah terverifikasi oleh admin.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover dt-responsive nowrap" id="dataLombaPublik" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">No.</th>
                                    <th>Nama Lomba & Poster</th>
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
<div class="modal fade" id="modalDetailLombaPublik" tabindex="-1" aria-labelledby="modalDetailLombaPublikLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div>

{{-- Modal untuk Form Pengajuan Lomba oleh Mahasiswa/Dosen --}}
@if(in_array(Auth::user()->role, ['mahasiswa', 'dosen']))
<div class="modal fade" id="modalFormLombaUser" tabindex="-1" aria-labelledby="modalFormLombaUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div>
@endif
@endsection

@push('js')
<script>
    function modalActionLomba(url, title = 'Form', modalId = 'modalDetailLombaPublik') {
        // ... (kode JS modalActionLomba Anda yang sudah ada, pastikan modalId benar)
        const targetModal = $(`#${modalId}`);
        const targetModalContent = targetModal.find('.modal-content');
        
        targetModalContent.html('<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3 fs-5">Memuat...</p></div>');
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

    $(document).ready(function () {
        $('#dataLombaPublik').DataTable({
            processing: true, serverSide: true, responsive: true,
            ajax: "{{ route('lomba.list.publik') }}", // Route baru untuk data publik
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_lomba', name: 'nama_lomba' },
                { data: 'penyelenggara', name: 'penyelenggara' },
                { data: 'tingkat', name: 'tingkat' },
                { data: 'periode_pendaftaran', name: 'pembukaan_pendaftaran' }, // Sorting berdasarkan pembukaan
                { data: 'biaya_formatted', name: 'biaya' },
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" }
        });
    });
</script>
@endpush