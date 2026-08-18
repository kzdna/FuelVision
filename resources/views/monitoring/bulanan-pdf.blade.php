<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>Monitoring Bulanan BBM</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 18px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #222;
            margin: 0;
            padding: 0;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 4px;
        }

        h2 {
            font-size: 10px;
            margin: 12px 0 6px;
        }

        .subtitle {
            color: #666;
            font-size: 8px;
            margin-bottom: 10px;
        }

        .periode {
            border: 1px solid #d9d9d9;
            background: #f7f7f7;
            padding: 7px 9px;
            margin-bottom: 10px;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .summary td {
            width: 25%;
            border: 1px solid #d9d9d9;
            padding: 7px 8px;
            vertical-align: top;
        }

        .summary-label {
            color: #666;
            font-size: 7px;
            margin-bottom: 3px;
        }

        .summary-value {
            font-size: 11px;
            font-weight: bold;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        table.data thead {
            display: table-header-group;
        }

        table.data tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        table.data th {
            background: #eeeeee;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        table.data th,
        table.data td {
            border: 1px solid #cfcfcf;
            padding: 4px 5px;
        }

        table.data td {
            vertical-align: middle;
        }

        .section {
            page-break-inside: avoid;
        }

        .section-title {
            page-break-after: avoid;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .wajar {
            color: #198754;
            font-weight: bold;
        }

        .tidak-wajar {
            color: #dc3545;
            font-weight: bold;
        }

        .belum {
            color: #6c757d;
            font-weight: bold;
        }

        .danger {
            color: #dc3545;
            font-weight: bold;
        }

        .footer {
            margin-top: 15px;
            padding-top: 7px;
            border-top: 1px solid #ddd;
            font-size: 7px;
            color: #777;
            text-align: center;
        }

        .summary-status {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .summary-status td {
            width: 25%;
            border: 1px solid #d9d9d9;
            padding: 6px 8px;
        }

        .status-label {
            font-size: 7px;
            color: #666;
            margin-bottom: 3px;
        }

        .status-value {
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h1>
        Monitoring Bulanan BBM
    </h1>

    <div class="subtitle">
        FuelVision - Ringkasan penggunaan dan evaluasi BBM
    </div>


    <div class="periode">

        <strong>Periode:</strong>

        {{ $awalBulan->translatedFormat('F Y') }}

        @if (isset($akhirBulan))
            - {{ $akhirBulan->format('d/m/Y') }}
        @endif

    </div>


    <table class="summary">

        <tr>

            <td>

                <div class="summary-label">
                    Total Transaksi
                </div>

                <div class="summary-value">
                    {{ $totalTransaksi }}
                </div>

            </td>


            <td>

                <div class="summary-label">
                    Total Liter
                </div>

                <div class="summary-value">
                    {{ number_format((float) $totalLiter, 2, ',', '.') }} L
                </div>

            </td>


            <td>

                <div class="summary-label">
                    Total Biaya
                </div>

                <div class="summary-value">
                    Rp {{ number_format((float) $totalBiaya, 0, ',', '.') }}
                </div>

            </td>


            <td>

                <div class="summary-label">
                    Rata-rata Liter
                </div>

                <div class="summary-value">
                    {{ number_format((float) $rataRataLiter, 2, ',', '.') }} L
                </div>

            </td>

        </tr>

    </table>


    <table class="summary-status">

        <tr>

            <td>

                <div class="status-label">
                    Kendaraan Wajar
                </div>

                <div class="status-value wajar">

                    {{ collect($perKendaraan)->where('evaluasi', 'Wajar')->count() }}

                </div>

            </td>


            <td>

                <div class="status-label">
                    Kendaraan Tidak Wajar
                </div>

                <div class="status-value tidak-wajar">

                    {{ collect($perKendaraan)->where('evaluasi', 'Tidak Wajar')->count() }}

                </div>

            </td>


            <td>

                <div class="status-label">
                    Total Pemborosan
                </div>

                <div class="status-value danger">

                    {{ number_format(
                        collect($perKendaraan)->sum(function ($item) {
                            return (float) ($item['varian_pemborosan'] ?? 0);
                        }),
                        2,
                        ',',
                        '.'
                    ) }}
                    L

                </div>

            </td>


            <td>

                <div class="status-label">
                    Biaya Kerugian
                </div>

                <div class="status-value danger">

                    Rp
                    {{ number_format(
                        collect($perKendaraan)->sum(function ($item) {
                            return (float) ($item['biaya_kerugian'] ?? 0);
                        }),
                        0,
                        ',',
                        '.'
                    ) }}

                </div>

            </td>

        </tr>

    </table>


    <div class="section">

        <h2 class="section-title">
            Evaluasi Penggunaan BBM Berdasarkan Kendaraan
        </h2>


        <table class="data">

            <thead>

                <tr>

                    <th>No</th>

                    <th>Kendaraan</th>

                    <th>Jenis Kendaraan</th>

                    <th>Transaksi</th>

                    <th>KM Sebelumnya</th>

                    <th>KM Sekarang</th>

                    <th>Jarak</th>

                    <th>Liter</th>

                    <th>Rasio Aktual</th>

                    <th>Standar</th>

                    <th>Evaluasi</th>

                    <th>Pemborosan</th>

                    <th>Biaya Kerugian</th>

                </tr>

            </thead>


            <tbody>

                @forelse ($perKendaraan as $data)

                    <tr>

                        <td class="text-center">
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $data['kendaraan'] }}
                        </td>

                        <td>
                            {{ $data['jenis_kendaraan'] }}
                        </td>

                        <td class="text-center">
                            {{ $data['total_transaksi'] }}
                        </td>

                        <td class="text-right">

                            @if ($data['km_sebelumnya'] !== null)

                                {{ number_format(
                                    (float) $data['km_sebelumnya'],
                                    0,
                                    ',',
                                    '.'
                                ) }}
                                km

                            @else

                                -

                            @endif

                        </td>

                        <td class="text-right">

                            {{ number_format(
                                (float) $data['km_sekarang'],
                                0,
                                ',',
                                '.'
                            ) }}
                            km

                        </td>

                        <td class="text-right">

                            @if ($data['total_jarak'] !== null)

                                {{ number_format(
                                    (float) $data['total_jarak'],
                                    0,
                                    ',',
                                    '.'
                                ) }}
                                km

                            @else

                                -

                            @endif

                        </td>

                        <td class="text-right">

                            {{ number_format(
                                (float) $data['total_liter'],
                                2,
                                ',',
                                '.'
                            ) }}
                            L

                        </td>

                        <td class="text-right">

                            @if ($data['rasio_aktual'] !== null)

                                {{ number_format(
                                    (float) $data['rasio_aktual'],
                                    2,
                                    ',',
                                    '.'
                                ) }}
                                km/L

                            @else

                                -

                            @endif

                        </td>

                        <td class="text-right">

                            @if ($data['rasio_standar'] !== null)

                                {{ number_format(
                                    (float) $data['rasio_standar'],
                                    2,
                                    ',',
                                    '.'
                                ) }}
                                km/L

                            @else

                                -

                            @endif

                        </td>

                        <td class="text-center">

                            @if ($data['evaluasi'] === 'Wajar')

                                <span class="wajar">
                                    Wajar
                                </span>

                            @elseif ($data['evaluasi'] === 'Tidak Wajar')

                                <span class="tidak-wajar">
                                    Tidak Wajar
                                </span>

                            @else

                                <span class="belum">
                                    Belum Dievaluasi
                                </span>

                            @endif

                        </td>

                        <td
                            class="text-right {{ ($data['varian_pemborosan'] ?? 0) > 0 ? 'danger' : '' }}"
                        >

                            {{ number_format(
                                (float) ($data['varian_pemborosan'] ?? 0),
                                2,
                                ',',
                                '.'
                            ) }}
                            L

                        </td>

                        <td
                            class="text-right {{ ($data['biaya_kerugian'] ?? 0) > 0 ? 'danger' : '' }}"
                        >

                            Rp
                            {{ number_format(
                                (float) ($data['biaya_kerugian'] ?? 0),
                                0,
                                ',',
                                '.'
                            ) }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="13"
                            class="text-center"
                        >
                            Tidak ada data transaksi.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <div class="section">

        <h2 class="section-title">
            Ringkasan Berdasarkan Departemen
        </h2>


        <table class="data">

            <thead>

                <tr>

                    <th>No</th>

                    <th>Departemen</th>

                    <th>Transaksi</th>

                    <th>Liter</th>

                    <th>Total Biaya</th>

                </tr>

            </thead>


            <tbody>

                @forelse ($perDepartemen as $departemen => $data)

                    <tr>

                        <td class="text-center">
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $departemen }}
                        </td>

                        <td class="text-center">
                            {{ $data['total_transaksi'] }}
                        </td>

                        <td class="text-right">

                            {{ number_format(
                                (float) $data['total_liter'],
                                2,
                                ',',
                                '.'
                            ) }}
                            L

                        </td>

                        <td class="text-right">

                            Rp
                            {{ number_format(
                                (float) $data['total_biaya'],
                                0,
                                ',',
                                '.'
                            ) }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center"
                        >
                            Tidak ada data.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <div class="section">

        <h2 class="section-title">
            Ringkasan Berdasarkan Jenis BBM
        </h2>


        <table class="data">

            <thead>

                <tr>

                    <th>No</th>

                    <th>Jenis BBM</th>

                    <th>Transaksi</th>

                    <th>Liter</th>

                    <th>Total Biaya</th>

                </tr>

            </thead>


            <tbody>

                @forelse ($perBbm as $jenisBbm => $data)

                    <tr>

                        <td class="text-center">
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $jenisBbm }}
                        </td>

                        <td class="text-center">
                            {{ $data['total_transaksi'] }}
                        </td>

                        <td class="text-right">

                            {{ number_format(
                                (float) $data['total_liter'],
                                2,
                                ',',
                                '.'
                            ) }}
                            L

                        </td>

                        <td class="text-right">

                            Rp
                            {{ number_format(
                                (float) $data['total_biaya'],
                                0,
                                ',',
                                '.'
                            ) }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center"
                        >
                            Tidak ada data.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <div class="footer">

        FuelVision - Monitoring Bulanan BBM

    </div>

</body>
</html>