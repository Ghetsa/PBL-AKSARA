<div class="modal-header">
    <h5 class="modal-title" id="myModalLabel">
        <i class="fas fa-calendar-alt me-2"></i>Detail Periode Semester
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <ul class="list-group list-group-flush">
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="fw-bold"><i class="fas fa-business-time text-muted me-2"></i>Semester</span>
            <span class="badge bg-primary rounded-pill fs-6">{{ $periode->semester }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="fw-bold col-6"><i class="fas fa-calendar-times text-muted me-2"></i>Tahun Akademik</span>
            <span class="text-end text-break col-6">{{ $periode->tahun_akademik }}</span>
        </li>
    </ul>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
</div>

{{-- <div class="modal-header">
    <h5 class="modal-title" id="myModalLabel">Detail Periode Semester</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <table class="table table-bordered table-striped">
        <tbody>
            <tr>
                <th style="width: 30%;">ID Periode</th>
                <td>{{ $periode->periode_id }}</td> 
            </tr>
            <tr>
                <th>Semester</th>
                <td>{{ $periode->semester }}</td>
            </tr>
            <tr>
                <th>Tahun Akademik</th>
                <td>{{ $periode->tahun_akademik }}</td>
            </tr>
        </tbody>
    </table>
</div> 

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div> --}}