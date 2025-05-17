<form id="formTambahKeahlian" method="POST" action="{{ route('mahasiswa.keahlianuser.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title">Tambah Keahlian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label for="keahlian_id" class="form-label">Pilih Keahlian</label>
            <select name="keahlian_id" id="keahlian_id" class="form-select" required>
                <option value="">-- Pilih --</option>
                @foreach ($keahlians as $item)
                    <option value="{{ $item->keahlian_id }}">{{ $item->keahlian_nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="sertifikasi_file" class="form-label">Upload Bukti Sertifikasi (Opsional)</label>
            <input type="file" name="sertifikasi_file" id="sertifikasi_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            <small class="text-muted">Format: PDF, JPG, PNG. Maks 2MB.</small>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
    </div>
</form>
