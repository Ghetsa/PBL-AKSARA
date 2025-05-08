<div class="modal-header">
    <h5 class="modal-title" id="myModalLabel">Detail Program Studi</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <table class="table table-bordered table-striped">
        <tbody>
            <tr>
                <th style="width: 30%;">Role</th>
                <td>{{ $user->role }}</td>
            </tr>
            @if ($user->role == 'dosen')
            <tr>
                <th>NIP</th>
                <td>{{ $user->nip }}</td>
            </tr>
            <tr>
                <th>Bidang Keahlian</th>
                <td>{{ $user->bidang_keahlian }}</td>
            </tr>
            @endif
            <tr>
                <th>Nama</th>
                <td>{{ $user->nama }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $user->status }}</td>
            </tr>
        </tbody>
    </table>
</div> {{-- Akhir dari modal-body --}}

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>
