@extends('layouts.app')

@section('title', $kendaraan->exists ? 'Edit Kendaraan Operasional' : 'Tambah Kendaraan Operasional')

@section('content')

<div class="mb-4">

    <h2>
        {{ $kendaraan->exists ? 'Edit Kendaraan Operasional' : 'Tambah Kendaraan Operasional' }}
    </h2>

    <p class="text-muted mb-0">
        {{ $kendaraan->exists
            ? 'Perbarui data kendaraan operasional.'
            : 'Tambahkan data kendaraan operasional baru.' }}
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


<div class="card">

    <div class="card-body">

        <form
            method="POST"
            action="{{ $kendaraan->exists
                ? route('kendaraan-operasional.update', $kendaraan)
                : route('kendaraan-operasional.store') }}"
        >

            @csrf

            @if ($kendaraan->exists)
                @method('PUT')
            @endif


            <div class="row">

                {{-- KODE UNIT --}}

                <div class="col-md-6 mb-3">

                    <label for="kode_unit" class="form-label">
                        Kode Unit
                    </label>

                    <input
                        type="text"
                        id="kode_unit"
                        name="kode_unit"
                        class="form-control"
                        value="{{ old('kode_unit', $kendaraan->kode_unit) }}"
                        maxlength="20"
                        required
                    >

                    <div class="form-text">
                        Masukkan kode unit kendaraan.
                    </div>

                </div>


                {{-- PLAT NOMOR --}}

                <div class="col-md-6 mb-3">

                    <label for="plat_nomor" class="form-label">
                        Plat Nomor
                    </label>

                    <input
                        type="text"
                        id="plat_nomor"
                        name="plat_nomor"
                        class="form-control"
                        value="{{ old('plat_nomor', $kendaraan->plat_nomor) }}"
                        maxlength="30"
                        required
                    >

                </div>


                {{-- JENIS KENDARAAN --}}

                <div class="col-md-6 mb-3">

                    <label for="jenis_kendaraan" class="form-label">
                        Jenis Kendaraan
                    </label>

                    <select
                        id="jenis_kendaraan"
                        name="jenis_kendaraan"
                        class="form-select"
                        required
                    >

                        <option value="">
                            -- Pilih Jenis Kendaraan --
                        </option>

                        @foreach ($jenisKendaraan as $jenis)

                            <option
                                value="{{ $jenis }}"
                                {{ old('jenis_kendaraan', $kendaraan->jenis_kendaraan) == $jenis ? 'selected' : '' }}
                            >
                                {{ $jenis }}
                            </option>

                        @endforeach

                    </select>

                    <div class="form-text">
                        Jenis kendaraan mengikuti master data FuelVision.
                    </div>

                </div>


                {{-- DEPARTEMEN --}}

                <div class="col-md-6 mb-3">

                    <label for="departemen" class="form-label">
                        Departemen
                    </label>

                    <select
                        id="departemen"
                        name="departemen"
                        class="form-select"
                        required
                    >

                        <option value="">
                            -- Pilih Departemen --
                        </option>

                        <option
                            value="Administration"
                            {{ old('departemen', $kendaraan->departemen) == 'Administration' ? 'selected' : '' }}
                        >
                            Administration
                        </option>

                        <option
                            value="Spare Part"
                            {{ old('departemen', $kendaraan->departemen) == 'Spare Part' ? 'selected' : '' }}
                        >
                            Spare Part
                        </option>

                        <option
                            value="Service FMC"
                            {{ old('departemen', $kendaraan->departemen) == 'Service FMC' ? 'selected' : '' }}
                        >
                            Service FMC
                        </option>

                        <option
                            value="Service Non FMC"
                            {{ old('departemen', $kendaraan->departemen) == 'Service Non FMC' ? 'selected' : '' }}
                        >
                            Service Non FMC
                        </option>

                    </select>

                </div>


                {{-- COST CENTER --}}

                <div class="col-md-6 mb-3">

                    <label for="cost_center" class="form-label">
                        Cost Center
                    </label>

                    <select
                        id="cost_center"
                        name="cost_center"
                        class="form-select"
                        required
                    >

                        <option value="">
                            -- Pilih Cost Center --
                        </option>

                    </select>

                    <div class="form-text">
                        Cost Center akan otomatis menyesuaikan Departemen.
                    </div>

                </div>


                {{-- STATUS --}}

                <div class="col-md-6 mb-3">

                    <label for="status" class="form-label">
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
                            {{ old(
                                'status',
                                $kendaraan->exists ? (int) $kendaraan->status : 1
                            ) == 1 ? 'selected' : '' }}
                        >
                            Aktif
                        </option>

                        <option
                            value="0"
                            {{ old(
                                'status',
                                $kendaraan->exists ? (int) $kendaraan->status : 1
                            ) == 0 ? 'selected' : '' }}
                        >
                            Nonaktif
                        </option>

                    </select>

                </div>

            </div>


            {{-- BUTTON --}}

            <div class="d-flex gap-2 mt-3">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    {{ $kendaraan->exists
                        ? 'Simpan Perubahan'
                        : 'Simpan Kendaraan' }}
                </button>

                <a
                    href="{{ route('kendaraan-operasional.index') }}"
                    class="btn btn-secondary"
                >
                    Batal
                </a>

            </div>

        </form>

    </div>

</div>


{{-- DEPARTEMEN → COST CENTER --}}

<script>

    const costCenterOptions = {

        'Administration': [
            {
                value: '01-STI-ADM',
                text: '01-STI-ADM'
            }
        ],

        'Spare Part': [
            {
                value: '01-STI-WHS',
                text: '01-STI-WHS'
            }
        ],

        'Service FMC': [
            {
                value: '01-STI-FMC',
                text: '01-STI-FMC'
            }
        ],

        'Service Non FMC': [
            {
                value: '01-STI-Non FMC',
                text: '01-STI-Non FMC'
            }
        ]

    };


    const departemenSelect =
        document.getElementById('departemen');

    const costCenterSelect =
        document.getElementById('cost_center');


    function updateCostCenter(selectedCostCenter = '')
    {
        const departemen =
            departemenSelect.value;

        costCenterSelect.innerHTML =
            '<option value="">-- Pilih Cost Center --</option>';


        if (!departemen) {

            costCenterSelect.disabled = true;

            return;

        }


        const options =
            costCenterOptions[departemen] || [];


        options.forEach(function (item) {

            const option =
                document.createElement('option');

            option.value =
                item.value;

            option.textContent =
                item.text;


            if (item.value === selectedCostCenter) {

                option.selected = true;

            }


            costCenterSelect.appendChild(option);

        });


        costCenterSelect.disabled =
            options.length === 0;
    }


    departemenSelect.addEventListener(
        'change',
        function () {

            updateCostCenter();

        }
    );


    const initialCostCenter =
        @json(old('cost_center', $kendaraan->cost_center));


    updateCostCenter(initialCostCenter);

</script>

@endsection