@extends('layouts.app')

@section('title', 'AI Insight BBM')

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
            AI Insight BBM
        </h1>

        <p class="text-muted mb-0">
            Analisis penggunaan BBM berdasarkan data monitoring yang dipilih.
        </p>
    </div>

    <span class="badge bg-primary fs-6 px-3 py-2">
        <i class="bi bi-stars me-1"></i>
        Gemini AI
    </span>

</div>

<div class="card mb-4">

    <div class="card-body">

        <form
            method="GET"
            action="{{ route('ai.insight') }}"
            id="aiInsightForm"
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
                                        - {{ $item->jenis_kendaraan }}
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
                        title="Analisis"
                        id="aiInsightSubmit"
                    >
                        <i
                            class="bi bi-stars"
                            id="aiInsightIcon"
                        ></i>

                        <span
                            class="d-none"
                            id="aiInsightLoading"
                        >
                            Menganalisis...
                        </span>

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<div class="card mb-4">

    <div class="card-body">

        <div class="d-flex align-items-center gap-2 mb-3">

            <i class="bi bi-calendar3 text-primary"></i>

            <div>

                <div class="fw-semibold">
                    Periode Analisis
                </div>

                <div class="text-muted small">

                    {{ $awalBulan->translatedFormat('F Y') }}

                    @if ($kendaraanFilter === 'all')
                        · Semua Kendaraan
                    @else
                        · {{ $kendaraanFilter }}
                    @endif

                    @if ($jenisBbmFilter === 'all')
                        · Semua Jenis BBM
                    @else
                        · Jenis BBM Terpilih
                    @endif

                </div>

            </div>

        </div>

        <div class="row g-3">

            <div class="col-xl-3 col-md-6">

                <div class="border rounded p-3 h-100">

                    <div class="text-muted small mb-1">
                        Total Transaksi
                    </div>

                    <div class="fs-4 fw-semibold">
                        {{ $totalTransaksi }}
                    </div>

                </div>

            </div>

            <div class="col-xl-3 col-md-6">

                <div class="border rounded p-3 h-100">

                    <div class="text-muted small mb-1">
                        Total Liter
                    </div>

                    <div class="fs-4 fw-semibold">
                        {{ number_format((float) $totalLiter, 2, ',', '.') }} L
                    </div>

                </div>

            </div>

            <div class="col-xl-3 col-md-6">

                <div class="border rounded p-3 h-100">

                    <div class="text-muted small mb-1">
                        Total Biaya
                    </div>

                    <div class="fs-4 fw-semibold">
                        Rp {{ number_format((float) $totalBiaya, 0, ',', '.') }}
                    </div>

                </div>

            </div>

            <div class="col-xl-3 col-md-6">

                <div class="border rounded p-3 h-100">

                    <div class="text-muted small mb-1">
                        Rata-rata per Transaksi
                    </div>

                    <div class="fs-4 fw-semibold">
                        {{ number_format((float) $rataRataLiter, 2, ',', '.') }} L
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<div class="row g-4 mb-4">

    <div class="col-md-3">

        <div class="card h-100">

            <div class="card-body">

                <div class="text-muted small mb-2">
                    Kendaraan Wajar
                </div>

                <div class="d-flex justify-content-between align-items-center">

                    <div class="fs-3 fw-semibold text-success">
                        {{ $jumlahWajar }}
                    </div>

                    <i class="bi bi-check-circle text-success fs-3"></i>

                </div>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card h-100">

            <div class="card-body">

                <div class="text-muted small mb-2">
                    Kendaraan Tidak Wajar
                </div>

                <div class="d-flex justify-content-between align-items-center">

                    <div class="fs-3 fw-semibold text-danger">
                        {{ $jumlahTidakWajar }}
                    </div>

                    <i class="bi bi-exclamation-triangle text-danger fs-3"></i>

                </div>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card h-100">

            <div class="card-body">

                <div class="text-muted small mb-2">
                    Total Pemborosan
                </div>

                <div class="d-flex justify-content-between align-items-center">

                    <div class="fs-3 fw-semibold text-danger">
                        {{ number_format($totalPemborosan, 2, ',', '.') }} L
                    </div>

                    <i class="bi bi-fuel-pump-fill text-danger fs-3"></i>

                </div>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card h-100">

            <div class="card-body">

                <div class="text-muted small mb-2">
                    Biaya Kerugian
                </div>

                <div class="d-flex justify-content-between align-items-center">

                    <div class="fs-5 fw-semibold text-danger">
                        Rp {{ number_format($totalBiayaKerugian, 0, ',', '.') }}
                    </div>

                    <i class="bi bi-cash-stack text-danger fs-3"></i>

                </div>

            </div>

        </div>

    </div>

</div>

