@extends('layouts.app')

@section('title', 'Edit Standar Konsumsi BBM')

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="mb-2">
            Edit Standar Konsumsi BBM
        </h1>

        <p class="text-muted mb-0">
            Perbarui standar konsumsi BBM untuk jenis kendaraan.
        </p>
    </div>

    <a
        href="{{ route('standar-konsumsi-bbm.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left me-1"></i>
        Kembali
    </a>
</div>

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

    <div class="card-body">

        <form
            method="POST"
            action="{{ route('standar-konsumsi-bbm.update', $standarKonsumsi->id) }}"
        >

            @csrf
            @method('PUT')

            <div class="mb-4">

                <label
                    for="jenis_kendaraan"
                    class="form-label"
                >
                    Jenis Kendaraan
                </label>

                <input
                    type="text"
                    id="jenis_kendaraan"
                    class="form-control"
                    value="{{ $standarKonsumsi->jenis_kendaraan }}"
                    readonly
                >

                <div class="form-text">
                    Jenis kendaraan mengikuti master data kendaraan dan tidak dapat diubah dari halaman ini.
                </div>

            </div>

            <div class="row">

                <div class="col-md-6 mb-4">

                    <label
                        for="standar_min_km_per_liter"
                        class="form-label"
                    >
                        Standar Minimum
                    </label>

                    <div class="input-group">

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            id="standar_min_km_per_liter"
                            name="standar_min_km_per_liter"
                            class="form-control"
                            value="{{ old('standar_min_km_per_liter', $standarKonsumsi->standar_min_km_per_liter) }}"
                            placeholder="Contoh: 8"
                        >

                        <span class="input-group-text">
                            km/L
                        </span>

                    </div>

                    <div class="form-text">
                        Batas minimum konsumsi yang digunakan sebagai acuan evaluasi.
                    </div>

                </div>

                <div class="col-md-6 mb-4">

                    <label
                        for="standar_max_km_per_liter"
                        class="form-label"
                    >
                        Standar Maksimum
                    </label>

                    <div class="input-group">

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            id="standar_max_km_per_liter"
                            name="standar_max_km_per_liter"
                            class="form-control"
                            value="{{ old('standar_max_km_per_liter', $standarKonsumsi->standar_max_km_per_liter) }}"
                            placeholder="Contoh: 12"
                        >

                        <span class="input-group-text">
                            km/L
                        </span>

                    </div>

                    <div class="form-text">
                        Nilai maksimum tidak boleh lebih kecil dari standar minimum.
                    </div>

                </div>

            </div>

            <div class="mb-4">

                <label
                    for="status"
                    class="form-label"
                >
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                    class="form-select"
                    required
                >

                    <option
                        value="1"
                        @selected(old('status', $standarKonsumsi->status) == 1)
                    >
                        Aktif
                    </option>

                    <option
                        value="0"
                        @selected(old('status', $standarKonsumsi->status) == 0)
                    >
                        Tidak Aktif
                    </option>

                </select>

                <div class="form-text">
                    Standar hanya digunakan dalam evaluasi monitoring jika berstatus aktif.
                </div>

            </div>

            <div class="d-flex justify-content-end gap-2">

                <a
                    href="{{ route('standar-konsumsi-bbm.index') }}"
                    class="btn btn-outline-secondary"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-save me-1"></i>
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection