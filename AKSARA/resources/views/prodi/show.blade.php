<div class="modal-header">
    <h5 class="modal-title" id="myModalLabel">
        <i class="fas fa-user-graduate me-2"></i>Detail Program Studi
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <ul class="list-group list-group-flush">
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="fw-bold"><i class="fas fa-hashtag text-muted me-2"></i>Kode Prodi</span>
            <span class="badge bg-primary rounded-pill fs-6">{{ $prodi->kode }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="fw-bold"><i class="fas fa-graduation-cap text-muted me-2"></i>Nama Prodi</span>
            <span class="text-end">{{ $prodi->nama }}</span>
        </li>
    </ul>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
</div>