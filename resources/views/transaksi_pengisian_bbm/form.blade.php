@extends('layouts.app')

@section('title', 'Tambah Transaksi Pengisian BBM')

@section('content')

<div class="mb-4">
    <h2>Tambah Transaksi Pengisian BBM</h2>

    <p class="text-muted mb-0">
        Masukkan data pengisian BBM kendaraan.
    </p>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Terdapat kesalahan:</strong>

        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $selectedOperasional = old(
        'kendaraan_operasional_id',
        $kendaraanOperasionalId ?? null
    );

    $selectedGs = old(
        'kendaraan_gs_id',
        $kendaraanGsId ?? null
    );

    $selectedJenisKendaraan = old(
        'jenis_kendaraan',
        $jenisKendaraan ?? null
    );

    $fromQr =
        !empty($kendaraanOperasionalId) ||
        !empty($kendaraanGsId) ||
        !empty($selectedJenisKendaraan);

    $isGs =
        $selectedJenisKendaraan === 'gs' ||
        !empty($selectedGs);

    $selectedGsData = null;

    if ($selectedGs) {
        $selectedGsData = $kendaraanGs->firstWhere(
            'id',
            $selectedGs
        );
    }
@endphp

<div class="card">
    <div class="card-body">

        @if ($fromQr)

            <div class="alert alert-info mb-4">

                <strong>Kendaraan terdeteksi dari QR Code.</strong>

                <br>

                Silakan lengkapi data pengisian BBM di bawah.

            </div>

        @endif

        @if ($isGs)

            <div class="alert alert-warning mb-4">

                <strong>
                    Pengisian BBM Kendaraan GS
                </strong>

                <br>

                Pilih kendaraan operasional yang sedang digantikan
                untuk menentukan Departemen dan Cost Center pembebanan.

            </div>

        @endif

        <form
            method="POST"
            action="{{ route('transaksi-pengisian-bbm.store') }}"
        >

            @csrf

            <input
                type="hidden"
                name="jenis_kendaraan"
                value="{{ $isGs ? 'gs' : 'operasional' }}"
            >

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label
                        for="kendaraan_operasional_id"
                        class="form-label"
                    >
                        {{ $isGs
                            ? 'Kendaraan yang Digantikan'
                            : 'Kendaraan Operasional' }}
                    </label>

                    <select
                        id="kendaraan_operasional_id"
                        name="kendaraan_operasional_id"
                        class="form-select"
                        {{ $isGs ? 'required' : '' }}
                    >

                        <option value="">
                            {{ $isGs
                                ? '-- Pilih Kendaraan yang Digantikan --'
                                : '-- Pilih Kendaraan Operasional --' }}
                        </option>

                        @foreach ($kendaraanOperasional as $item)

                            <option
                                value="{{ $item->id }}"
                                data-departemen="{{ $item->departemen }}"
                                data-cost-center="{{ $item->cost_center }}"
                                @selected(
                                    $selectedOperasional == $item->id
                                )
                            >
                                {{ $item->kode_unit }}
                                -
                                {{ $item->plat_nomor }}
                            </option>

                        @endforeach

                    </select>

                    @if ($isGs)

                        <div class="form-text">
                            Pilih kendaraan operasional yang sedang
                            digantikan oleh kendaraan GS.
                        </div>

                    @endif

                </div>

                <div class="col-md-6 mb-3">

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
                        {{ $isGs ? 'disabled' : '' }}
                    >

                        <option value="">
                            -- Tidak Menggunakan Kendaraan GS --
                        </option>

                        @foreach ($kendaraanGs as $item)

                            <option
                                value="{{ $item->id }}"
                                @selected(
                                    $selectedGs == $item->id
                                )
                            >
                                {{ $item->kode_gs }}

                                @if ($item->plat_nomor)
                                    - {{ $item->plat_nomor }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                    @if ($isGs && $selectedGs)

                        <input
                            type="hidden"
                            name="kendaraan_gs_id"
                            value="{{ $selectedGs }}"
                        >

                        <div class="form-text">

                            @if (
                                $selectedGsData &&
                                $selectedGsData->kode_gs === 'GS-UMUM'
                            )

                                <strong>GS-UMUM</strong>
                                ditentukan berdasarkan QR Code.

                            @else

                                Kendaraan GS ditentukan berdasarkan QR Code.

                            @endif

                        </div>

                    @endif

                </div>

                @if ($isGs)

                    <div class="col-md-6 mb-3">

                        <label
                            for="departemen_pembebanan"
                            class="form-label"
                        >
                            Departemen Pembebanan
                        </label>

                        <input
                            type="text"
                            id="departemen_pembebanan"
                            class="form-control"
                            value=""
                            readonly
                        >

                        <div class="form-text">
                            Mengikuti kendaraan yang digantikan.
                        </div>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label
                            for="cost_center_pembebanan"
                            class="form-label"
                        >
                            Cost Center Pembebanan
                        </label>

                        <input
                            type="text"
                            id="cost_center_pembebanan"
                            class="form-control"
                            value=""
                            readonly
                        >

                        <div class="form-text">
                            Mengikuti kendaraan yang digantikan.
                        </div>

                    </div>

                @endif

                <div class="col-md-6 mb-3">

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

                        <option
                            value=""
                            data-harga="0"
                        >
                            -- Pilih Jenis BBM --
                        </option>

                        @foreach ($hargaBbm as $item)

                            <option
                                value="{{ $item->id }}"
                                data-harga="{{ $item->harga }}"
                                @selected(
                                    old('master_harga_bbm_vendor_id') == $item->id
                                )
                            >
                                {{ $item->jenis_bbm }}
                                -
                                Rp {{ number_format(
                                    (float) $item->harga,
                                    2,
                                    ',',
                                    '.'
                                ) }}/L
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6 mb-3">

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
                        value="{{ old('driver') }}"
                        maxlength="100"
                        required
                    >

                </div>

                <div class="col-md-6 mb-3">

                    <label
                        for="kilometer"
                        class="form-label"
                    >
                        Kilometer / Odometer
                    </label>

                    <input
                        type="number"
                        id="kilometer"
                        name="kilometer"
                        class="form-control"
                        value="{{ old('kilometer') }}"
                        min="0"
                        required
                    >

                    <div class="form-text">
                        Masukkan kilometer kendaraan saat pengisian.
                    </div>

                </div>

                <div class="col-md-6 mb-3">

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
                        value="{{ old('jumlah_liter') }}"
                        min="0.01"
                        step="0.01"
                        required
                    >

                </div>

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Harga per Liter
                    </label>

                    <input
                        type="text"
                        id="harga_per_liter"
                        class="form-control"
                        value="Rp 0,00"
                        readonly
                    >

                </div>

                <div class="col-md-6 mb-3">

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
                        value="{{ old('tanggal_pengisian') }}"
                        required
                    >

                </div>

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Total Biaya
                    </label>

                    <input
                        type="text"
                        id="total_biaya"
                        class="form-control"
                        value="Rp 0,00"
                        readonly
                    >

                </div>

                <div class="col-md-6 mb-3">

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
                    >{{ old('keterangan') }}</textarea>

                </div>

            </div>

            <div class="d-flex gap-2 mt-3">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Simpan Transaksi
                </button>

                @if (auth()->check())

                    <a
                        href="{{ route('transaksi-pengisian-bbm.index') }}"
                        class="btn btn-secondary"
                    >
                        Batal
                    </a>

                @endif

            </div>

        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))

