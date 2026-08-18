@extends('layouts.app')

@section('title', $hargaBbm->exists ? 'Edit Harga BBM' : 'Tambah Harga BBM')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h2>
            {{ $hargaBbm->exists ? 'Edit Harga BBM' : 'Tambah Harga BBM' }}
        </h2>

        <p class="text-muted mb-0">
            {{ $hargaBbm->exists
                ? 'Perbarui data harga BBM vendor.'
                : 'Tambahkan data harga BBM vendor baru.' }}
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
                action="{{ $hargaBbm->exists
                    ? route('master-harga-bbm-vendor.update', $hargaBbm)
                    : route('master-harga-bbm-vendor.store') }}"
            >

                @csrf

                @if ($hargaBbm->exists)
                    @method('PUT')
                @endif

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label for="jenis_bbm" class="form-label">
                            Jenis BBM
                        </label>

                        <input
                            type="text"
                            id="jenis_bbm"
                            name="jenis_bbm"
                            class="form-control"
                            value="{{ old('jenis_bbm', $hargaBbm->jenis_bbm) }}"
                            maxlength="50"
                            required
                        >

                        <div class="form-text">
                            Contoh: Solar, Dexlite, atau jenis BBM lainnya.
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="harga" class="form-label">
                            Harga per Liter
                        </label>

                        <input
                            type="number"
                            id="harga"
                            name="harga"
                            class="form-control"
                            value="{{ old('harga', $hargaBbm->harga) }}"
                            min="0"
                            step="0.01"
                            required
                        >

                        <div class="form-text">
                            Masukkan harga BBM per liter.
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
                                {{ old('status', $hargaBbm->exists ? (int) $hargaBbm->status : 1) == 1 ? 'selected' : '' }}
                            >
                                Aktif
                            </option>

                            <option
                                value="0"
                                {{ old('status', $hargaBbm->exists ? (int) $hargaBbm->status : 1) == 0 ? 'selected' : '' }}
                            >
                                Nonaktif
                            </option>
                        </select>
                    </div>

                </div>

                <div class="d-flex gap-2 mt-3">

                    <button type="submit" class="btn btn-primary">
                        {{ $hargaBbm->exists ? 'Simpan Perubahan' : 'Simpan Harga BBM' }}
                    </button>

                    <a
                        href="{{ route('master-harga-bbm-vendor.index') }}"
                        class="btn btn-secondary"
                    >
                        Batal
                    </a>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection