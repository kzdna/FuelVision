@extends('layouts.app')

@section('title', 'Dashboard Monitoring BBM')

@section('content')

<div class="mb-4">

    <h1 class="mb-2">
        Dashboard Monitoring BBM
    </h1>

    <p class="text-muted mb-0">
        Monitoring penggunaan BBM FuelVision.
    </p>

</div>


<div class="row g-4 mb-4">

    <div class="col-xl-3 col-md-6">

        <div class="card h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <div class="text-muted mb-2">
                            Total Transaksi
                        </div>

                        <h2 class="mb-0">
                            {{ $totalTransaksi }}
                        </h2>

                    </div>

                    <div class="text-primary fs-3">
                        <i class="bi bi-receipt"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-xl-3 col-md-6">

        <div class="card h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <div class="text-muted mb-2">
                            Total Liter
                        </div>

                        <h2 class="mb-0">
                            {{ number_format((float) $totalLiter, 2, ',', '.') }} L
                        </h2>

                    </div>

                    <div class="text-primary fs-3">
                        <i class="bi bi-fuel-pump"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-xl-3 col-md-6">

        <div class="card h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <div class="text-muted mb-2">
                            Total Biaya
                        </div>

                        <h2 class="mb-0">
                            Rp {{ number_format((float) $totalBiaya, 0, ',', '.') }}
                        </h2>

                    </div>

                    <div class="text-primary fs-3">
                        <i class="bi bi-cash-stack"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-xl-3 col-md-6">

        <div class="card h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <div class="text-muted mb-2">
                            Rata-rata Liter
                        </div>

                        <h2 class="mb-0">
                            {{ number_format((float) $rataRataLiter, 2, ',', '.') }} L
                        </h2>

                    </div>

                    <div class="text-primary fs-3">
                        <i class="bi bi-bar-chart"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<div class="row g-4 mb-4">

    <div class="col-lg-7">

        <div class="card h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start mb-3">

                    <div>

                        <h5 class="mb-1">
                            Penggunaan Berdasarkan Jenis Kendaraan
                        </h5>

                        <p class="text-muted mb-0 small">
                            Total penggunaan BBM berdasarkan jenis kendaraan.
                        </p>

                    </div>

                    <span class="badge bg-light text-dark border">
                        Liter
                    </span>

                </div>

                <div style="height: 300px;">
                    <canvas id="jenisKendaraanChart"></canvas>
                </div>

            </div>

        </div>

    </div>


    <div class="col-lg-5">

        <div class="card h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start mb-3">

                    <div>

                        <h5 class="mb-1">
                            Penggunaan Tertinggi
                        </h5>

                        <p class="text-muted mb-0 small">
                            Kendaraan dengan total penggunaan BBM terbesar.
                        </p>

                    </div>

                    <span class="badge bg-light text-dark border">
                        Tertinggi
                    </span>

                </div>


                @if ($kendaraanTertinggi)

                    <div class="border rounded p-3">

                        <div class="text-muted small mb-1">
                            Kendaraan
                        </div>

                        <h4 class="mb-1">
                            {{ $kendaraanTertinggi['kode_unit'] }}
                        </h4>

                        <div class="text-muted mb-3">
                            {{ $kendaraanTertinggi['jenis_kendaraan'] }}
                        </div>


                        <div class="row g-3">

                            <div class="col-6">

                                <div class="text-muted small mb-1">
                                    Total Liter
                                </div>

                                <strong>
                                    {{ number_format((float) $kendaraanTertinggi['total_liter'], 2, ',', '.') }}
                                    L
                                </strong>

                            </div>


                            <div class="col-6">

                                <div class="text-muted small mb-1">
                                    Transaksi
                                </div>

                                <strong>
                                    {{ $kendaraanTertinggi['total_transaksi'] }}
                                </strong>

                            </div>

                        </div>


                        <hr>


                        <div class="text-muted small mb-1">
                            Total Biaya
                        </div>

                        <h5 class="mb-0">
                            Rp {{ number_format((float) $kendaraanTertinggi['total_biaya'], 0, ',', '.') }}
                        </h5>

                    </div>

                @else

                    <div class="text-center text-muted py-5">

                        <i
                            class="bi bi-bar-chart"
                            style="font-size: 2rem;"
                        ></i>

                        <div class="mt-2">
                            Belum ada data penggunaan kendaraan.
                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>


