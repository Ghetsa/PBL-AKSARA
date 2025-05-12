@extends('layouts.template')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Card Profil -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Data Profil</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('mantis/src/assets/images/avatar-1.jpg') }}"
                            class="img-circle elevation-2" alt="Foto Profil" style="max-width:150px;">
                    </div>
                    <p><strong>Nama:</strong> {{ $user->nama }}</p>
                    <p><strong>Username:</strong> {{ $user->username }}</p>
                    <!-- Tombol untuk membuka modal edit profil -->
                    <button id="btnEditProfile" class="btn btn-warning">Ubah Profil</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal untuk Update Profile -->
    <div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog" aria-hidden="true"></div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#updateProfileModal').load(url, function() {
                $('#updateProfileModal').modal('show');
            });
        }

        $(document).ready(function() {
            // Saat tombol "Ubah Profil" diklik, muat form update secara AJAX ke dalam modal
            $('#btnEditProfile').click(function() {
                modalAction("{{ url('user/profile_ajax') }}"); // Panggil modalAction dengan URL AJAX
            });
        });
    </script>
@endpush