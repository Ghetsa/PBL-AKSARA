<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Data Prestasi</title>
  <style>
    body {
      font-family: "Times New Roman", Times, serif;
      margin: 6px 20px 5px 20px;
      line-height: 15px;
    }

    thead {
      background-color: rgb(220, 224, 230);
    }

    table {
      width: 100%;
      border-collapse: collapse;
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

    .font-11 {
      font-size: 10pt;
    }

    .font-12 {
      font-size: 12pt;
      margin-bottom: 2pt;
    }

    .font-13 {
      font-size: 13pt;
    }

    .font-15 {
      font-size: 15pt;
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
      <td width="15%" class="text-center"><img src="{{ asset('logo/polinema-bw.png') }}" width="100%"></td>
      <td width="100%">
        <span class="text-center d-block font-12 font-bold">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN
          TEKNOLOGI</span>
        <span class="text-center d-block font-15 font-bold">POLITEKNIK NEGERI MALANG</span>
        <span class="text-center d-block font-11">Jl. Soekarno-Hatta No. 9 Malang 65141</span>
        <span class="text-center d-block font-11">Telepon (0341) 404424 Pes. 101-105, 0341-404420, Fax. (0341)
          404420</span>
        <span class="text-center d-block font-11">Laman: www.polinema.ac.id</span>
      </td>
    </tr>
  </table>

  <h2 style="text-align: center;" class="font-15">Data Prestasi Mahasiswa</h2>
  <table class="border-all">
    <thead>
      <tr>
        <th class="text-center font-12">No</th>
        <th class="text-center font-12">Nama Mahasiswa</th>
        <th class="text-center font-12">Nama Prestasi</th>
        <th class="text-center font-12">Kategori</th>
        <th class="text-center font-12">Bidang</th>
        <th class="text-center font-12">Penyelenggara</th>
        <th class="text-center font-12">Tingkat</th>
        <th class="text-center font-12">Tahun</th>
        <th class="text-center font-12">Dosen Pembimbing</th>
        <th class="text-center font-12">Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($prestasi as $i => $item)
      <tr>
      <td class="font-12">{{ $i + 1 }}</td>
      <td class="font-12">{{ $item->mahasiswa->user->nama ?? '-' }}</td>
      <td class="font-12">{{ $item->nama_prestasi }}</td>
      <td class="font-12">{{ ucfirst($item->kategori) }}</td>
      <td class="font-12">{{ $item->bidang->bidang_nama ?? '-' }}</td>
      <td class="font-12">{{ $item->penyelenggara }}</td>
      <td class="font-12">{{ ucfirst($item->tingkat) }}</td>
      <td class="font-12">{{ $item->tahun }}</td>
      <td class="font-12">{{ $item->dosen->user->nama ?? '-' }}</td>
      <td class="font-12">{{ ucfirst($item->status_verifikasi) }}</td>
      </tr>
    @endforeach
    </tbody>
  </table>
</body>

</html>