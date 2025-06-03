{{-- resources/views/lomba/partial_detail_perhitungan.blade.php --}}
<div>
    <h5>{{ $detail['lomba']->nama_lomba }}</h5>
    <hr>

    {{-- 1. Matriks Keputusan Awal --}}
    <h6>1. Matriks Keputusan Awal (X<sub>ij</sub>)</h6>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Kriteria</th>
                <th>Nilai Awal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail['asli'] as $k => $v)
                <tr>
                    <td>{{ ucfirst($k) }}</td>
                    <td>{{ $v }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 2. Normalisasi --}}
    <h6>2. Normalisasi (r<sub>ij</sub> = X<sub>ij</sub> / √ΣX<sub>j</sub>²)</h6>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Kriteria</th>
                <th>Nilai Normalisasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail['normalisasi'] as $k => $v)
                <tr>
                    <td>{{ ucfirst($k) }}</td>
                    <td>{{ number_format($v, 4) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 3. Nilai Terbobot --}}
    <h6>3. Nilai Terbobot (w<sub>j</sub> × r<sub>ij</sub>)</h6>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Kriteria</th>
                <th>Bobot (w<sub>j</sub>)</th>
                <th>Nilai Terbobot</th>
            </tr>
        </thead>
        <tbody>
            @php
                $weights = [
                    'minat'     => 0.25,
                    'keahlian'  => 0.25,
                    'tingkat'   => 0.15,
                    'hadiah'    => 0.15,
                    'penutupan' => 0.10,
                    'biaya'     => 0.10,
                ];
            @endphp
            @foreach ($detail['terbobot'] as $k => $v)
                <tr>
                    <td>{{ ucfirst($k) }}</td>
                    <td>{{ $weights[$k] }}</td>
                    <td>{{ number_format($v, 4) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 4. Total Benefit & Cost --}}
    <h6>4. Total Benefit dan Total Cost</h6>
    <ul>
        <li><strong>Total Benefit Score</strong>: {{ number_format($detail['benefit'], 4) }}</li>
        <li><strong>Total Cost Score</strong>: {{ number_format($detail['cost'], 4) }}</li>
    </ul>

    {{-- 5. Skor MOORA Akhir --}}
    <h6>5. Skor MOORA Akhir</h6>
    <p>
        <strong>Skor MOORA = (Total Benefit) – (Total Cost) = 
        <code>{{ number_format($detail['score'], 4) }}</code></strong>
    </p>
</div>
