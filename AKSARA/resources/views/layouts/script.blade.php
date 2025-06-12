<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

<script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{ asset('sneat/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('sneat/assets/js/main.js') }}"></script>

<!-- Page JS -->
<script src="{{ asset('sneat/assets/js/dashboards-analytics.js') }}"></script>

<!-- External JS -->
<script async defer src="https://buttons.github.io/buttons.js"></script>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    /**
     * Fungsi untuk menampilkan popup konfirmasi sebelum logout.
     */
    function confirmLogout(event) {
        event.preventDefault(); // Mencegah link langsung berjalan

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan keluar dari sesi aplikasi ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            // Jika pengguna menekan tombol "Ya, Keluar!"
            if (result.isConfirmed) {
                // Maka, submit form logout
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>

