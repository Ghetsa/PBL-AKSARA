@extends('layouts.template')
@section('title', 'Histori Prestasi Saya')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Histori Prestasi Saya</h3>
                    {{-- Tombol Tambah memanggil modalAction dengan route create_ajax --}}
                    <button type="button" class="btn btn-primary btn-sm"
                            onclick="modalAction('{{ route('prestasi.mahasiswa.create_ajax') }}')">
                        <i class="fas fa-plus"></i> Tambah Prestasi Baru (AJAX)
                    </button>
                </div>
                <div class="card-body">
                    {{-- Flash messages akan ditampilkan oleh SweetAlert dari AJAX response --}}

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataPrestasiMahasiswa" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">No.</th>
                                    <th>Nama Prestasi/Kegiatan</th>
                                    <th>Kategori</th>
                                    <th>Tingkat</th>
                                    <th>Tahun</th>
                                    <th class="text-center">Status Verifikasi</th>
                                    <th class="text-center">Bukti</th>
                                    <th class="text-center" style="width: 10%;">Aksi</th>
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
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> {{-- modal-lg untuk form yang lebih lebar --}}
        <div class="modal-content">
            {{-- Konten modal akan dimuat di sini oleh modalAction() --}}
        </div>
    </div>
</div>
@endsection

@push('js')
{{-- Pastikan jQuery, Bootstrap JS, DataTables, jQuery Validation, SweetAlert2 sudah dimuat di layout utama --}}
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"> --}}
{{-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> --}}
{{-- <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script> --}}

<script>
    // Variabel global untuk DataTable agar bisa di-reload dari script modal
    var dataPrestasiMahasiswa;

    // Fungsi untuk memuat konten AJAX ke dalam modal
    function modalAction(url, modalTitle = 'Form Aksi') {
        $('#myModal .modal-content').html(''); // Kosongkan dulu
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#myModal .modal-content').html(response);
                // Bootstrap 5: new bootstrap.Modal($('#myModal')).show();
                // Bootstrap 4: $('#myModal').modal('show');
                // Coba cara ini untuk Bootstrap 5 jika yang di atas tidak jalan atau sudah ada instance
                 const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById('myModal'));
                 modalInstance.show();
            },
            error: function (xhr) {
                let errorMessage = 'Gagal memuat konten modal.';
                if(xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
                Swal.fire({ icon: 'error', title: 'Error', text: errorMessage });
            }
        });
    }

    // Fungsi untuk konfirmasi hapus (jika akan diimplementasikan)
    // function deleteConfirmAjax(id, title) { ... }

    $(document).ready(function() {
        dataPrestasiMahasiswa = $('#dataPrestasiMahasiswa').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('prestasi.mahasiswa.list') }}", // Route ke method listMahasiswa
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_prestasi', name: 'nama_prestasi' },
                { data: 'kategori', name: 'kategori' },
                { data: 'tingkat', name: 'tingkat' },
                { data: 'tahun', name: 'tahun' },
                { data: 'status_verifikasi', name: 'status_verifikasi', className: 'text-center' },
                { data: 'file_bukti_action', name: 'file_bukti_action', className: 'text-center', orderable: false, searchable: false },
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" } // Bahasa Indonesia
        });

        // Event listener untuk menutup modal (jika modal Bootstrap 5)
        // Berguna untuk mereset state atau validasi jika diperlukan, tapi sudah ada di script form
        // var myModalEl = document.getElementById('myModal');
        // myModalEl.addEventListener('hidden.bs.modal', function (event) {
        //     // Kosongkan konten modal setelah ditutup agar tidak ada sisa form lama
        //     $('#myModal .modal-content').html('');
        // });
    });
</script>
@endpush