@extends('layouts.app')

@section('title', 'Monitoring Bulanan BBM')

@section('content')

@php
    $jumlahWajar = collect($perKendaraan)
        ->where('evaluasi', 'Wajar')
        ->count();

    $jumlahTidakWajar = collect($perKendaraan)
        ->where('evaluasi', 'Tidak Wajar')
        ->count();

    $jumlahBelumDievaluasi = collect($perKendaraan)
        ->where('evaluasi', 'Belum Dievaluasi')
        ->count();

    $totalPemborosan = collect($perKendaraan)
        ->sum(function ($item) {
            return (float) ($item['varian_pemborosan'] ?? 0);
        });

    $totalBiayaKerugian = collect($perKendaraan)
        ->sum(function ($item) {
            return (float) ($item['biaya_kerugian'] ?? 0);
        });
@endphp

<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">

    <div>

        <h1 class="mb-2">
            Monitoring Bulanan BBM
        </h1>

        <p class="text-muted mb-0">
            Ringkasan penggunaan dan evaluasi BBM pada bulan berjalan.
        </p>

    </div>

    <div class="d-flex gap-2 flex-wrap">

        <a
            href="{{ route('monitoring.bulanan.pdf', [
                'tanggal_mulai' => $awalBulan->format('Y-m-d'),
                'kendaraan' => $kendaraanFilter,
                'jenis_bbm' => $jenisBbmFilter,
            ]) }}"
            class="btn btn-primary"
            target="_blank"
        >
            <i class="bi bi-file-earmark-pdf me-1"></i>
            Download Summary PDF
        </a>

    </div>

</div>


