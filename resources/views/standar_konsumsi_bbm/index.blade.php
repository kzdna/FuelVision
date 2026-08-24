@extends('layouts.app')

@section('title', 'Standar Konsumsi BBM')

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="mb-2">
            Standar Konsumsi BBM
        </h1>

        <p class="text-muted mb-0">
            Kelola standar konsumsi BBM berdasarkan jenis kendaraan.
        </p>
    </div>
</div>

@if (session('success'))

    <div class="alert alert-success d-flex align-items-center mb-4">

        <i class="bi bi-check-circle me-2"></i>

        <span>
            {{ session('success') }}
        </span>

    </div>

@endif

@if ($errors->any())

    <div class="alert alert-danger mb-4">

        <div class="fw-semibold mb-2">
            Terdapat kesalahan:
        </div>

        <ul class="mb-0">

            @foreach ($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

            @endforeach

        </ul>

    </div>

@endif

<div class="card">

    <div class="card-body p-0">

        <div class="px-4 py-3 border-bottom">

            <h5 class="mb-1">
                Data Standar Konsumsi
            </h5>

            <p class="text-muted mb-0 small">
                Standar konsumsi digunakan sebagai acuan evaluasi konsumsi BBM kendaraan pada monitoring.
            </p>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th class="px-4">
                            No
                        </th>

                        <th>
                            Jenis Kendaraan
                        </th>

                        <th>
                            Standar Minimum
                        </th>

                        <th>
                            Standar Maksimum
                        </th>

                        <th>
                            Status
                        </th>

                        <th class="text-end px-4">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($standarKonsumsi as $index => $item)

                        <tr>

                            <td class="px-4">
                                {{ $index + 1 }}
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $item->jenis_kendaraan }}
                                </div>
                            </td>

                            <td>

                                @if ($item->standar_min_km_per_liter !== null)

                                    {{ number_format(
                                        (float) $item->standar_min_km_per_liter,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                    km/L

                                @else

                                    <span class="text-muted">
                                        Belum diatur
                                    </span>

                                @endif

                            </td>

                            <td>

                                @if ($item->standar_max_km_per_liter !== null)

                                    {{ number_format(
                                        (float) $item->standar_max_km_per_liter,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                    km/L

                                @else

                                    <span class="text-muted">
                                        Belum diatur
                                    </span>

                                @endif

                            </td>

                            <td>

                                @if ($item->status)

                                    <span class="badge bg-success">
                                        Aktif
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Tidak Aktif
                                    </span>

                                @endif

                            </td>

                            <td class="text-end px-4">

                                <a
                                    href="{{ route(
                                        'standar-konsumsi-bbm.edit',
                                        $item->id
                                    ) }}"
                                    class="btn btn-sm btn-warning"
                                >
                                    <i class="bi bi-pencil me-1"></i>
                                    Edit
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-5"
                            >

                                <div class="text-muted">

                                    <i class="bi bi-speedometer2 fs-3 d-block mb-2"></i>

                                    Belum ada data standar konsumsi BBM.

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