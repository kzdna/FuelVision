@extends('layouts.app')

@section('title', 'Master Harga BBM Vendor')

@section('content')

<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">

    <div>
        <h1 class="mb-2">
            Master Harga BBM Vendor
        </h1>

        <p class="text-muted mb-0">
            Kelola data harga BBM dari vendor.
        </p>
    </div>

    <a
        href="{{ route('master-harga-bbm-vendor.create') }}"
        class="btn btn-primary"
    >
        <i class="bi bi-plus-lg me-1"></i>
        Tambah Harga BBM
    </a>

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
                    Data Harga BBM
                </h5>

                <p class="text-muted mb-0 small">
                    Daftar harga BBM vendor yang terdaftar.
                </p>

            </div>

            <span class="badge bg-light text-dark border">
                {{ $hargaBbm->count() }} Harga BBM
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
                            Harga
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($hargaBbm as $item)

                        <tr>

                            <td class="ps-4 fw-semibold">
                                {{ $loop->iteration }}
                            </td>

                            <td class="fw-semibold">
                                {{ $item->jenis_bbm }}
                            </td>

                            <td>
                                Rp {{ number_format((float) $item->harga, 2, ',', '.') }}
                            </td>

                            <td>

                                @if ($item->status)

                                    <span class="badge bg-success">
                                        Aktif
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Nonaktif
                                    </span>

                                @endif

                            </td>

                            <td>

                                <div class="d-flex gap-1">

                                    <a
                                        href="{{ route('master-harga-bbm-vendor.edit', $item) }}"
                                        class="btn btn-sm btn-warning"
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form
                                        action="{{ route('master-harga-bbm-vendor.destroy', $item) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus harga BBM ini?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                            title="Hapus"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center py-5"
                            >

                                <div class="text-muted">

                                    <i
                                        class="bi bi-fuel-pump"
                                        style="font-size: 2rem;"
                                    ></i>

                                    <div class="mt-2">
                                        Belum ada data harga BBM vendor.
                                    </div>

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