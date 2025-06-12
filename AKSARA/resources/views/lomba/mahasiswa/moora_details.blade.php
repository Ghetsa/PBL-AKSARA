<div class="modal-header">
    <h5 class="modal-title" id="mooraDetailsModalLabel"><i class="fas fa-calculator me-2"></i>Detail Perhitungan Metode
        MOORA</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $breadcrumb->title }}</h3>
                </div>
                <div class="card-body">
                    <h4 class="mt-4">Tahap 0: Kriteria dan Bobot (W)</h4>
                    <p>
                        Setiap kriteria diberi bobot untuk menentukan tingkat kepentingannya. Kriteria dibagi menjadi
                        dua jenis: <strong>Benefit</strong> (semakin besar nilainya semakin baik) dan
                        <strong>Cost</strong> (semakin kecil nilainya semakin baik).
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dt-responsive wrap">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Kriteria</th>
                                    <th>Bobot (W)</th>
                                    <th>Jenis</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($globalData['criteria'] as $key)
                                    <tr>
                                        <td><strong>C{{ $loop->iteration }}</strong></td>
                                        <td>{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                        <td>{{ number_format($globalData['weights'][$key], 4) }}</td>
                                        <td>
                                            @if (in_array($key, $globalData['benefit_criteria']))
                                                <span class="badge bg-success">Benefit</span>
                                            @else
                                                <span class="badge bg-danger">Cost</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-5">

                    <h4 class="mt-4">Tahap 1: Matriks Keputusan (X)</h4>
                    <p>
                        Matriks ini berisi nilai asli dari setiap alternatif (lomba) untuk setiap kriteria.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Alternatif (Lomba)</th>
                                    @foreach ($globalData['criteria'] as $criterion)
                                        <th>{{ ucwords(str_replace('_', ' ', $criterion)) }} (C{{ $loop->iteration }})
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $result)
                                    <tr>
                                        <td>{{ $result['lomba']->nama_lomba }}</td>
                                        @foreach ($result['original_values'] as $value)
                                            <td>{{ $value }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-5">

                    <h4 class="mt-4">Tahap 2: Matriks Normalisasi (R)</h4>
                    <p>
                        Normalisasi matriks dilakukan menggunakan rumus berikut:
                    </p>

                    <div class="bg-light p-3 rounded mb-3 text-center">
                        \[
                        \text{Pembagi}_j = \sqrt{\sum_{i=1}^{m} x_{ij}^2}
                        \]
                    </div>

                    <p>
                        Di mana:
                    </p>
                    <ul>
                        <li><strong>\( x_{ij} \)</strong>: nilai alternatif ke-<em>i</em> pada kriteria ke-<em>j</em>
                        </li>
                        <li><strong>\( m \)</strong>: jumlah total alternatif</li>
                    </ul>
                    <strong>Nilai Pembagi:</strong>
                    <ul>
                        @foreach ($globalData['divisors'] as $criterion => $divisor)
                            <li><strong>{{ ucwords(str_replace('_', ' ', $criterion)) }}:</strong>
                                {{ number_format($divisor, 4) }}</li>
                        @endforeach
                    </ul>
                    <p class="mt-3">
                        Matriks ternormalisasi \( r_{ij} \) dihitung dengan rumus:
                    </p>
                    <div class="bg-light p-3 rounded mb-4">
                        \[
                        r_{ij} = \frac{x_{ij}}{\text{Pembagi}_j}
                        \]
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Alternatif (Lomba)</th>
                                    @foreach ($globalData['criteria'] as $criterion)
                                        <th>{{ ucwords(str_replace('_', ' ', $criterion)) }} (C{{ $loop->iteration }})
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $result)
                                    <tr>
                                        <td>{{ $result['lomba']->nama_lomba }}</td>
                                        @foreach ($result['normalized_values'] as $value)
                                            <td>{{ number_format($value, 4) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-5">

                    <h4 class="mt-4">Tahap 3: Matriks Normalisasi Terbobot (V)</h4>
                    <p>
                        Setiap nilai yang telah dinormalisasi kemudian dikalikan dengan bobot kriteria yang sesuai:
                    </p>
                    <div class="bg-light p-3 rounded mb-4">
                        \[
                        v_{ij} = r_{ij} \times w_j
                        \]
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Alternatif (Lomba)</th>
                                    @foreach ($globalData['criteria'] as $criterion)
                                        <th>{{ ucwords(str_replace('_', ' ', $criterion)) }} (C{{ $loop->iteration }})
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $result)
                                    <tr>
                                        <td>{{ $result['lomba']->nama_lomba }}</td>
                                        @foreach ($globalData['criteria'] as $criterion)
                                            <td>{{ number_format($result['normalized_values'][$criterion] * $globalData['weights'][$criterion], 4) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-5">

                    <h4 class="mt-4">Tahap 4 & 5: Perhitungan Nilai Optimasi (Y) dan Perangkingan</h4>
                    <p>
                        Nilai akhir (skor optimasi) untuk setiap alternatif dihitung dengan menjumlahkan nilai terbobot
                        dari kriteria <strong>Benefit</strong> dan menguranginya dengan jumlah nilai terbobot dari
                        kriteria <strong>Cost</strong>.
                    </p>
                    <div class="bg-light p-3 rounded mb-4">
                        \[
                        Y_i = \sum_{j=1}^{g} v_{ij} - \sum_{j=g+1}^{n} v_{ij}
                        \]
                    </div>
                    <p>
                        Di mana \( g \) adalah jumlah kriteria benefit. Alternatif kemudian diurutkan dari nilai \( Y_i
                        \) tertinggi ke terendah.
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Peringkat</th>
                                    <th>Nama Lomba</th>
                                    <th>Perhitungan Skor (Benefit - Cost)</th>
                                    <th>Skor Akhir (Y)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $result)
                                    @php
                                        $benefitSum = 0;
                                        $costSum = 0;
                                        $benefitCalc = [];
                                        $costCalc = [];

                                        foreach ($globalData['benefit_criteria'] as $c) {
                                            $weightedValue =
                                                $result['normalized_values'][$c] * $globalData['weights'][$c];
                                            $benefitSum += $weightedValue;
                                            $benefitCalc[] = number_format($weightedValue, 4);
                                        }
                                        foreach ($globalData['cost_criteria'] as $c) {
                                            $weightedValue =
                                                $result['normalized_values'][$c] * $globalData['weights'][$c];
                                            $costSum += $weightedValue;
                                            $costCalc[] = number_format($weightedValue, 4);
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center align-middle">
                                            @if ($loop->iteration == 1)
                                                <span class="text-warning font-weight-bold"
                                                    style="font-size: 1.2rem;">{{ $loop->iteration }}</span>
                                            @elseif ($loop->iteration == 2)
                                                <span class="text-success font-weight-bold"
                                                    style="font-size: 1.2rem;">{{ $loop->iteration }}</span>
                                            @elseif ($loop->iteration == 3)
                                                <span class="text-info font-weight-bold"
                                                    style="font-size: 1.2rem;">{{ $loop->iteration }}</span>
                                            @else
                                                <span class="text-secondary"
                                                    style="font-size: 1.2rem;">{{ $loop->iteration }}</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ $result['lomba']->nama_lomba }}</strong></td>
                                        <td>
                                            ({{ implode(' + ', $benefitCalc) }})
                                            <br> - <br>
                                            ( {{ implode(' + ', $costCalc) }} )
                                        </td>
                                        <td><strong>{{ number_format($result['score'], 4) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
</div>