<div class="card mb-4">

    <div class="card-body">

        <form
            method="GET"
            action="{{ route('monitoring.bulanan') }}"
        >

            <div class="row g-3 align-items-end">

                <div class="col-lg-4 col-md-6">

                    <label
                        for="tanggal_mulai"
                        class="form-label"
                    >
                        Bulan
                    </label>

                    <input
                        type="month"
                        name="tanggal_mulai"
                        id="tanggal_mulai"
                        class="form-control"
                        value="{{ $awalBulan->format('Y-m') }}"
                    >

                    <div class="form-text">
                        Pilih bulan yang ingin dimonitor.
                    </div>

                </div>


                <div class="col-lg-4 col-md-6">

                    <label
                        for="kendaraan"
                        class="form-label"
                    >
                        Kendaraan
                    </label>

                    <select
                        name="kendaraan"
                        id="kendaraan"
                        class="form-select"
                    >

                        <option value="all">
                            Semua Kendaraan
                        </option>

                        @if ($kendaraanOperasional->count())

                            <optgroup label="Kendaraan Operasional">

                                @foreach ($kendaraanOperasional as $item)

                                    <option
                                        value="operasional:{{ $item->id }}"
                                        {{ $kendaraanFilter === 'operasional:' . $item->id ? 'selected' : '' }}
                                    >
                                        {{ $item->kode_unit }}
                                        -
                                        {{ $item->jenis_kendaraan }}
                                    </option>

                                @endforeach

                            </optgroup>

                        @endif

                        @if ($kendaraanGs->count())

                            <optgroup label="Kendaraan GS">

                                @foreach ($kendaraanGs as $item)

                                    <option
                                        value="gs:{{ $item->id }}"
                                        {{ $kendaraanFilter === 'gs:' . $item->id ? 'selected' : '' }}
                                    >
                                        {{ $item->kode_gs }}
                                    </option>

                                @endforeach

                            </optgroup>

                        @endif

                    </select>

                </div>


                <div class="col-lg-3 col-md-6">

                    <label
                        for="jenis_bbm"
                        class="form-label"
                    >
                        Jenis BBM
                    </label>

                    <select
                        name="jenis_bbm"
                        id="jenis_bbm"
                        class="form-select"
                    >

                        <option value="all">
                            Semua Jenis BBM
                        </option>

                        @foreach ($hargaBbm as $item)

                            <option
                                value="{{ $item->id }}"
                                {{ (string) $jenisBbmFilter === (string) $item->id ? 'selected' : '' }}
                            >
                                {{ $item->jenis_bbm }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-lg-1 col-md-6">

                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                    >
                        <i class="bi bi-funnel me-1"></i>
                        Filter
                    </button>

                </div>

            </div>

        </form>

    </div>

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
                            {{ number_format((float) $totalLiter, 2, ',', '.') }}
                            L
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
                            Rp
                            {{ number_format((float) $totalBiaya, 0, ',', '.') }}
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
                            Rata-rata per Transaksi
                        </div>

                        <h2 class="mb-0">
                            {{ number_format((float) $rataRataLiter, 2, ',', '.') }}
                            L
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

    <div class="col-xl-3 col-md-6">

        <div class="card h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <div class="text-muted mb-2">
                            Kendaraan Wajar
                        </div>

                        <h2 class="mb-0 text-success">
                            {{ $jumlahWajar }}
                        </h2>

                    </div>

                    <div class="text-success fs-3">
                        <i class="bi bi-check-circle"></i>
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
                            Kendaraan Tidak Wajar
                        </div>

                        <h2 class="mb-0 text-danger">
                            {{ $jumlahTidakWajar }}
                        </h2>

                    </div>

                    <div class="text-danger fs-3">
                        <i class="bi bi-exclamation-triangle"></i>
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
                            Total Pemborosan
                        </div>

                        <h2 class="mb-0 text-danger">
                            {{ number_format($totalPemborosan, 2, ',', '.') }}
                            L
                        </h2>

                    </div>

                    <div class="text-danger fs-3">
                        <i class="bi bi-fuel-pump-fill"></i>
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
                            Biaya Kerugian
                        </div>

                        <h2 class="mb-0 text-danger">
                            Rp
                            {{ number_format($totalBiayaKerugian, 0, ',', '.') }}
                        </h2>

                    </div>

                    <div class="text-danger fs-3">
                        <i class="bi bi-cash-stack"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<div class="card fv-table-card mb-4">

    <div class="card-body p-0">

        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">

            <div>

                <h5 class="mb-1">
                    Monitoring Per Kendaraan
                </h5>

                <p class="text-muted mb-0 small">
                    Evaluasi penggunaan BBM berdasarkan kendaraan pada bulan yang dipilih.
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
                            Kendaraan
                        </th>

                        <th>
                            Jenis Kendaraan
                        </th>

                        <th>
                            Transaksi
                        </th>

                        <th>
                            KM Sebelumnya
                        </th>

                        <th>
                            KM Sekarang
                        </th>

                        <th>
                            Total Jarak
                        </th>

                        <th>
                            Total Liter
                        </th>

                        <th>
                            Rasio Aktual
                        </th>

                        <th>
                            Rasio Standar
                        </th>

                        <th>
                            Evaluasi
                        </th>

                        <th>
                            Persentase
                        </th>

                        <th>
                            Varian Pemborosan
                        </th>

                        <th>
                            Biaya Kerugian
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
                                {{ $data['kendaraan'] }}
                            </td>

                            <td>
                                {{ $data['jenis_kendaraan'] }}
                            </td>

                            <td>
                                {{ $data['total_transaksi'] }}
                            </td>

                            <td>
                                @if ($data['km_sebelumnya'] !== null)
                                    {{ number_format((float) $data['km_sebelumnya'], 0, ',', '.') }}
                                    km
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                @if ($data['km_sekarang'] !== null)
                                    {{ number_format((float) $data['km_sekarang'], 0, ',', '.') }}
                                    km
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                @if ($data['total_jarak'] !== null)
                                    {{ number_format((float) $data['total_jarak'], 0, ',', '.') }}
                                    km
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                {{ number_format((float) $data['total_liter'], 2, ',', '.') }}
                                L
                            </td>

                            <td>
                                @if ($data['rasio_aktual'] !== null)
                                    {{ number_format((float) $data['rasio_aktual'], 2, ',', '.') }}
                                    km/L
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                @if ($data['rasio_standar'] !== null)
                                    {{ number_format((float) $data['rasio_standar'], 2, ',', '.') }}
                                    km/L
                                @else
                                    -
                                @endif
                            </td>

                            <td>

                                @if ($data['evaluasi'] === 'Wajar')

                                    <span class="badge bg-success">
                                        Wajar
                                    </span>

                                @elseif ($data['evaluasi'] === 'Tidak Wajar')

                                    <span class="badge bg-danger">
                                        Tidak Wajar
                                    </span>

                                @elseif ($data['evaluasi'] === 'Belum Dievaluasi')

                                    <span class="badge bg-warning text-dark">
                                        Belum Dievaluasi
                                    </span>

                                @else

                                    -

                                @endif

                            </td>

                            <td>
                                {{ number_format((float) ($data['persentase'] ?? 0), 2, ',', '.') }}
                                %
                            </td>

                            <td>
                                {{ number_format((float) ($data['varian_pemborosan'] ?? 0), 2, ',', '.') }}
                                L
                            </td>

                            <td>
                                Rp
                                {{ number_format((float) ($data['biaya_kerugian'] ?? 0), 0, ',', '.') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="14"
                                class="text-center py-5"
                            >

                                <div class="text-muted">
                                    Tidak ada data kendaraan sesuai filter.
                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


<div class="card fv-table-card mb-4">

    <div class="card-body p-0">

        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">

            <div>

                <h5 class="mb-1">
                    Penggunaan Berdasarkan Jenis Kendaraan
                </h5>

                <p class="text-muted mb-0 small">
                    Ringkasan penggunaan BBM berdasarkan jenis kendaraan.
                </p>

            </div>

            <span class="badge bg-light text-dark border">
                {{ count($perJenisKendaraan) }} Jenis
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
                            Jenis Kendaraan
                        </th>

                        <th>
                            Transaksi
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

                    @forelse ($perJenisKendaraan as $jenisKendaraan => $data)

                        <tr>

                            <td class="ps-4 fw-semibold">
                                {{ $loop->iteration }}
                            </td>

                            <td class="fw-semibold">
                                {{ $jenisKendaraan }}
                            </td>

                            <td>
                                {{ $data['total_transaksi'] }}
                            </td>

                            <td>
                                {{ number_format((float) $data['total_liter'], 2, ',', '.') }}
                                L
                            </td>

                            <td>
                                Rp
                                {{ number_format((float) $data['total_biaya'], 0, ',', '.') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center py-5"
                            >
                                Tidak ada data.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


<div class="card fv-table-card mb-4">

    <div class="card-body p-0">

        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">

            <div>

                <h5 class="mb-1">
                    Penggunaan Berdasarkan Jenis BBM
                </h5>

                <p class="text-muted mb-0 small">
                    Ringkasan penggunaan setiap jenis BBM.
                </p>

            </div>

            <span class="badge bg-light text-dark border">
                {{ count($perBbm) }} Jenis
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
                            Jenis BBM
                        </th>

                        <th>
                            Transaksi
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

                    @forelse ($perBbm as $jenisBbm => $data)

                        <tr>

                            <td class="ps-4 fw-semibold">
                                {{ $loop->iteration }}
                            </td>

                            <td class="fw-semibold">
                                {{ $jenisBbm }}
                            </td>

                            <td>
                                {{ $data['total_transaksi'] }}
                            </td>

                            <td>
                                {{ number_format((float) $data['total_liter'], 2, ',', '.') }}
                                L
                            </td>

                            <td>
                                Rp
                                {{ number_format((float) $data['total_biaya'], 0, ',', '.') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center py-5"
                            >
                                Tidak ada data.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


<div class="row g-4 mb-4">

    <div class="col-lg-6">

        <div class="card fv-table-card h-100">

            <div class="card-body p-0">

                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">

                    <div>

                        <h5 class="mb-1">
                            Penggunaan Berdasarkan Departemen
                        </h5>

                        <p class="text-muted mb-0 small">
                            Ringkasan penggunaan BBM setiap departemen.
                        </p>

                    </div>

                    <span class="badge bg-light text-dark border">
                        {{ count($perDepartemen) }} Departemen
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
                                    Departemen
                                </th>

                                <th>
                                    Transaksi
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

                            @forelse ($perDepartemen as $departemen => $data)

                                <tr>

                                    <td class="ps-4 fw-semibold">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="fw-semibold">
                                        {{ $departemen }}
                                    </td>

                                    <td>
                                        {{ $data['total_transaksi'] }}
                                    </td>

                                    <td>
                                        {{ number_format((float) $data['total_liter'], 2, ',', '.') }}
                                        L
                                    </td>

                                    <td>
                                        Rp
                                        {{ number_format((float) $data['total_biaya'], 0, ',', '.') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="5"
                                        class="text-center py-5"
                                    >

                                        <div class="text-muted">
                                            Tidak ada transaksi sesuai filter.
                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>


    <div class="col-lg-6">

        <div class="card fv-table-card h-100">

            <div class="card-body p-0">

                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">

                    <div>

                        <h5 class="mb-1">
                            Penggunaan Berdasarkan Cost Center
                        </h5>

                        <p class="text-muted mb-0 small">
                            Ringkasan penggunaan BBM berdasarkan cost center.
                        </p>

                    </div>

                    <span class="badge bg-light text-dark border">
                        {{ count($perCostCenter) }} Cost Center
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
                                    Cost Center
                                </th>

                                <th>
                                    Transaksi
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

                            @forelse ($perCostCenter as $costCenter => $data)

                                <tr>

                                    <td class="ps-4 fw-semibold">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="fw-semibold">
                                        {{ $costCenter }}
                                    </td>

                                    <td>
                                        {{ $data['total_transaksi'] }}
                                    </td>

                                    <td>
                                        {{ number_format((float) $data['total_liter'], 2, ',', '.') }}
                                        L
                                    </td>

                                    <td>
                                        Rp
                                        {{ number_format((float) $data['total_biaya'], 0, ',', '.') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="5"
                                        class="text-center py-5"
                                    >

                                        <div class="text-muted">
                                            Tidak ada transaksi sesuai filter.
                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>


<div class="card fv-table-card mb-4">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h5 class="mb-1">
                    AI Summary Bulanan
                </h5>

                <p class="text-muted mb-0 small">
                    Ringkasan analisis penggunaan BBM selama bulan yang dipilih.
                </p>

            </div>

            <span class="badge bg-light text-dark border">
                AI Insight
            </span>

        </div>


        @if ($totalTransaksi <= 0)

            <div class="text-center py-4">

                <div class="text-muted">

                    <i
                        class="bi bi-stars"
                        style="font-size: 2rem;"
                    ></i>

                    <div class="mt-2">
                        Belum ada data transaksi untuk dianalisis.
                    </div>

                </div>

            </div>

        @elseif ($aiError)

            <div class="alert alert-warning mb-0">

                <div class="d-flex align-items-start gap-2">

                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>

                    <div>

                        <div class="fw-semibold mb-1">
                            AI Summary belum tersedia
                        </div>

                        <div>
                            {{ $aiError }}
                        </div>

                    </div>

                </div>

            </div>

        @elseif ($aiSummary)

            <div class="bg-light rounded p-4">

                <div class="d-flex align-items-start gap-2">

                    <i class="bi bi-stars text-primary mt-1"></i>

                    <div class="flex-grow-1">

                        {!! nl2br(e($aiSummary)) !!}

                    </div>

                </div>

            </div>

        @else

            <div class="text-center py-4">

                <div class="text-muted">

                    <i
                        class="bi bi-stars"
                        style="font-size: 2rem;"
                    ></i>

                    <div class="mt-2">
                        AI Summary belum tersedia.
                    </div>

                </div>

            </div>

        @endif

    </div>

</div>


<div class="card fv-table-card">

    <div class="card-body p-0">

        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">

            <div>

                <h5 class="mb-1">
                    Detail Transaksi Bulan Ini
                </h5>

                <p class="text-muted mb-0 small">
                    Daftar seluruh transaksi pengisian BBM sesuai filter.
                </p>

            </div>

            <span class="badge bg-light text-dark border">
                {{ count($transaksi) }} Transaksi
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
                            Tanggal
                        </th>

                        <th>
                            Kendaraan
                        </th>

                        <th>
                            GS
                        </th>

                        <th>
                            Jenis Kendaraan
                        </th>

                        <th>
                            Driver
                        </th>

                        <th>
                            BBM
                        </th>

                        <th>
                            Kilometer
                        </th>

                        <th>
                            Liter
                        </th>

                        <th>
                            Total Biaya
                        </th>

                        <th>
                            Departemen
                        </th>

                        <th>
                            Cost Center
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($transaksi as $item)

                        <tr>

                            <td class="ps-4 fw-semibold">
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($item->tanggal_pengisian)->format('d/m/Y') }}
                            </td>

                            <td>

                                @if ($item->kendaraanOperasional)

                                    <span class="fw-semibold">
                                        {{ $item->kendaraanOperasional->kode_unit }}
                                    </span>

                                @else

                                    -

                                @endif

                            </td>

                            <td>

                                @if ($item->kendaraanGs)

                                    <span class="fw-semibold">
                                        {{ $item->kendaraanGs->kode_gs }}
                                    </span>

                                @else

                                    -

                                @endif

                            </td>

                            <td>

                                @if ($item->kendaraanGs)

                                    Kendaraan GS

                                @elseif ($item->kendaraanOperasional)

                                    {{ $item->kendaraanOperasional->jenis_kendaraan }}

                                @else

                                    -

                                @endif

                            </td>

                            <td>
                                {{ $item->driver }}
                            </td>

                            <td>
                                {{ $item->masterHargaBbmVendor?->jenis_bbm ?? '-' }}
                            </td>

                            <td>
                                {{ number_format((float) $item->kilometer, 0, ',', '.') }}
                                km
                            </td>

                            <td>
                                {{ number_format((float) $item->jumlah_liter, 2, ',', '.') }}
                                L
                            </td>

                            <td>
                                Rp
                                {{ number_format((float) $item->total_biaya, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ $item->departemen_snapshot ?? '-' }}
                            </td>

                            <td>
                                {{ $item->cost_center_snapshot ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="12"
                                class="text-center py-5"
                            >

                                <div class="text-muted">
                                    Tidak ada transaksi sesuai filter.
                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        const button =
            document.getElementById('btnAnalisisAi');

        const icon =
            document.getElementById('btnAnalisisAiIcon');

        const text =
            document.getElementById('btnAnalisisAiText');

        if (!button) {
            return;
        }

        button.addEventListener('click', function () {

            button.disabled = true;

            button.style.cursor = 'wait';

            button.classList.remove('btn-primary');

            button.classList.add('btn-secondary');

            if (icon) {
                icon.className =
                    'spinner-border spinner-border-sm me-2';
            }

            if (text) {
                text.textContent =
                    'Menganalisis...';
            }

            const params =
                new URLSearchParams({
                    tanggal_mulai:
                        '{{ $awalBulan->format('Y-m-d') }}',

                    kendaraan:
                        '{{ $kendaraanFilter }}',

                    jenis_bbm:
                        '{{ $jenisBbmFilter }}'
                });

            const url =
                '{{ route('monitoring.bulanan.ai-insight') }}'
                + '?'
                + params.toString();

            setTimeout(function () {

                window.location.href = url;

            }, 500);

        });

    });
</script>

@endsection