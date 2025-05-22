<div class="modal-header">
    <h5 class="modal-title" id="myModalLabel">Detail Lomba</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <table class="table table-bordered table-striped">
        <tbody>
            <tr>
                <th style="width: 30%;">Nama Lomba</th>
                <td>{{ $lomba->nama_lomba }}</td>
            </tr>
            <tr>
                <th>Kategori</th>
                <td>{{ $lomba->kategori }}</td>
            </tr>
            <tr>
                <th>Bidang Keahlian</th>
                <td>{{ $lomba->bidang_keahlian }}</td>
            </tr>
            <tr>
                <th>Pembukaan Pendaftaran</th>
                <td>{{ $lomba->pembukaan_pendaftaran }}</td>
            </tr>
            <tr>
                <th>Penutupan Pendaftaran</th>
                <td>{{ $lomba->batas_pendaftaran }}</td>
            </tr>
            <tr>
                <th>Link Pendaftaran</th>
                <td>{{ $lomba->link_pendaftaran }}</td>
            </tr>
            <tr>
                <th>Link Penyelenggara</th>
                <td>{{ $lomba->link_penyelenggara }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $lomba->status_verifikasi }}</td>
            </tr>
        </tbody>
    </table>
</div> {{-- Akhir dari modal-body --}}

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>
