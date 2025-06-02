<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body{
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 15px;
        }
        table {
            width:100%;
            border-collapse: collapse;
        }
        td, th {
            padding: 4px 3px;
            border: 1px solid; /* Added for table borders in PDF */
        }
        th{
            text-align: left;
            background-color: #f2f2f2; /* Light gray background for headers */
        }
        .d-block{
            display: block;
        }
        img.image{
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
        .p-1{
            padding: 5px 1px 5px 1px;
        }
        .font-10{
            font-size: 10pt;
        }
        .font-11{
            font-size: 11pt;
        }
        .font-12{
            font-size: 12pt;
        }
        .font-13{
            font-size: 13pt;
        }
        .font-bold{ /* Added for bold text */
            font-weight: bold;
        }
        .mb-1{ /* Added for margin bottom */
            margin-bottom: 0.25rem;
        }
        .border-bottom-header{
            border-bottom: 1px solid;
        }
        .border-all, .border-all th, .border-all td{ /* Ensure this style is used or integrated */
            border: 1px solid;
        }
    </style>
</head>
<body>
    <table class="border-bottom-header">
        <tr>
            <td width="15%" class="text-center" style="border: none;"><img src="{{ public_path('polinema-bw.jpeg') }}" class="image"></td>
            <td width="85%" style="border: none;">
                <span class="text-center d-block font-11 font-bold mb-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</span>
                <span class="text-center d-block font-13 font-bold mb-1">POLITEKNIK NEGERI MALANG</span>
                <span class="text-center d-block font-10">Jl. Soekarno-Hatta No. 9 Malang 65141</span>
                <span class="text-center d-block font-10">Telepon (0341) 404424 Pes. 101-105, 0341-404420, Fax. (0341) 404420</span>
                <span class="text-center d-block font-10">Laman: www.polinema.ac.id</span>
            </td>
        </tr>
    </table>
    <h3 class="text-center" style="margin-top: 20px; margin-bottom: 20px;">LAPORAN DATA PROGRAM STUDI</h3>
    <table class="border-all">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Kode Prodi</th>
                <th>Nama Prodi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prodi as $p)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $p->kode }}</td>
                <td>{{ $p->nama }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>