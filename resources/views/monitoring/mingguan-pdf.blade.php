<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>
        Summary Monitoring BBM
    </title>

    <style>
        @page {
            margin: 30px 35px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
        }

        .period {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .period table {
            width: 100%;
            border-collapse: collapse;
        }

        .period td {
            padding: 3px 5px;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .summary td {
            width: 25%;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .summary-label {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 8px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.data th {
            background: #f1f3f5;
            border: 1px solid #ccc;
            padding: 7px;
            text-align: left;
        }

        table.data td {
            border: 1px solid #ccc;
            padding: 7px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .ai-summary {
            border: 1px solid #ddd;
            padding: 12px;
            line-height: 1.6;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 8px;
            color: #777;
        }
    </style>
</head>

<body>

<div class="header">

    <h1>
        FUELVISION
    </h1>

    <p>
        Summary Monitoring BBM
    </p>

</div>

<div class="period">

    <table>

        <tr>
            <td width="25%">
                <strong>Periode</strong>
            </td>

            <td>
                {{ $awalMinggu->format('d/m/Y') }}
                -
                {{ $akhirMinggu->format('d/m/Y') }}
            </td>
        </tr>

        <tr>
            <td>
                <strong>Kendaraan</strong>
            </td>

            <td>
                @if ($kendaraanFilter === 'all')
                    Semua Kendaraan
                @else
                    {{ $kendaraanFilter }}
                @endif
            </td>
        </tr>

        <tr>
            <td>
                <strong>Jenis BBM</strong>
            </td>

            <td>
                @if ($jenisBbmFilter === 'all')
                    Semua Jenis BBM
                @else
                    {{ $jenisBbmFilter }}
                @endif
            </td>
        </tr>

    </table>

</div>

<div class="section-title">
    Ringkasan Utama
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
                Total BBM
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
                Rata-rata / Transaksi
            </div>

            <div class="summary-value">
                {{ number_format((float) $rataRataLiter, 2, ',', '.') }} L
            </div>

        </td>

    </tr>

</table>

<div class="section-title">
    Perbandingan Penggunaan BBM per Kendaraan
</div>

<table class="data">

    <thead>

        <tr>

            <th width="5%" class="text-center">
                No
            </th>

            <th>
                Kendaraan
            </th>

            <th>
                Jenis Kendaraan
            </th>

            <th class="text-center">
                Transaksi
            </th>

            <th class="text-right">
                Total Liter
            </th>

            <th class="text-right">
                Total Biaya
            </th>

            <th class="text-right">
                Persentase
            </th>

        </tr>

    </thead>

    <tbody>

        @forelse ($perKendaraan as $item)

            <tr>

                <td class="text-center">
                    {{ $loop->iteration }}
                </td>

                <td>
                    {{ $item['kendaraan'] }}
                </td>

                <td>
                    {{ $item['jenis_kendaraan'] }}
                </td>

                <td class="text-center">
                    {{ $item['total_transaksi'] }}
                </td>

                <td class="text-right">
                    {{ number_format((float) $item['total_liter'], 2, ',', '.') }}
                    L
                </td>

                <td class="text-right">
                    Rp {{ number_format((float) $item['total_biaya'], 0, ',', '.') }}
                </td>

                <td class="text-right">
                    {{ number_format((float) $item['persentase'], 2, ',', '.') }}%
                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="7"
                    class="text-center"
                >
                    Tidak ada data kendaraan.
                </td>

            </tr>

        @endforelse

    </tbody>

</table>

<div class="section-title">
    Penggunaan Berdasarkan Jenis BBM
</div>

<table class="data">

    <thead>

        <tr>

            <th>
                Jenis BBM
            </th>

            <th class="text-center">
                Transaksi
            </th>

            <th class="text-right">
                Total Liter
            </th>

            <th class="text-right">
                Total Biaya
            </th>

        </tr>

    </thead>

    <tbody>

        @forelse ($perBbm as $jenis => $item)

            <tr>

                <td>
                    {{ $jenis }}
                </td>

                <td class="text-center">
                    {{ $item['total_transaksi'] }}
                </td>

                <td class="text-right">
                    {{ number_format((float) $item['total_liter'], 2, ',', '.') }}
                    L
                </td>

                <td class="text-right">
                    Rp {{ number_format((float) $item['total_biaya'], 0, ',', '.') }}
                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="4"
                    class="text-center"
                >
                    Tidak ada data BBM.
                </td>

            </tr>

        @endforelse

    </tbody>

</table>

<div class="section-title">
    Penggunaan Berdasarkan Departemen
</div>

<table class="data">

    <thead>

        <tr>

            <th>
                Departemen
            </th>

            <th class="text-center">
                Transaksi
            </th>

            <th class="text-right">
                Total Liter
            </th>

            <th class="text-right">
                Total Biaya
            </th>

        </tr>

    </thead>

    <tbody>

        @forelse ($perDepartemen as $departemen => $item)

            <tr>

                <td>
                    {{ $departemen }}
                </td>

                <td class="text-center">
                    {{ $item['total_transaksi'] }}
                </td>

                <td class="text-right">
                    {{ number_format((float) $item['total_liter'], 2, ',', '.') }}
                    L
                </td>

                <td class="text-right">
                    Rp {{ number_format((float) $item['total_biaya'], 0, ',', '.') }}
                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="4"
                    class="text-center"
                >
                    Tidak ada data departemen.
                </td>

            </tr>

        @endforelse

    </tbody>

</table>

<div class="section-title">
    Penggunaan Berdasarkan Cost Center
</div>

<table class="data">

    <thead>

        <tr>

            <th>
                Cost Center
            </th>

            <th class="text-center">
                Transaksi
            </th>

            <th class="text-right">
                Total Liter
            </th>

            <th class="text-right">
                Total Biaya
            </th>

        </tr>

    </thead>

    <tbody>

        @forelse ($perCostCenter as $costCenter => $item)

            <tr>

                <td>
                    {{ $costCenter }}
                </td>

                <td class="text-center">
                    {{ $item['total_transaksi'] }}
                </td>

                <td class="text-right">
                    {{ number_format((float) $item['total_liter'], 2, ',', '.') }}
                    L
                </td>

                <td class="text-right">
                    Rp {{ number_format((float) $item['total_biaya'], 0, ',', '.') }}
                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="4"
                    class="text-center"
                >
                    Tidak ada data cost center.
                </td>

            </tr>

        @endforelse

    </tbody>

</table>

<div class="section-title">
    AI Summary
</div>

<div class="ai-summary">

    @if ($aiSummary)

        {!! nl2br(e($aiSummary)) !!}

    @elseif ($aiError)

        {{ $aiError }}

    @else

        AI Summary belum tersedia.

    @endif

</div>

<div class="footer">

    FuelVision - Summary Monitoring BBM

</div>

</body>
</html>