@extends('layouts.app')

@section('title', 'Kendaraan Operasional')

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="mb-2">
            Kendaraan Operasional
        </h1>

        <p class="text-muted mb-0">
            Kelola data kendaraan operasional FuelVision.
        </p>
    </div>

    <a
        href="{{ route('kendaraan-operasional.create') }}"
        class="btn btn-primary"
    >
        <i class="bi bi-plus-lg me-1"></i>
        Tambah Kendaraan Operasional
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

<div class="card">

    <div class="card-body p-0">

        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">

            <div>

                <h5 class="mb-1">
                    Data Kendaraan
                </h5>

                <p class="text-muted mb-0 small">
                    Daftar kendaraan operasional yang terdaftar.
                </p>

            </div>

            <span class="badge bg-light text-dark border">
                {{ $kendaraan->count() }} Kendaraan
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
                            Kode Unit
                        </th>

                        <th>
                            Plat Nomor
                        </th>

                        <th>
                            Jenis Kendaraan
                        </th>

                        <th>
                            Departemen
                        </th>

                        <th>
                            Cost Center
                        </th>

                        <th>
                            QR Code
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

                    @forelse ($kendaraan as $item)

                        <tr>

                            <td class="ps-4 fw-semibold">
                                {{ $loop->iteration }}
                            </td>

                            <td class="fw-semibold">
                                {{ $item->kode_unit }}
                            </td>

                            <td>
                                {{ $item->plat_nomor }}
                            </td>

                            <td>
                                {{ $item->jenis_kendaraan }}
                            </td>

                            <td>
                                {{ $item->departemen }}
                            </td>

                            <td>
                                {{ $item->cost_center }}
                            </td>

                            <td>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-info text-white"
                                    onclick="showQrCode(
                                        @js($item->id),
                                        @js($item->kode_unit),
                                        @js($item->plat_nomor),
                                        @js(route('transaksi-pengisian-bbm.create', [
                                            'kendaraan_operasional_id' => $item->id
                                        ]))
                                    )"
                                >
                                    <i class="bi bi-qr-code me-1"></i>
                                    Lihat QR
                                </button>

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
                                        href="{{ route('kendaraan-operasional.edit', $item) }}"
                                        class="btn btn-sm btn-warning"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form
                                        action="{{ route('kendaraan-operasional.destroy', $item) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus kendaraan ini?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
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
                                colspan="9"
                                class="text-center py-5"
                            >

                                <div class="text-muted">

                                    <i
                                        class="bi bi-truck"
                                        style="font-size: 2rem;"
                                    ></i>

                                    <div class="mt-2">
                                        Belum ada data kendaraan operasional.
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

<div
    class="modal fade"
    id="qrModal"
    tabindex="-1"
    aria-labelledby="qrModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="qrModalLabel"
                >
                    QR Code Kendaraan
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>

            <div class="modal-body text-center">

                <h5
                    id="qrUnit"
                    class="mb-1"
                ></h5>

                <p
                    id="qrPlat"
                    class="text-muted mb-3"
                ></p>

                <div
                    id="qrcode"
                    class="d-flex justify-content-center"
                ></div>

                <p class="mt-3 mb-1">
                    Scan QR ini untuk membuka form pengisian BBM.
                </p>

                <p class="mb-0">

                    <small
                        id="qrValue"
                        class="text-muted"
                        style="word-break: break-all;"
                    ></small>

                </p>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Tutup
                </button>

                <button
                    type="button"
                    id="downloadQrButton"
                    class="btn btn-primary"
                    onclick="downloadQrCode()"
                >
                    <i class="bi bi-download me-1"></i>
                    Download QR
                </button>

            </div>

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>

let currentQrUrl = '';

function showQrCode(id, kodeUnit, platNomor, url) {

    currentQrUrl = url;

    document.getElementById('qrUnit').textContent = kodeUnit;

    document.getElementById('qrPlat').textContent = platNomor;

    document.getElementById('qrValue').textContent = url;

    const qrContainer = document.getElementById('qrcode');

    qrContainer.innerHTML = '';

    new QRCode(qrContainer, {
        text: url,
        width: 220,
        height: 220,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });

    const modalElement = document.getElementById('qrModal');

    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

    modal.show();
}

function downloadQrCode() {

    const qrContainer = document.getElementById('qrcode');

    const canvas = qrContainer.querySelector('canvas');

    if (canvas) {

        const link = document.createElement('a');

        link.download = 'QR-' + document.getElementById('qrUnit').textContent + '.png';

        link.href = canvas.toDataURL('image/png');

        link.click();

        return;
    }

    const image = qrContainer.querySelector('img');

    if (image) {

        const link = document.createElement('a');

        link.download = 'QR-' + document.getElementById('qrUnit').textContent + '.png';

        link.href = image.src;

        link.click();
    }
}

</script>

@endpush