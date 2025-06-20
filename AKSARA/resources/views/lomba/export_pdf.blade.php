<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: rgb(220, 224, 230);
        }

        td,
        th {
            padding: 4px 3px;
        }

        th {
            text-align: left;
        }

        .d-block {
            display: block;
        }

        img.image {
            width: auto;
            height: 80px;
            max-width: 150px;
            max-height: 150px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .p-1 {
            padding: 5px 1px 5px 1px;
        }

        .font-10 {
            font-size: 10pt;
        }

        .font-11 {
            font-size: 11pt;
        }

        .font-12 {
            font-size: 12pt;
        }

        .font-13 {
            font-size: 13pt;
        }

        .border-bottom-header {
            border-bottom: 1px solid;
        }

        .border-all,
        .border-all th,
        .border-all td {
            border: 1px solid;
        }
    </style>
</head>

<body>
    <table class="border-bottom-header">
        <tr>
            <td width="15%" class="text-center"><img src="{{ asset('logo/polinema-bw.png') }}" class="image"></td>
            <td width="85%">
                <span class="text-center d-block font-11 font-bold mb-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN
                    TEKNOLOGI</span>
                <span class="text-center d-block font-13 font-bold mb-1">POLITEKNIK NEGERI MALANG</span>
                <span class="text-center d-block font-10">Jl. Soekarno-Hatta No. 9 Malang 65141</span>
                <span class="text-center d-block font-10">Telepon (0341) 404424 Pes. 101-105, 0341-404420, Fax. (0341)
                    404420</span>
                <span class="text-center d-block font-10">Laman: www.polinema.ac.id</span>
            </td>
        </tr>
    </table>
    <h3 class="text-center">LAPORAN DATA USER</h4>
        <table class="border-all">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Nama Lomba</th>
                    <th class="text-center">Penyelenggara</th>
                    <th class="text-center">Tingkat</th>
                    <th class="text-center">Biaya</th>
                    <th class="text-center">Pembukaan Pendaftaran</th>
                    <th class="text-center">Batas Pendaftaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lomba as $l)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $l->nama_lomba }}</td>
                        {{-- @if($u->role === 'admin' && $u->admin)
                        <td>{{ $u->admin->nip }}</td>
                        @elseif($u->role === 'dosen' && $u->dosen)
                        <td>{{ $u->dosen->nip }}</td>
                        @elseif($u->role === 'mahasiswa' && $u->mahasiswa)
                        <td>{{ $u->mahasiswa->nim }}</td>
                        @else
                        N/A
                        @endif --}}
                        <td>{{ $l->penyelenggara }}</td>
                        <td>{{ $l->tingkat }}</td>
                        <td>{{ $l->biaya }}</td>
                        <td>{{ $l->pembukaan_pendaftaran->isoFormat('D MMMM YYYY') }}</td>
                        <td>{{ $l->batas_pendaftaran->isoFormat('D MMMM YYYY') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
</body>

</html>