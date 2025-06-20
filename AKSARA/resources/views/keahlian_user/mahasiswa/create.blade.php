<div class="modal-header">
    <h5 class="modal-title">Tambah Keahlian</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="formKeahlianUser" action="{{ route('keahlian_user.store') }}" method="POST" enctype="multipart/form-data" novalidate>
    <div class="modal-body" style="max-height: 68vh; overflow-y: auto;">
        @csrf
        
        <div class="mb-3">
            <label for="bidang_id" class="form-label">Bidang Keahlian </label>
            <select name="bidang_id" id="bidang_id" class="form-select">
                <option value="">-- Pilih Bidang Keahlian --</option>
                @foreach($bidang as $b)
                    <option value="{{ $b->bidang_id }}">{{ $b->bidang_nama }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback"></div>
        </div>

        <div class="mb-3">
            <label for="nama_sertifikat" class="form-label">Nama Sertifikat</label>
            <input type="text" name="nama_sertifikat" id="nama_sertifikat" class="form-control" placeholder="Cth: Web Development Expert, AWS Certified Cloud Practitioner">
            <div class="invalid-feedback"></div>
        </div>

        <div class="mb-3">
            <label for="lembaga_sertifikasi" class="form-label">Lembaga Penerbit</label>
            <input type="text" name="lembaga_sertifikasi" id="lembaga_sertifikasi" class="form-control" placeholder="Cth: Google, AWS, Microsoft">
            <div class="invalid-feedback"></div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="tanggal_perolehan_sertifikat" class="form-label">Tanggal Perolehan</label>
                <input type="date" name="tanggal_perolehan_sertifikat" id="tanggal_perolehan_sertifikat" class="form-control">
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="tanggal_kadaluarsa_sertifikat" class="form-label">Tanggal Kedaluwarsa</label>
                <input type="date" name="tanggal_kadaluarsa_sertifikat" id="tanggal_kadaluarsa_sertifikat" class="form-control">
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="mb-3">
            <label for="sertifikasi" class="form-label">File Bukti Sertifikat </label>
            <input type="file" name="sertifikasi" id="sertifikasi" class="form-control">
            <small class="form-text text-muted">Format: PDF, JPG, PNG. Maksimal 2MB.</small>
            <div class="invalid-feedback"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Definisi custom validation method untuk ukuran file
    $.validator.addMethod('filesize', function(value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    }, 'Ukuran file tidak boleh lebih dari {0} bytes.');

    // Definisi custom validation method untuk tanggal
    $.validator.addMethod("after_or_equal", function(value, element, param) {
        if (!/Invalid|NaN/.test(new Date(value))) {
            const startDate = $(param).val();
            if (!startDate) return true; // Jika tanggal mulai kosong, anggap valid
            return new Date(value) >= new Date(startDate);
        }
        return isNaN(value) && isNaN($(param).val()) || (Number(value) >= Number($(param).val()));
    }, 'Tanggal kadaluwarsa harus setelah atau sama dengan tanggal perolehan.');

    // Inisialisasi jQuery Validate pada form
    const validator = $('#formKeahlianUser').validate({
        rules: {
            bidang_id: {
                required: true
            },
            nama_sertifikat: {
                required: true,
                minlength: 5,
                maxlength: 50
            },
            lembaga_sertifikasi: {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            tanggal_perolehan_sertifikat: {
                required: true,
                date: true
            },
            tanggal_kadaluarsa_sertifikat: {
                required: true,
                date: true,
                after_or_equal: '#tanggal_perolehan_sertifikat'
            },
            sertifikasi: {
                required: true,
                extension: "pdf|jpg|jpeg|png",
                filesize: 2097152 // 2 MB in bytes
            }
        },
        messages: {
            bidang_id: {
                required: "Bidang keahlian wajib dipilih."
            },
            nama_sertifikat: {
                required: "Nama sertifikat wajib diisi.",
                maxlength: "Nama sertifikat minimal 5 karakter.",
                maxlength: "Nama sertifikat tidak boleh lebih dari 50 karakter."
            },
            lembaga_sertifikasi: {
                required: "Lembaga sertifikasi wajib diisi.",
                maxlength: "Lembaga sertifikasi minimal 2 karakter.",
                maxlength: "Lembaga sertifikasi tidak boleh lebih dari 50 karakter."
            },
            tanggal_perolehan_sertifikat: {
                required: "Tanggal perolehan wajib diisi.",
                date: "Format tanggal tidak valid."
            },
            tanggal_kadaluarsa_sertifikat: {
                required: "Tanggal kedaluwarsa wajib diisi.",
                after_or_equal: "Tanggal kedaluwarsa harus sesudah tanggal perolehan."
            },
            sertifikasi: {
                required: "File bukti wajib diunggah.",
                extension: "Tipe file harus PDF, JPG, JPEG, atau PNG.",
                filesize: "Ukuran file maksimal adalah 2MB."
            }
        },
        errorElement: 'div',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.mb-3, .col-md-6').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        },
        submitHandler: function(form) {
            // Jika form valid, lanjutkan dengan AJAX submit
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');
            const originalButtonText = submitButton.html();

            $.ajax({
                url: $(form).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
                },
                success: function (res) {
                    if (res.status) {
                        Swal.fire('Berhasil', res.message, 'success');
                        $('#myModal').modal('hide');
                        $('#dataKeahlianUser').DataTable().ajax.reload();
                    } else {
                        Swal.fire('Gagal', res.message || 'Gagal menyimpan data.', 'error');
                    }
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessages = '';
                    if (errors) {
                        $.each(errors, function(key, value) { errorMessages += value[0] + '<br>'; });
                    } else {
                        errorMessages = xhr.responseJSON.message || 'Terjadi kesalahan.';
                    }
                    Swal.fire('Gagal Validasi Server', errorMessages, 'error');
                },
                complete: function () {
                    submitButton.prop('disabled', false).html(originalButtonText);
                }
            });
        }
    });

    // Menghapus event handler submit bawaan untuk digantikan oleh submitHandler dari validate
    // $("#formKeahlianUser").on("submit", function(e) { e.preventDefault(); });
});
</script>