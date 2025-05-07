{{-- Modal Create User (dipindahkan dari index.blade.php) --}}
  <div class="modal fade" id="modalCreateUser" tabindex="-1" aria-labelledby="modalCreateUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg"> {{-- Buat modal lebih lebar jika perlu --}}
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalCreateUserLabel">Tambah User Baru</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              {{-- Pindahkan form dari create.blade.php ke sini --}}
              <form id="formCreateUser" class="form-horizontal" method="POST" action="{{ route('user.store') }}">
                @csrf {{-- CSRF token tetap diperlukan untuk AJAX POST --}}
                <div class="form-group row mb-3">
                  <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="nama" name="nama" required>
                    {{-- Error message akan ditambahkan oleh jQuery Validation --}}
                  </div>
                </div>
                <div class="form-group row mb-3">
                  <label for="email" class="col-sm-2 col-form-label">Email</label>
                  <div class="col-sm-10">
                    <input type="email" class="form-control" id="email" name="email" required>
                  </div>
                </div>
                <div class="form-group row mb-3">
                  <label for="password" class="col-sm-2 col-form-label">Password</label>
                  <div class="col-sm-10">
                    <input type="password" class="form-control" id="password" name="password" required>
                  </div>
                </div>
                <div class="form-group row mb-3">
                  <label for="role_modal" class="col-sm-2 col-form-label">Role</label>
                  <div class="col-sm-10">
                    {{-- Beri ID unik untuk select role di modal --}}
                    <select class="form-select" id="role_modal" name="role" required>
                      <option value="">- Pilih Role -</option>
                      <option value="admin">Admin</option>
                      <option value="mahasiswa">Mahasiswa</option>
                      <option value="dosen">Dosen</option>
                    </select>
                  </div>
                </div>
    
                {{-- Field tambahan --}}
                <div id="form-nip-modal" style="display: none;">
                  <div class="form-group row mb-3">
                    <label for="nip" class="col-sm-2 col-form-label">NIP</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="nip" name="nip">
                    </div>
                  </div>
                </div>
                <div id="form-keahlian-modal" style="display: none;">
                  <div class="form-group row mb-3">
                    <label for="bidang_keahlian" class="col-sm-2 col-form-label">Bidang Keahlian</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="bidang_keahlian" name="bidang_keahlian">
                    </div>
                  </div>
                </div>
                <div id="form-nim-modal" style="display: none;">
                  <div class="form-group row mb-3">
                    <label for="nim" class="col-sm-2 col-form-label">NIM</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="nim" name="nim">
                    </div>
                  </div>
                </div>
                <div id="form-prodi_id-modal" style="display: none;">
                    <div class="form-group row mb-3">
                        <label for="prodi_id" class="col-sm-2 col-form-label">Prodi</label>
                        <div class="col-sm-10">
                            <select class="form-control @error('prodi_id') is-invalid @enderror" id="prodi_id" name="prodi_id" required>
                                <option value="">- Pilih Prodi -</option>
                                @foreach($prodi as $item)
                                    <option value="{{ $item->prodi_id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div id="form-periode_id-modal" style="display: none;">
                    <div class="form-group row mb-3">
                        <label for="periode_id" class="col-sm-2 col-form-label">Periode</label> {{-- Ganti label for --}}
                        <div class="col-sm-10">
                            <select class="form-control @error('periode_id') is-invalid @enderror" id="periode_id" name="periode_id" required>
                                <option value="">- Pilih Periode -</option>
                                @foreach($periode as $item)
                                    <option value="{{ $item->periode_id }}">{{ $item->tahun_akademik }} / {{ $item->semester }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div id="form-bidang_minat-modal" style="display: none;">
                    <div class="form-group row mb-3">
                        <label for="bidang_minat" class="col-sm-2 col-form-label">Bidang Minat</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="bidang_minat" name="bidang_minat">
                        </div>
                    </div>
                </div>
                <div id="form-keahlian-mahasiswa-modal" style="display: none;">
                    <div class="form-group row mb-3">
                        <label for="keahlian" class="col-sm-2 col-form-label">Keahlian Mahasiswa</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="keahlian_mahasiswa" name="keahlian_mahassiwa">
                        </div>
                    </div>
                </div>
                    {{-- End Field tambahan --}}
        
                    <div class="form-group row mb-3">
                        <label for="status" class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                        <select class="form-select" id="status" name="status" required>
                            <option value="">- Pilih Status -</option>
                            <option value="aktif" selected>Aktif</option> {{-- Default ke aktif --}}
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                        </div>
                    </div>
                    {{-- Tombol submit dipindahkan ke modal footer --}}
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    {{-- Tombol submit form --}}
                    <button type="submit" class="btn btn-primary" form="formCreateUser">Simpan</button>
                </div>
                </div>
            </div>
            </div>
        
        @push('js')
            <script>
            $(document).ready(function() {
                // ----- LOGIKA MODAL CREATE USER -----
        
                const modalCreate = new bootstrap.Modal(document.getElementById('modalCreateUser'));
                const formCreate = $('#formCreateUser');
                const modalElement = document.getElementById('modalCreateUser');
        
                // Fungsi untuk menampilkan/menyembunyikan field tambahan di modal
                function toggleAdditionalFormsModal() {
                const role = $('#role_modal').val(); // Ambil dari select di modal
                const formNip = $('#form-nip-modal');
                const formKeahlian = $('#form-keahlian-modal');
                const formNim = $('#form-nim-modal');
                const formProdi_id = $('#form-prodi_id-modal');
                const formPeriode = $('#form-periode_id-modal');
                const formBidang_Minat = $('#form-bidang_minat-modal');
                const formKeahlianMahasiswa = $('#form-keahlian-mahasiswa-modal');
        
                // Reset semua ke hidden dulu
                formNip.hide();
                formKeahlian.hide();
                formNim.hide();
                formProdi_id.hide();
                formPeriode.hide();
                formBidang_Minat.hide();
                formKeahlianMahasiswa.hide();
        
                // Reset required attribute jika ada
                formNip.find('input').prop('required', false);
                formKeahlian.find('input').prop('required', false);
                formNim.find('input').prop('required', false);
                formProdi_id.find('input').prop('required', false);
                formPeriode.find('input').prop('required', false);
                formBidang_Minat.find('input').prop('required', false);
                formKeahlianMahasiswa.find('input').prop('required', false);
        
                if (role === 'admin') {
                    formNip.show();
                    formNip.find('input').prop('required', true); // NIP wajib untuk admin
                } else if (role === 'dosen') {
                    formNip.show();
                    formKeahlian.show();
                    formNip.find('input').prop('required', true); // NIP wajib untuk dosen
                    formKeahlian.find('input').prop
                    ('required', true); // Keahlian wajib untuk dosen
                } else if (role === 'mahasiswa') {
                    formNim.show();
                    formProdi_id.show();
                    formPeriode.show();
                    formNim.find('input').prop('required', true); // NIM wajib untuk mahasiswa
                    formProdi_id.find('input').prop('required', true); // Prodi wajib untuk mahasiswa
                    formPeriode.find('input').prop('required', true); // Periode wajib untuk mahasiswa
                    formBidang_Minat.find('input').prop('required', true); // Bidang Minat Wajib untuk mahasiswa
                    formKeahlianMahasiswa.find('input').prop('required', true); // Keahlian mahasiswa Wajib untuk mahasiswa
                }
            }

            // Event listener untuk select role di modal
            $('#role_modal').on('change', toggleAdditionalFormsModal);

            // Inisialisasi state field tambahan saat modal pertama kali dibuka
            modalElement.addEventListener('show.bs.modal', function (event) {
                toggleAdditionalFormsModal(); // Panggil fungsi toggle saat modal akan tampil
            });

            // Reset form dan validasi saat modal ditutup
            modalElement.addEventListener('hidden.bs.modal', function (event) {
                formCreate.trigger('reset'); // Reset nilai form
                formCreate.validate().resetForm(); // Reset pesan error validasi
                formCreate.find('.is-invalid').removeClass('is-invalid'); // Hapus class invalid
                formCreate.find('.is-valid').removeClass('is-valid'); // Hapus class valid
                toggleAdditionalFormsModal(); // Panggil lagi untuk menyembunyikan field tambahan
            });

            // Inisialisasi jQuery Validation
            formCreate.validate({
                rules: {
                    nama: {
                        required: true,
                        minlength: 3
                    },
                    email: {
                        required: true,
                        email: true
                        // Jika perlu validasi unique email via AJAX:
                    },
                    password: {
                        required: true,
                        minlength: 6 // Atur minimal panjang password
                    },
                    role: {                      
                        required: true
                    },
                    status: {
                        required: true
                    },
                    nip: {
                        // required akan di-set dinamis oleh toggleAdditionalFormsModal
                        digits: true // Contoh: hanya boleh angka
                    },
                    nim: {
                        // required akan di-set dinamis
                        digits: true
                    }
                },
                messages: {
                    nama: {
                        required: "Nama tidak boleh kosong",
                        minlength: "Nama minimal harus 3 karakter"
                    },
                    email: {
                        required: "Email tidak boleh kosong",
                        email: "Format email tidak valid"
                        // remote: "Email sudah terdaftar"
                    },
                    password: {
                        required: "Password tidak boleh kosong",
                        minlength: "Password minimal harus 6 karakter"
                    },
                    role: "Silakan pilih role",
                    status: "Silakan pilih status",
                    nip : {
                        required: "NIP wajib diisi untuk role ini",
                        digits: "NIP hanya boleh berisi angka"
                    },
                    nim : {
                        required: "NIM wajib diisi untuk role ini",
                        digits: "NIM hanya boleh berisi angka"
                    },
                    bidang_keahlian: {
                        required: "Bidang keahlian wajib diisi untuk Dosen"
                    },
                        prodi_id: {
                        required: "Prodi wajib diisi untuk Mahasiswa"
                    },
                        periode_id: {
                        required: "Periode wajib diisi untuk Mahasiswa"
                    },
                        bidang_Minat: {
                        required: "Bidang Minat wajib diisi untuk Mahasiswa"
                    },
                        keahlian_mahasiswa: {
                        required: "Keahlian mahasiswa wajib diisi untuk Mahasiswa"
                    }
                },
                submitHandler: function(form) {
                    // Jika form valid, kirim data via AJAX
                    $.ajax({
                        url: $(form).attr('action'), // Ambil URL dari atribut action form
                        method: $(form).attr('method'), // Ambil method dari atribut method form
                        data: $(form).serialize(), // Ambil semua data form
                        dataType: 'json', // Harapkan response JSON dari server
                        beforeSend: function() {
                            // Tampilkan loading atau disable tombol submit
                            $(form).find('button[type="submit"]').prop('disabled', true).text('Menyimpan...');
                        },
                        success: function(response) {
                            // Tutup modal
                            modalCreate.hide();

                            // Tampilkan notifikasi sukses (Gunakan SweetAlert jika ada)
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                });
                            } else {
                                alert(response.message);
                            }

                            // Reload DataTable untuk melihat data baru
                            dataUser.ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            // Tangani error jika ada
                            console.error(xhr.responseText);
                            var err = JSON.parse(xhr.responseText);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: err.message,
                                });
                            } else {
                                alert('Terjadi kesalahan saat menyimpan data.');
                            }
                        },
                        complete: function() {
                            // Aktifkan kembali tombol submit dan ubah teksnya
                            $(form).find('button[type="submit"]').prop('disabled', false).text('Simpan');
                        }
                    });
                }
            });

        }); // End document ready
    </script>
@endpush