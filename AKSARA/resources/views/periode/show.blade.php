<div class="modal-header">
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
</div> {{-- Akhir dari modal-body --}}

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>