<script>
document.addEventListener('DOMContentLoaded', function () {

    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: @json(session('success')),
        confirmButtonText: 'OK',
        confirmButtonColor: '#0d6efd'
    });

});
</script>

@endif

@if ($errors->any())

<script>
document.addEventListener('DOMContentLoaded', function () {

    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        html: @json(
            '<ul style="text-align:left; margin:0; padding-left:20px;">' .
            collect($errors->all())
                ->map(
                    fn ($error) =>
                        '<li>' . e($error) . '</li>'
                )
                ->implode('') .
            '</ul>'
        ),
        confirmButtonText: 'OK',
        confirmButtonColor: '#dc3545'
    });

});
</script>

@endif

<script>

document.addEventListener('DOMContentLoaded', function () {

    const kendaraanOperasional =
        document.getElementById(
            'kendaraan_operasional_id'
        );

    const kendaraanGs =
        document.getElementById(
            'kendaraan_gs_id'
        );

    const jenisBbm =
        document.getElementById(
            'master_harga_bbm_vendor_id'
        );

    const jumlahLiter =
        document.getElementById(
            'jumlah_liter'
        );

    const hargaPerLiter =
        document.getElementById(
            'harga_per_liter'
        );

    const totalBiaya =
        document.getElementById(
            'total_biaya'
        );

    const departemenPembebanan =
        document.getElementById(
            'departemen_pembebanan'
        );

    const costCenterPembebanan =
        document.getElementById(
            'cost_center_pembebanan'
        );

    function formatRupiah(angka) {

        return new Intl.NumberFormat(
            'id-ID',
            {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        ).format(angka);

    }

    function hitungTotal() {

        if (!jenisBbm) {
            return;
        }

        const selectedOption =
            jenisBbm.options[
                jenisBbm.selectedIndex
            ];

        const harga =
            parseFloat(
                selectedOption?.dataset?.harga || 0
            );

        const liter =
            parseFloat(
                jumlahLiter?.value || 0
            );

        if (hargaPerLiter) {

            hargaPerLiter.value =
                formatRupiah(harga);

        }

        if (totalBiaya) {

            totalBiaya.value =
                formatRupiah(
                    harga * liter
                );

        }

    }

    function updatePembebanan() {

        if (
            !departemenPembebanan ||
            !costCenterPembebanan ||
            !kendaraanOperasional
        ) {
            return;
        }

        const selectedOption =
            kendaraanOperasional.options[
                kendaraanOperasional.selectedIndex
            ];

        if (
            !selectedOption ||
            !selectedOption.value
        ) {

            departemenPembebanan.value = '';
            costCenterPembebanan.value = '';

            return;

        }

        departemenPembebanan.value =
            selectedOption.dataset.departemen || '';

        costCenterPembebanan.value =
            selectedOption.dataset.costCenter || '';

    }

    if (kendaraanOperasional) {

        kendaraanOperasional.addEventListener(
            'change',
            updatePembebanan
        );

    }

    if (jenisBbm) {

        jenisBbm.addEventListener(
            'change',
            hitungTotal
        );

    }

    if (jumlahLiter) {

        jumlahLiter.addEventListener(
            'input',
            hitungTotal
        );

    }

    updatePembebanan();
    hitungTotal();

});

</script>

@endsection