<div class="card fv-table-card mb-4">

    <div class="card-body p-0">

        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">

            <div>

                <h5 class="mb-1">
                    Ringkasan Penggunaan Kendaraan
                </h5>

                <p class="text-muted mb-0 small">
                    Perbandingan penggunaan BBM setiap kendaraan.
                </p>

            </div>

            <span class="badge bg-light text-dark border">
                {{ count($perKendaraan) }} Kendaraan
            </span>

        </div>


        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0 fv-table">

                <thead>

                    <tr>

                        <th class="ps-4">
                            No
                        </th>

                        <th>
                            Kode Unit
                        </th>

                        <th>
                            Jenis Kendaraan
                        </th>

                        <th>
                            Transaksi
                        </th>

                        <th>
                            Total Liter
                        </th>

                        <th>
                            Total Biaya
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($perKendaraan as $data)

                        <tr>

                            <td class="ps-4 fw-semibold">
                                {{ $loop->iteration }}
                            </td>

                            <td class="fw-semibold">
                                {{ $data['kode_unit'] }}
                            </td>

                            <td>
                                {{ $data['jenis_kendaraan'] }}
                            </td>

                            <td>
                                {{ $data['total_transaksi'] }}
                            </td>

                            <td>
                                {{ number_format((float) $data['total_liter'], 2, ',', '.') }} L
                            </td>

                            <td>
                                Rp {{ number_format((float) $data['total_biaya'], 0, ',', '.') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-5"
                            >

                                <div class="text-muted">

                                    <i
                                        class="bi bi-bar-chart"
                                        style="font-size: 2rem;"
                                    ></i>

                                    <div class="mt-2">
                                        Belum ada data penggunaan kendaraan.
                                    </div>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


<div class="card fv-table-card">

    <div class="card-body p-0">

        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">

            <div>

                <h5 class="mb-1">
                    Transaksi Terbaru
                </h5>

                <p class="text-muted mb-0 small">
                    Lima transaksi pengisian BBM terakhir.
                </p>

            </div>

            <a
                href="{{ route('transaksi-pengisian-bbm.index') }}"
                class="btn btn-primary btn-sm"
            >
                <i class="bi bi-list-ul me-1"></i>
                Lihat Semua
            </a>

        </div>


        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0 fv-table">

                <thead>

                    <tr>

                        <th class="ps-4">
                            No
                        </th>

                        <th>
                            Tanggal
                        </th>

                        <th>
                            Kendaraan
                        </th>

                        <th>
                            Driver
                        </th>

                        <th>
                            BBM
                        </th>

                        <th>
                            Liter
                        </th>

                        <th>
                            Total Biaya
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($transaksiTerbaru as $item)

                        <tr>

                            <td class="ps-4 fw-semibold">
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $item->tanggal_pengisian->format('d/m/Y H:i') }}
                            </td>

                            <td>

                                @if ($item->kendaraanOperasional)

                                    <span class="fw-semibold">
                                        {{ $item->kendaraanOperasional->kode_unit }}
                                    </span>

                                @elseif ($item->kendaraanGs)

                                    <span class="fw-semibold">
                                        {{ $item->kendaraanGs->kode_gs }}
                                    </span>

                                    <br>

                                    <small class="text-muted">
                                        Kendaraan GS
                                    </small>

                                @else

                                    -

                                @endif

                            </td>

                            <td>
                                {{ $item->driver }}
                            </td>

                            <td>
                                {{ $item->masterHargaBbmVendor->jenis_bbm ?? '-' }}
                            </td>

                            <td>
                                {{ number_format((float) $item->jumlah_liter, 2, ',', '.') }} L
                            </td>

                            <td>
                                Rp {{ number_format((float) $item->total_biaya, 0, ',', '.') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-5"
                            >

                                <div class="text-muted">

                                    <i
                                        class="bi bi-receipt"
                                        style="font-size: 2rem;"
                                    ></i>

                                    <div class="mt-2">
                                        Belum ada transaksi pengisian BBM.
                                    </div>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const jenisKendaraanLabels =
    @json($perJenisKendaraan->keys()->values());

const jenisKendaraanData =
    @json(
        $perJenisKendaraan->pluck('total_liter')->values()
    );

new Chart(
    document.getElementById('jenisKendaraanChart'),
    {
        type: 'bar',

        data: {
            labels: jenisKendaraanLabels,

            datasets: [
                {
                    label: 'Total Liter',
                    data: jenisKendaraanData,
                    borderWidth: 1
                }
            ]
        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {
                    display: false
                }

            },

            scales: {

                y: {
                    beginAtZero: true,

                    title: {
                        display: true,
                        text: 'Liter'
                    }
                }

            }

        }

    }
);

</script>

@endpush