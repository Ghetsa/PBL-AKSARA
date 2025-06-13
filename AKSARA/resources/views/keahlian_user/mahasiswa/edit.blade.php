<div class="modal-header">
    <h5 class="modal-title"><i class="ti ti-edit-circle me-2"></i>Edit Keahlian</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="formEditKeahlianUser" enctype="multipart/form-data" novalidate>
    <div class="modal-body" style="max-height: 68vh; overflow-y: auto;">
        @csrf
        <div class="mb-3">
            <label for="edit_bidang_id" class="form-label">Bidang Keahlian</label>
            <select name="bidang_id" id="edit_bidang_id" class="form-select">
                <option value="">-- Pilih Bidang Keahlian --</option>
                @foreach ($bidang as $item)
                    <option value="{{ $item->bidang_id }}" {{ $data->bidang_id == $item->bidang_id ? 'selected' : '' }}>
                        {{ $item->bidang_nama }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback"></div>
        </div>
        <div class="mb-3">
            <label for="edit_nama_sertifikat" class="form-label">Nama Sertifikat</label>
            <input type="text" name="nama_sertifikat" id="edit_nama_sertifikat" class="form-control" value="{{ old('nama_sertifikat', $data->nama_sertifikat) }}" placeholder="Cth: Web Development Expert, AWS Certified Cloud Practitioner">
            <div class="invalid-feedback"></div>
        </div>
        <div class="mb-3">
            <label for="edit_lembaga_sertifikasi" class="form-label">Lembaga Penerbit</label>
            <input type="text" name="lembaga_sertifikasi" id="edit_lembaga_sertifikasi" class="form-control" value="{{ old('lembaga_sertifikasi', $data->lembaga_sertifikasi) }}" placeholder="Cth: Google, AWS, Microsoft">
            <div class="invalid-feedback"></div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="edit_tanggal_perolehan_sertifikat" class="form-label">Tanggal Perolehan</label>
                <input type="date" name="tanggal_perolehan_sertifikat" id="edit_tanggal_perolehan_sertifikat" class="form-control" value="{{ old('tanggal_perolehan_sertifikat', $data->tanggal_perolehan_sertifikat ? \Carbon\Carbon::parse($data->tanggal_perolehan_sertifikat)->format('Y-m-d') : '') }}">
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="edit_tanggal_kadaluarsa_sertifikat" class="form-label">Tanggal Kedaluwarsa</label>
                <input type="date" name="tanggal_kadaluarsa_sertifikat" id="edit_tanggal_kadaluarsa_sertifikat" class="form-control" value="{{ old('tanggal_kadaluarsa_sertifikat', $data->tanggal_kadaluarsa_sertifikat ? \Carbon\Carbon::parse($data->tanggal_kadaluarsa_sertifikat)->format('Y-m-d') : '') }}">
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="mb-3">
            <label for="edit_sertifikasi" class="form-label">File Bukti Sertifikat</label>
            <input type="file" name="sertifikasi" id="edit_sertifikasi" class="form-control">
            <small class="form-text text-muted">Format: PDF, JPG, PNG. Maksimal 2MB.</small>
            @if($data->sertifikasi)
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah file. <a href="{{ asset('storage/' . $data->sertifikasi) }}" target="_blank">Lihat file saat ini</a>.</small>
            @endif
            <div class="invalid-feedback"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Definisi custom methods (jika belum ada di scope global)
    if (typeof $.validator.methods.filesize === 'undefined') {
        $.validator.addMethod('filesize', function(value, element, param) {
            return this.optional(element) || (element.files[0].size <= param);
        }, 'Ukuran file tidak boleh lebih dari {0} bytes.');
    }
    if (typeof $.validator.methods.after_or_equal === 'undefined') {
         $.validator.addMethod("after_or_equal", function(value, element, param) {
            if (!/Invalid|NaN/.test(new Date(value))) {
                const startDate = $(param).val();
                if (!startDate) return true;
                return new Date(value) >= new Date(startDate);
            }
            return isNaN(value) && isNaN($(param).val()) || (Number(value) >= Number($(param).val()));
        }, 'Tanggal kadaluwarsa harus setelah atau sama dengan tanggal perolehan.');
    }
    
    // Inisialisasi jQuery Validate pada form edit
    const validator = $('#formEditKeahlianUser').validate({
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
                required: false,
                date: true,
                after_or_equal: '#edit_tanggal_perolehan_sertifikat'
            },
            sertifikasi: {
                // Tidak required pada form edit
                extension: "pdf|jpg|jpeg|png",
                filesize: 2097152 // 2 MB
            }
        },
        messages: {
            // (Pesan error sama seperti form create)
            bidang_id: { required: "Bidang keahlian wajib dipilih." },
            nama_sertifikat: { required: "Nama sertifikat wajib diisi.", maxlength: "Tidak boleh lebih dari 50 karakter.", minlength: "Minimal 5 karakter."  },
            lembaga_sertifikasi: {  required: "Lembaga sertifikasi wajib diisi.", maxlength: "Tidak boleh lebih dari 50 karakter.", , minlength: "Minimal 2 karakter." },
            tanggal_perolehan_sertifikat: { required: "Tanggal perolehan wajib diisi.", date: "Format tanggal tidak valid." },
            tanggal_kadaluarsa_sertifikat: { required: "Tanggal kedaluwarsa wajib diisi.", after_or_equal: "Tanggal kedaluwarsa harus sesudah tanggal perolehan." },
            sertifikasi: { extension: "Tipe file harus PDF, JPG, JPEG, atau PNG.", filesize: "Ukuran file maksimal adalah 2MB." }
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
            const formData = new FormData(form);
            formData.append('_method', 'PUT'); // Tambahkan method spoofing untuk Laravel

            const submitButton = $(form).find('button[type="submit"]');
            const originalButtonText = submitButton.html();

            $.ajax({
                url: "{{ route('keahlian_user.update', $data->keahlian_user_id) }}",
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
});
</script>