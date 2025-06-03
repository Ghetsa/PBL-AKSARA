<form id="formVerifikasi" action="{{ route('bimbingan.verify.submit', $prestasi->id) }}" method="POST">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title">Verifikasi Prestasi Mahasiswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Nama Mahasiswa</label>
            <input type="text" class="form-control" value="{{ $prestasi->mahasiswa->user->nama }}" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Nama Prestasi</label>
            <input type="text" class="form-control" value="{{ $prestasi->nama_prestasi }}" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <input type="text" class="form-control" value="{{ $prestasi->kategori }}" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Tingkat</label>
            <input type="text" class="form-control" value="{{ $prestasi->tingkat }}" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Tahun</label>
            <input type="text" class="form-control" value="{{ $prestasi->tahun }}" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Status Verifikasi</label>
            <select name="status_verifikasi" class="form-select" required>
                <option value="">-- Pilih Status --</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Catatan (Opsional)</label>
            <textarea name="catatan" class="form-control" rows="3" placeholder="Masukkan catatan jika perlu..."></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitVerification('formVerifikasi')">Simpan</button>
    </div>
</form>