<div class="card mb-4">

    <div class="card-body">

        <div class="d-flex align-items-start gap-3 mb-4">

            <div
                class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                style="width: 48px; height: 48px;"
            >
                <i class="bi bi-stars fs-4"></i>
            </div>

            <div>

                <h4 class="mb-1">
                    Analisis AI
                </h4>

                <p class="text-muted mb-0">
                    Interpretasi otomatis berdasarkan hasil monitoring BBM.
                </p>

            </div>

        </div>

        @if ($totalTransaksi <= 0)

            <div class="text-center py-5">

                <i
                    class="bi bi-database-x text-muted"
                    style="font-size: 3rem;"
                ></i>

                <h5 class="mt-3">
                    Tidak ada data untuk dianalisis
                </h5>

                <p class="text-muted mb-0">
                    Tidak terdapat transaksi pada periode dan filter yang dipilih.
                </p>

            </div>

        @elseif ($aiError)

            <div class="alert alert-warning mb-0">

                <div class="d-flex align-items-start gap-3">

                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>

                    <div>

                        <div class="fw-semibold mb-1">
                            Analisis AI belum tersedia
                        </div>

                        <div>
                            {{ $aiError }}
                        </div>

                    </div>

                </div>

            </div>

        @elseif ($aiSummary)

            @php
                $insightLines = preg_split('/\r\n|\r|\n/', trim($aiSummary));
            @endphp

            <div class="d-flex flex-column gap-3">

                @foreach ($insightLines as $line)

                    @php
                        $line = trim($line);

                        if ($line === '') {
                            continue;
                        }

                        $title = '';
                        $content = $line;

                        if (preg_match('/^(\d+)\.\s*([^:]+):\s*(.*)$/', $line, $matches)) {
                            $number = $matches[1];
                            $title = trim($matches[2]);
                            $content = trim($matches[3]);
                        } else {
                            $number = null;
                        }
                    @endphp

                    @if ($number)

                        <div class="border rounded p-3 bg-light">

                            <div class="d-flex align-items-start gap-3">

                                <div
                                    class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 36px; height: 36px;"
                                >
                                    <span class="fw-semibold">
                                        {{ $number }}
                                    </span>
                                </div>

                                <div class="flex-grow-1">

                                    <div class="fw-semibold mb-1">
                                        {{ $title }}
                                    </div>

                                    <div class="text-muted lh-lg">
                                        {{ $content }}
                                    </div>

                                </div>

                            </div>

                        </div>

                    @else

                        <div class="border rounded p-3 bg-light">
                            <div class="text-muted lh-lg">
                                {{ $content }}
                            </div>
                        </div>

                    @endif

                @endforeach

            </div>

        @else

            <div class="text-center py-5">

                <i
                    class="bi bi-stars text-muted"
                    style="font-size: 3rem;"
                ></i>

                <h5 class="mt-3">
                    AI Insight belum tersedia
                </h5>

                <p class="text-muted mb-0">
                    Pilih periode dan filter kemudian jalankan analisis.
                </p>

            </div>

        @endif

    </div>

</div>

<div class="card">

    <div class="card-body">

        <div class="d-flex align-items-center gap-2 mb-3">

            <i class="bi bi-lightbulb text-warning"></i>

            <div>

                <h5 class="mb-1">
                    Fokus Perhatian
                </h5>

                <p class="text-muted small mb-0">
                    Indikator yang perlu diperhatikan oleh Finance.
                </p>

            </div>

        </div>

        @if ($jumlahTidakWajar > 0)

            <div class="alert alert-danger d-flex align-items-start gap-2">

                <i class="bi bi-exclamation-triangle-fill mt-1"></i>

                <div>

                    <div class="fw-semibold">
                        Terdapat kendaraan dengan konsumsi tidak wajar.
                    </div>

                    <div class="small">
                        {{ $jumlahTidakWajar }}
                        kendaraan perlu diperiksa berdasarkan standar konsumsi yang telah ditentukan.
                    </div>

                </div>

            </div>

        @else

            <div class="alert alert-success d-flex align-items-start gap-2">

                <i class="bi bi-check-circle-fill mt-1"></i>

                <div>

                    <div class="fw-semibold">
                        Tidak ditemukan kendaraan dengan konsumsi tidak wajar.
                    </div>

                    <div class="small">
                        Berdasarkan data dan standar konsumsi pada periode yang dipilih.
                    </div>

                </div>

            </div>

        @endif

        @if ($totalPemborosan > 0)

            <div class="alert alert-warning d-flex align-items-start gap-2 mb-0">

                <i class="bi bi-fuel-pump-fill mt-1"></i>

                <div>

                    <div class="fw-semibold">
                        Terdapat indikasi pemborosan BBM.
                    </div>

                    <div class="small">
                        Total pemborosan tercatat sebesar
                        <strong>
                            {{ number_format($totalPemborosan, 2, ',', '.') }} L
                        </strong>
                        dengan estimasi biaya kerugian sebesar
                        <strong>
                            Rp {{ number_format($totalBiayaKerugian, 0, ',', '.') }}
                        </strong>.
                    </div>

                </div>

            </div>

        @endif

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('aiInsightForm');
    const button = document.getElementById('aiInsightSubmit');
    const icon = document.getElementById('aiInsightIcon');
    const loading = document.getElementById('aiInsightLoading');

    if (!form || !button || !icon || !loading) {
        return;
    }

    form.addEventListener('submit', function () {
        button.disabled = true;
        button.classList.add('disabled');
        button.title = 'Sedang menganalisis...';

        icon.className = 'spinner-border spinner-border-sm';
        icon.setAttribute('role', 'status');
        icon.setAttribute('aria-hidden', 'true');

        loading.classList.remove('d-none');
    });
});
</script>

@endsection