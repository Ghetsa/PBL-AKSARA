{{-- resources/views/prodi/show_ajax.blade.php --}}

<div class="modal-header">
    <h5 class="modal-title" id="myModalLabel">Detail Program Studi</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <table class="table table-bordered table-striped">
        <tbody>
            <tr>
                <th style="width: 30%;">ID Prodi</th>
                <td>{{ $prodi->prodi_id }}</td> {{-- Ganti 'prodi_id' jika PK berbeda --}}
            </tr>
            <tr>
                <th>Kode Prodi</th>
                <td>{{ $prodi->kode }}</td>
            </tr>
            <tr>
                <th>Nama Prodi</th>
                <td>{{ $prodi->nama }}</td>
            </tr>
        </tbody>
    </table>
</div> {{-- Akhir dari modal-body --}}

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>