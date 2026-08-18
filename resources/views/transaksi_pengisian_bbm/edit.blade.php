@extends('layouts.app')

@section('title', 'Edit Transaksi BBM')

@section('content')

<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">

    <div>
        <h1 class="mb-2">
            Edit Transaksi BBM
        </h1>

        <p class="text-muted mb-0">
            Perbarui data transaksi pengisian BBM.
        </p>
    </div>

    <a
        href="{{ route('transaksi-pengisian-bbm.index') }}"
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

<div class="card fv-table-card">

    <div class="card-body">

        <form
            method="POST"
            action="{{ route('transaksi-pengisian-bbm.update', $transaksi->id) }}"
        >

            @csrf

            @method('PUT')

            <div class="row g-4">

                <div class="col-md-6">

                    <label
                        for="kendaraan_operasional_id"
                        class="form-label"
                    >
                        Kendaraan Operasional
                    </label>

                    <select
                        id="kendaraan_operasional_id"
                        name="kendaraan_operasional_id"
                        class="form-select"
                    >

                        <option value="">
                            Pilih Kendaraan
                        </option>

                        @foreach ($kendaraanOperasional as $item)

                            <option
                                value="{{ $item->id }}"
                                @selected(
                                    old(
                                        'kendaraan_operasional_id',
                                        $transaksi->kendaraan_operasional_id
                                    ) == $item->id
                                )
                            >
                                {{ $item->kode_unit }}
                                -
                                {{ $item->jenis_kendaraan }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6">

                    <label
                        for="kendaraan_gs_id"
                        class="form-label"
                    >
                        Kendaraan GS
                    </label>

                    <select
                        id="kendaraan_gs_id"
                        name="kendaraan_gs_id"
                        class="form-select"
                    >

                        <option value="">
                            Tidak menggunakan kendaraan GS
                        </option>

                        @foreach ($kendaraanGs as $item)

                            <option
                                value="{{ $item->id }}"
                                @selected(
                                    old(
                                        'kendaraan_gs_id',
                                        $transaksi->kendaraan_gs_id
                                    ) == $item->id
                                )
                            >
                                {{ $item->kode_gs }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6">

                    <label
                        for="driver"
                        class="form-label"
                    >
                        Driver
                    </label>

                    <input
                        type="text"
                        id="driver"
                        name="driver"
                        class="form-control"
                        value="{{ old('driver', $transaksi->driver) }}"
                        required
                    >

                </div>

                <div class="col-md-6">

                    <label
                        for="master_harga_bbm_vendor_id"
                        class="form-label"
                    >
                        Jenis BBM
                    </label>

                    <select
                        id="master_harga_bbm_vendor_id"
                        name="master_harga_bbm_vendor_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Pilih Jenis BBM
                        </option>

                        @foreach ($hargaBbm as $item)

                            <option
                                value="{{ $item->id }}"
                                data-harga="{{ $item->harga }}"
                                @selected(
                                    old(
                                        'master_harga_bbm_vendor_id',
                                        $transaksi->master_harga_bbm_vendor_id
                                    ) == $item->id
                                )
                            >
                                {{ $item->jenis_bbm }}
                                -
                                Rp
                                {{ number_format(
                                    (float) $item->harga,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6">

                    <label
                        for="kilometer"
                        class="form-label"
                    >
                        Kilometer
                    </label>

                    <input
                        type="number"
                        id="kilometer"
                        name="kilometer"
                        class="form-control"
                        min="0"
                        value="{{ old('kilometer', $transaksi->kilometer) }}"
                        required
                    >

                </div>

                <div class="col-md-6">

                    <label
                        for="jumlah_liter"
                        class="form-label"
                    >
                        Jumlah Liter
                    </label>

                    <input
                        type="number"
                        id="jumlah_liter"
                        name="jumlah_liter"
                        class="form-control"
                        min="0.01"
                        step="0.01"
                        value="{{ old('jumlah_liter', $transaksi->jumlah_liter) }}"
                        required
                    >

                </div>

                <div class="col-md-6">

                    <label
                        for="tanggal_pengisian"
                        class="form-label"
                    >
                        Tanggal Pengisian
                    </label>

                    <input
                        type="datetime-local"
                        id="tanggal_pengisian"
                        name="tanggal_pengisian"
                        class="form-control"
                        value="{{ old(
                            'tanggal_pengisian',
                            $transaksi->tanggal_pengisian?->format('Y-m-d\TH:i')
                        ) }}"
                        required
                    >

                </div>

                <div class="col-md-6">

                    <label
                        for="total_biaya"
                        class="form-label"
                    >
                        Total Biaya
                    </label>

                    <input
                        type="text"
                        id="total_biaya"
                        class="form-control"
                        value="Rp {{ number_format(
                            (float) $transaksi->total_biaya,
                            0,
                            ',',
                            '.'
                        ) }}"
                        readonly
                    >

                </div>

                <div class="col-12">

                    <label
                        for="keterangan"
                        class="form-label"
                    >
                        Keterangan
                    </label>

                    <textarea
                        id="keterangan"
                        name="keterangan"
                        class="form-control"
                        rows="3"
                    >{{ old('keterangan', $transaksi->keterangan) }}</textarea>

                </div>

            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">

                <a
                    href="{{ route('transaksi-pengisian-bbm.index') }}"
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const hargaSelect = document.getElementById('master_harga_bbm_vendor_id');
    const literInput = document.getElementById('jumlah_liter');
    const totalInput = document.getElementById('total_biaya');

    function updateTotal() {
        const selectedOption = hargaSelect.options[hargaSelect.selectedIndex];
        const harga = parseFloat(selectedOption?.dataset.harga || 0);
        const liter = parseFloat(literInput.value || 0);
        const total = harga * liter;

        totalInput.value = 'Rp ' + total.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    hargaSelect.addEventListener('change', updateTotal);
    literInput.addEventListener('input', updateTotal);

    updateTotal();
});
</script>

@endsection