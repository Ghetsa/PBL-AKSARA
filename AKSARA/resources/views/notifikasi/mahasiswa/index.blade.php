@extends('layouts.template')

@section('title', $breadcrumb->title ?? 'Daftar Notifikasi')

@push('css')
<style>
    .notification-list .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: 1px solid #e9ecef;
        border-left-width: 4px;
    }
    .notification-list .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .notification-list .card.unread {
        border-left-color: #696cff; /* Primary color */
        background-color: #f7f7ff;
    }
    .notification-icon {
        font-size: 1.5rem;
        width: 40px;
        text-align: center;
    }
    .notification-actions .btn {
        margin-right: 5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Daftar Notifikasi</h4>
            {{-- Menggunakan route notifikasi Mahasiswa --}}
            <form action="{{ route('mahasiswa.notifikasi.markAllAsRead') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fas fa-check-double me-1"></i> Tandai Semua Dibaca</button>
            </form>
        </div>
        <div class="card-body">
            @if($allNotifications->isEmpty())
                <div class="alert alert-info text-center">
                    Tidak ada notifikasi saat ini.
                </div>
            @else
                <div class="notification-list">
                    @foreach ($allNotifications as $notif)
                        @php
                            $iconClass = 'fas fa-bell'; // Default
                            $iconColor = 'text-secondary';
                            if (Str::contains($notif->type, 'lomba')) {
                                $iconClass = 'fas fa-award';
                                $iconColor = 'text-warning';
                            } elseif (Str::contains($notif->type, 'prestasi')) {
                                $iconClass = 'fas fa-trophy';
                                $iconColor = 'text-success';
                            } elseif (Str::contains($notif->type, 'keahlian')) {
                                $iconClass = 'fas fa-star';
                                $iconColor = 'text-info';
                            }
                        @endphp
                        <div class="card mb-3 {{ $notif->status_baca == 'belum_dibaca' ? 'unread' : '' }}">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="notification-icon me-3 {{ $iconColor }}">
                                        <i class="{{ $iconClass }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">{{ $notif->judul }}</h6>
                                        <p class="mb-1 text-muted">{{ $notif->isi }}</p>
                                        <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="ms-3 notification-actions d-flex flex-nowrap">
                                        {{-- Menggunakan route notifikasi Mahasiswa --}}
                                        <button class="btn btn-sm btn-primary" 
                                                onclick="showNotificationDetail('{{ route('mahasiswa.notifikasi.show_and_read', ['id' => $notif->id, 'model' => $notif->type]) }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        @if($notif->status_baca == 'belum_dibaca')
                                            {{-- Menggunakan route notifikasi Mahasiswa --}}
                                            <form action="{{ route('mahasiswa.notifikasi.mark_as_read', ['id' => $notif->id, 'model' => $notif->type]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Tandai Dibaca"><i class="fas fa-check"></i></button>
                                            </form>
                                        @endif
                                        
                                        {{-- Menggunakan route notifikasi Mahasiswa --}}
                                        <form action="{{ route('mahasiswa.notifikasi.destroy', ['id' => $notif->id, 'model' => $notif->type]) }}" method="POST" class="form-hapus">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $allNotifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Container untuk Detail Notifikasi --}}
<div class="modal fade" id="notificationDetailModal" tabindex="-1" role="dialog" aria-labelledby="notificationDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {{-- Konten dari show.blade.php akan dimuat di sini --}}
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Fungsi untuk menampilkan detail notifikasi di modal
    function showNotificationDetail(url) {
        const modal = $('#notificationDetailModal');
        modal.find('.modal-content').html(`<div class="modal-body text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>`);
        modal.modal('show');
        $.get(url, function(res) {
            modal.find('.modal-content').html(res);
        }).fail(function() {
            modal.find('.modal-content').html(`<div class="modal-header"><h5 class="modal-title text-danger">Error</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p>Gagal memuat detail notifikasi.</p></div>`);
        });
    }

    // Konfirmasi penghapusan
    document.addEventListener('DOMContentLoaded', function () {
        $('.form-hapus').on('submit', function (event) {
            event.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Anda yakin?',
                text: "Notifikasi yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush