@extends('layouts.template')

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

    /* CSS untuk efek shadow dan hover */
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
    /* Akhir dari CSS shadow */

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
        <h4 class="mb-0">Notifikasi Anda</h4>
        @if ($unreadCount > 0)
            <form action="{{ route('notifikasi.markAllAsRead') }}" method="POST" onsubmit="return confirm('Anda yakin ingin menandai semua notifikasi sebagai telah dibaca?');">
                @csrf
                <button type="submit" class="btn btn-outline-success btn-sm">
                    <i class='bx bx-check-double'></i> Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    <div class="card">
        <div class="card-header pb-0">
            {{-- Navigasi Filter --}}
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link @if($filter === 'semua') active @endif" href="{{ route('notifikasi.index', ['filter' => 'semua']) }}">Semua</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($filter === 'belum_dibaca') active @endif" href="{{ route('notifikasi.index', ['filter' => 'belum_dibaca']) }}">Belum Dibaca</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($filter === 'sudah_dibaca') active @endif" href="{{ route('notifikasi.index', ['filter' => 'sudah_dibaca']) }}">Sudah Dibaca</a>
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
                    Tidak ada notifikasi untuk ditampilkan pada kategori ini.
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
                                <a href="{{ route('notifikasi.show_and_read', ['id' => $notif->id, 'model' => $notif->type]) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                
                                @if($notif->status_baca == 'belum_dibaca')
                                <a href="{{ route('notifikasi.show_and_read', ['id' => $notif->id, 'model' => $notif->type]) }}" class="btn btn-sm btn-outline-success" title="Tandai Dibaca">Tandai Dibaca</a>
                                @endif
                                
                                <form action="{{ route('notifikasi.destroy', ['id' => $notif->id, 'model' => $notif->type]) }}" method="POST" class="form-hapus">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
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
@endpush