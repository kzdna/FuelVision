@extends('layouts.app')

@section('title', 'Riwayat Transaksi BBM')

@section('content')

<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">

    <div>
        <h1 class="mb-2">
            Riwayat Transaksi BBM
        </h1>

        <p class="text-muted mb-0">
            Daftar seluruh transaksi pengisian BBM FuelVision.
        </p>
    </div>

    <a
        href="{{ route('transaksi-pengisian-bbm.create') }}"
        class="btn btn-primary"
    >
        <i class="bi bi-plus-lg me-1"></i>
        Tambah Transaksi
    </a>

</div>

<div class="card fv-table-card mb-4">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h5 class="mb-1">
                    Filter Transaksi
                </h5>

                <p class="text-muted mb-0 small">
                    Gunakan filter untuk mencari transaksi tertentu.
                </p>
            </div>

            <a
                href="{{ route('transaksi-pengisian-bbm.index') }}"
                class="btn btn-outline-secondary btn-sm"
            >
                <i class="bi bi-arrow-counterclockwise me-1"></i>
                Reset
            </a>

        </div>

        <form
            method="GET"
            action="{{ route('transaksi-pengisian-bbm.index') }}"
        >

            <div class="row g-3">

                <div class="col-md-6 col-lg-4">

                    <label
                        for="search"
                        class="form-label"
                    >
                        Pencarian
                    </label>

                    <input
                        type="text"
                        id="search"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="Kode kendaraan atau driver"
                    >

                </div>

                <div class="col-md-6 col-lg-2">

                    <label
                        for="tanggal_mulai"
                        class="form-label"
                    >
                        Tanggal Mulai
                    </label>

                    <input
                        type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        class="form-control"
                        value="{{ request('tanggal_mulai') }}"
                    >

                </div>

                <div class="col-md-6 col-lg-2">

                    <label
                        for="tanggal_akhir"
                        class="form-label"
                    >
                        Tanggal Akhir
                    </label>

                    <input
                        type="date"
                        id="tanggal_akhir"
                        name="tanggal_akhir"
                        class="form-control"
                        value="{{ request('tanggal_akhir') }}"
                    >

                </div>

                <div class="col-md-6 col-lg-4">

                    <label
                        for="jenis_bbm"
                        class="form-label"
                    >
                        Jenis BBM
                    </label>

                    <select
                        id="jenis_bbm"
                        name="jenis_bbm"
                        class="form-select"
                    >

                        <option value="">
                            Semua Jenis BBM
                        </option>

                        @foreach ($jenisBbm as $item)

                            <option
                                value="{{ $item }}"
                                @selected(request('jenis_bbm') == $item)
                            >
                                {{ $item }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6 col-lg-4">

                    <label
                        for="departemen"
                        class="form-label"
                    >
                        Departemen
                    </label>

                    <select
                        id="departemen"
                        name="departemen"
                        class="form-select"
                    >

                        <option value="">
                            Semua Departemen
                        </option>

                        @foreach ($departemen as $item)

                            <option
                                value="{{ $item }}"
                                @selected(request('departemen') == $item)
                            >
                                {{ $item }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6 col-lg-4">

                    <label
                        for="cost_center"
                        class="form-label"
                    >
                        Cost Center
                    </label>

                    <select
                        id="cost_center"
                        name="cost_center"
                        class="form-select"
                    >

                        <option value="">
                            Semua Cost Center
                        </option>

                        @foreach ($costCenter as $item)

                            <option
                                value="{{ $item }}"
                                @selected(request('cost_center') == $item)
                            >
                                {{ $item }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6 col-lg-4 d-flex align-items-end">

                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                    >
                        <i class="bi bi-search me-1"></i>
                        Terapkan Filter
                    </button>

                </div>

            </div>

        </form>

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

<div class="card fv-table-card">

    <div class="card-body p-0">

        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">

            <div>

                <h5 class="mb-1">
                    Data Transaksi
                </h5>

                <p class="text-muted mb-0 small">

                    @if ($transaksi->total() > 0)

                        Menampilkan
                        {{ $transaksi->firstItem() }}
                        -
                        {{ $transaksi->lastItem() }}
                        dari
                        {{ $transaksi->total() }}
                        transaksi.

                    @else

                        Tidak ada transaksi.

                    @endif

                </p>

            </div>

            <span class="badge bg-light text-dark border">

                {{ $transaksi->total() }}
                Transaksi

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
                            Driver
                        </th>

                        <th>
                            Kilometer
                        </th>

                        <th>
                            BBM
                        </th>

                        <th>
                            Liter
                        </th>

                        <th>
                            Harga/Liter
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

                        <th class="text-center">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($transaksi as $item)

                        @php
                            $isGs = !is_null($item->kendaraan_gs_id);
                        @endphp

                        <tr>

                            <td class="ps-4 fw-semibold">
                                {{ $transaksi->firstItem() + $loop->index }}
                            </td>

                            <td>

                                <div class="fw-medium">
                                    {{ $item->tanggal_pengisian->format('d/m/Y') }}
                                </div>

                                <small class="text-muted">
                                    {{ $item->tanggal_pengisian->format('H:i') }}
                                </small>

                            </td>

                            <td>

                                @if ($isGs)

                                    <div class="fw-semibold">

                                        {{ $item->kendaraanGs?->kode_gs ?? 'GS-UMUM' }}

                                    </div>

                                    <small class="text-muted">
                                        Kendaraan GS
                                    </small>

                                    @if ($item->kendaraanOperasional)

                                        <div class="mt-1">

                                            <small class="text-muted">
                                                Menggantikan:
                                            </small>

                                            <br>

                                            <span class="fw-medium">
                                                {{ $item->kendaraanOperasional->kode_unit }}
                                            </span>

                                            <small class="text-muted">
                                                -
                                                {{ $item->kendaraanOperasional->plat_nomor }}
                                            </small>

                                        </div>

                                    @else

                                        <div class="mt-1">

                                            <small class="text-warning">
                                                Kendaraan pengganti belum ditentukan
                                            </small>

                                        </div>

                                    @endif

                                @elseif ($item->kendaraanOperasional)

                                    <div class="fw-semibold">
                                        {{ $item->kendaraanOperasional->kode_unit }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $item->kendaraanOperasional->jenis_kendaraan }}
                                    </small>

                                @else

                                    <span class="text-muted">
                                        -
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ $item->driver }}
                            </td>

                            <td>

                                {{ number_format(
                                    (float) $item->kilometer,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                                km

                            </td>

                            <td>

                                <span class="badge bg-light text-dark border">

                                    {{ $item->masterHargaBbmVendor?->jenis_bbm ?? '-' }}

                                </span>

                            </td>

                            <td class="fw-semibold">

                                {{ number_format(
                                    (float) $item->jumlah_liter,
                                    2,
                                    ',',
                                    '.'
                                ) }}

                                L

                            </td>

                            <td>

                                Rp

                                {{ number_format(
                                    (float) $item->harga_bbm_snapshot,
                                    2,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                            <td class="fw-semibold">

                                Rp

                                {{ number_format(
                                    (float) $item->total_biaya,
                                    2,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                            <td>
                                {{ $item->departemen_snapshot ?? '-' }}
                            </td>

                            <td>
                                {{ $item->cost_center_snapshot ?? '-' }}
                            </td>

                            <td class="text-center">

                                <div class="d-flex justify-content-center gap-2">

                                    <a
                                        href="{{ route(
                                            'transaksi-pengisian-bbm.edit',
                                            $item->id
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit transaksi"
                                    >

                                        <i class="bi bi-pencil-square"></i>
                                        Edit

                                    </a>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Hapus transaksi"
                                        onclick="confirmDelete({{ $item->id }})"
                                    >

                                        <i class="bi bi-trash"></i>
                                        Hapus

                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="12"
                                class="text-center py-5"
                            >

                                <div class="text-muted">

                                    <i
                                        class="bi bi-search"
                                        style="font-size: 2rem;"
                                    ></i>

                                    <div class="mt-2">
                                        Tidak ada transaksi yang sesuai dengan filter.
                                    </div>

                                    <a
                                        href="{{ route('transaksi-pengisian-bbm.index') }}"
                                        class="btn btn-sm btn-outline-primary mt-3"
                                    >
                                        Reset Filter
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if ($transaksi->hasPages())

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 px-4 py-3 border-top">

                <div class="text-muted small">

                    Halaman
                    {{ $transaksi->currentPage() }}
                    dari
                    {{ $transaksi->lastPage() }}

                </div>

                <div>

                    {{ $transaksi->onEachSide(1)->links('pagination::bootstrap-5') }}

                </div>

            </div>

        @endif

    </div>

</div>

<div
    class="modal fade"
    id="deleteModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Konfirmasi Hapus
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>

            <div class="modal-body">

                <p class="mb-1 fw-semibold">
                    Yakin ingin menghapus transaksi ini?
                </p>

                <p class="text-muted mb-0">
                    Data transaksi yang dihapus tidak dapat dikembalikan.
                </p>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                >
                    Batal
                </button>

                <form
                    id="deleteForm"
                    method="POST"
                    class="d-inline"
                >

                    @csrf

                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-danger"
                    >

                        <i class="bi bi-trash me-1"></i>
                        Ya, Hapus

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<script>

function confirmDelete(id) {

    const form =
        document.getElementById('deleteForm');

    form.action =
        "{{ url('/transaksi-pengisian-bbm') }}/" + id;

    const modal =
        new bootstrap.Modal(
            document.getElementById('deleteModal')
        );

    modal.show();

}

</script>

@endsection