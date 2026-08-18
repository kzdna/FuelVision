@extends('layouts.app')

@section('title', $kendaraan->exists ? 'Edit Kendaraan GS' : 'Tambah Kendaraan GS')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h2>
            {{ $kendaraan->exists ? 'Edit Kendaraan GS' : 'Tambah Kendaraan GS' }}
        </h2>

        <p class="text-muted mb-0">
            {{ $kendaraan->exists
                ? 'Perbarui data kendaraan GS.'
                : 'Tambahkan data kendaraan GS baru.' }}
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
                    ? route('kendaraan-gs.update', $kendaraan)
                    : route('kendaraan-gs.store') }}"
            >

                @csrf

                @if ($kendaraan->exists)
                    @method('PUT')
                @endif

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label for="kode_gs" class="form-label">
                            Kode GS
                        </label>

                        <input
                            type="text"
                            id="kode_gs"
                            name="kode_gs"
                            class="form-control"
                            value="{{ old('kode_gs', $kendaraan->kode_gs) }}"
                            maxlength="30"
                            required
                        >

                        <div
                            id="kodeGsHelp"
                            class="form-text"
                        >
                            Masukkan kode kendaraan GS.
                        </div>
                    </div>

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
                            maxlength="20"
                        >

                        <div
                            id="platNomorHelp"
                            class="form-text"
                        >
                            Plat nomor wajib diisi untuk kendaraan GS biasa.
                        </div>
                    </div>

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
                                {{ old('status', $kendaraan->exists ? (int) $kendaraan->status : 1) == 1 ? 'selected' : '' }}
                            >
                                Aktif
                            </option>

                            <option
                                value="0"
                                {{ old('status', $kendaraan->exists ? (int) $kendaraan->status : 1) == 0 ? 'selected' : '' }}
                            >
                                Nonaktif
                            </option>
                        </select>
                    </div>

                </div>

                <div
                    id="gsUmumInfo"
                    class="alert alert-info mt-2 d-none"
                >
                    <strong>GS Umum</strong>
                    <br>
                    Data ini digunakan untuk kendaraan GS yang belum memiliki
                    identitas kendaraan atau kode unit spesifik.
                    <br>
                    QR Code akan otomatis menggunakan kode
                    <strong>GS_GENERAL</strong>.
                    Plat nomor tidak diperlukan.
                </div>

                <div class="d-flex gap-2 mt-3">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        {{ $kendaraan->exists ? 'Simpan Perubahan' : 'Simpan Kendaraan GS' }}
                    </button>

                    <a
                        href="{{ route('kendaraan-gs.index') }}"
                        class="btn btn-secondary"
                    >
                        Batal
                    </a>

                </div>

            </form>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const kodeGsInput = document.getElementById('kode_gs');
    const platNomorInput = document.getElementById('plat_nomor');
    const kodeGsHelp = document.getElementById('kodeGsHelp');
    const platNomorHelp = document.getElementById('platNomorHelp');
    const gsUmumInfo = document.getElementById('gsUmumInfo');

    function isGsUmum(value) {

        const normalized = value
            .trim()
            .toUpperCase()
            .replace(/\s+/g, '-');

        return normalized === 'GS-UMUM';
    }

    function updateGsForm() {

        const value = kodeGsInput.value;

        if (isGsUmum(value)) {

            kodeGsInput.value = 'GS-UMUM';

            platNomorInput.value = '';
            platNomorInput.required = false;
            platNomorInput.disabled = true;

            kodeGsHelp.textContent =
                'GS-UMUM digunakan untuk kendaraan GS yang belum memiliki identitas spesifik.';

            platNomorHelp.textContent =
                'Plat nomor tidak diperlukan untuk GS-UMUM.';

            gsUmumInfo.classList.remove('d-none');

        } else {

            platNomorInput.disabled = false;
            platNomorInput.required = true;

            kodeGsHelp.textContent =
                'Masukkan kode kendaraan GS.';

            platNomorHelp.textContent =
                'Plat nomor wajib diisi untuk kendaraan GS biasa.';

            gsUmumInfo.classList.add('d-none');
        }
    }

    kodeGsInput.addEventListener('input', updateGsForm);

    kodeGsInput.addEventListener('change', updateGsForm);

    updateGsForm();
});
</script>

@endsection