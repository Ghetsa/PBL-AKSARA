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
            <form action="{{ route('admin.notifikasi.markAllAsRead') }}" method="POST">
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
                                        {{-- Tombol Detail yang akan membuka modal --}}
                                        <button class="btn btn-sm btn-primary" 
                                                onclick="showNotificationDetail('{{ route('admin.notifikasi.show_and_read', ['id' => $notif->id, 'model' => $notif->type]) }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        @if($notif->status_baca == 'belum_dibaca')
                                            <form action="{{ route('admin.notifikasi.mark_as_read', ['id' => $notif->id, 'model' => $notif->type]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Tandai Dibaca"><i class="fas fa-check"></i></button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('admin.notifikasi.destroy', ['id' => $notif->id, 'model' => $notif->type]) }}" method="POST" class="form-hapus">
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
        
        // Tampilkan loading spinner
        modal.find('.modal-content').html(`
            <div class="modal-body text-center p-5">
                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
            </div>
        `);
        modal.modal('show');

        // Ambil konten via AJAX
        $.get(url, function(res) {
            modal.find('.modal-content').html(res);
        }).fail(function() {
            modal.find('.modal-content').html(`
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"><p>Gagal memuat detail notifikasi.</p></div>
            `);
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

{{-- @extends('layouts.template')

@section('title', $breadcrumb->title ?? 'Daftar Notifikasi')

@push('styles')
<style>
    .nav-tabs .nav-link {
        border-radius: 0.25rem;
        margin-right: 5px;
    }
    .nav-tabs .nav-link.active {
        color: #fff;
        background-color: #696cff;
        border-color: #696cff;
    }
    .notification-item {
        border: 1px solid #ebeef4;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        background-color: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); 
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .notification-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }
    .notification-item.unread {
        border-left: 4px solid #696cff;
    }
    .notification-title {
        font-weight: 600;
        color: #333;
    }
    .notification-title .badge {
        font-size: 0.7rem;
        vertical-align: middle;
        margin-left: 8px;
    }
</style>
@endpush

@section('content')
<div class="page-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Daftar Notifikasi</h4>
        @if ($unreadCount > 0)
            <form action="{{ route('admin.notifikasi.markAllAsRead') }}" method="POST" onsubmit="return confirm('Anda yakin ingin menandai semua notifikasi sebagai telah dibaca?');">
                @csrf
                <button type="submit" class="btn btn-outline-success btn-sm">
                    <i class='bx bx-check-double'></i> Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    <div class="card">
        <div class="card-header pb-0">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link @if($filter === 'semua') active @endif" href="{{ route('admin.notifikasi.index', ['filter' => 'semua']) }}">Semua</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($filter === 'belum_dibaca') active @endif" href="{{ route('admin.notifikasi.index', ['filter' => 'belum_dibaca']) }}">Belum Dibaca</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($filter === 'sudah_dibaca') active @endif" href="{{ route('admin.notifikasi.index', ['filter' => 'sudah_dibaca']) }}">Sudah Dibaca</a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($allNotifications->isEmpty())
                <div class="alert alert-info text-center mt-3">
                    Tidak ada notifikasi.
                </div>
            @else
                <div class="mt-3">
                    @foreach($allNotifications as $notif)
                        <div class="notification-item d-flex justify-content-between align-items-center @if($notif->status_baca == 'belum_dibaca') unread @endif">
                            <div>
                                <h6 class="notification-title mb-1">
                                    {{ $notif->judul }}
                                    @if($notif->status_baca == 'belum_dibaca')
                                        <span class="badge bg-warning">Baru</span>
                                    @endif
                                </h6>
                                <p class="mb-1 text-muted" style="font-size: 0.9em;">{{ Str::limit($notif->isi, 100) }}</p>
                                <small class="text-muted" style="font-size: 0.8em;">
                                    <i class="bx bx-time-five"></i> {{ optional($notif->created_at)->diffForHumans() }}
                                </small>
                            </div>
                            <div class="actions d-flex">
                                <a href="{{ route('admin.notifikasi.show_and_read', ['id' => $notif->id, 'model' => $notif->type]) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                
                                @if($notif->status_baca == 'belum_dibaca')
                                <a href="{{ route('admin.notifikasi.show_and_read', ['id' => $notif->id, 'model' => $notif->type]) }}" class="btn btn-sm btn-outline-success" title="Tandai Dibaca">Tandai Dibaca</a>
                                @endif
                                
                                <form action="{{ route('admin.notifikasi.destroy', ['id' => $notif->id, 'model' => $notif->type]) }}" method="POST" class="form-hapus">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">{{ $allNotifications->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.form-hapus');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            if (confirm('Anda yakin ingin menghapus notifikasi ini?')) {
                form.submit();
            }
        });
    });
});
</script>
@endpush --}}