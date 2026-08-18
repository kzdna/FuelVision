@extends('layouts.app')

@section('title', 'Kendaraan GS')

@section('content')

<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">

    <div>
        <h1 class="mb-2">
            Kendaraan GS
        </h1>

        <p class="text-muted mb-0">
            Kelola data kendaraan GS FuelVision.
        </p>
    </div>

    <a
        href="{{ route('kendaraan-gs.create') }}"
        class="btn btn-primary"
    >
        <i class="bi bi-plus-lg me-1"></i>
        Tambah Kendaraan GS
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

@if (session('error'))

    <div class="alert alert-danger d-flex align-items-center mb-4">

        <i class="bi bi-exclamation-circle me-2"></i>

        <span>
            {{ session('error') }}
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
                    Data Kendaraan
                </h5>

                <p class="text-muted mb-0 small">
                    Daftar kendaraan GS dan QR pengisian BBM yang terdaftar.
                </p>

            </div>

            <span class="badge bg-light text-dark border">
                {{ $kendaraan->count() }} Data
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
                            Kode GS
                        </th>

                        <th>
                            Plat Nomor
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

                        @php

                            $isGsUmum =
                                $item->kode_gs === 'GS-UMUM';

                        @endphp


                        <tr>

                            <td class="ps-4 fw-semibold">
                                {{ $loop->iteration }}
                            </td>


                            <td class="fw-semibold">

                                {{ $item->kode_gs }}

                                @if ($isGsUmum)

                                    <span class="badge bg-info text-dark ms-1">
                                        GS Umum
                                    </span>

                                @endif

                            </td>


                            <td>

                                @if ($isGsUmum)

                                    <span class="text-muted">
                                        Tidak ditentukan
                                    </span>

                                @else

                                    {{ $item->plat_nomor }}

                                @endif

                            </td>


                            <td>

                                @if ($item->status)

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-info text-white"
                                        onclick="showGsQrCode(
                                            @js($item->id),
                                            @js($item->kode_gs),
                                            @js($item->plat_nomor),
                                            @js($item->qr_code),
                                            @js($isGsUmum)
                                        )"
                                    >

                                        <i class="bi bi-qr-code me-1"></i>

                                        Lihat QR

                                    </button>

                                @else

                                    <span class="text-muted">
                                        Tidak aktif
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
                                        Nonaktif
                                    </span>

                                @endif

                            </td>


                            <td>

                                <div class="d-flex gap-1">

                                    <a
                                        href="{{ route('kendaraan-gs.edit', $item) }}"
                                        class="btn btn-sm btn-warning"
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>


                                    @if (!$isGsUmum)

                                        <form
                                            action="{{ route('kendaraan-gs.destroy', $item) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus kendaraan GS ini?')"
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

                                    @endif

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-5"
                            >

                                <div class="text-muted">

                                    <i
                                        class="bi bi-truck-flatbed"
                                        style="font-size: 2rem;"
                                    ></i>

                                    <div class="mt-2">
                                        Belum ada data kendaraan GS.
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


{{-- ========================================================= --}}
{{-- MODAL QR CODE --}}
{{-- ========================================================= --}}

<div
    class="modal fade"
    id="gsQrModal"
    tabindex="-1"
    aria-labelledby="gsQrModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="gsQrModalLabel"
                >
                    QR Code Kendaraan GS
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
                    id="gsQrUnit"
                    class="mb-1"
                ></h5>


                <p
                    id="gsQrPlat"
                    class="text-muted mb-3"
                ></p>


                <div
                    id="gsQrcode"
                    class="d-flex justify-content-center"
                ></div>


                <p
                    id="gsQrDescription"
                    class="mt-3 mb-1"
                ></p>


                <p class="mb-0">

                    <small
                        id="gsQrValue"
                        class="text-muted"
                        style="
                            word-break: break-all;
                            font-size: 11px;
                        "
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
                    class="btn btn-primary"
                    onclick="downloadGsQrCode()"
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

<script
    src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"
></script>


<script>

/*
|--------------------------------------------------------------------------
| URL FORM PENGISIAN BBM
|--------------------------------------------------------------------------
|
| URL ini dibuat Laravel.
|
| Contoh hasil:
|
| http://192.168.1.17:8000/transaksi-pengisian-bbm/create
|
*/

const formPengisianBbmUrl =
    @json(
        route('transaksi-pengisian-bbm.create')
    );


let currentGsQrValue = '';

let currentGsQrIsGeneral = false;


/*
|--------------------------------------------------------------------------
| TAMPILKAN QR CODE
|--------------------------------------------------------------------------
*/

function showGsQrCode(
    id,
    kodeGs,
    platNomor,
    qrCode,
    isGsUmum
) {

    currentGsQrIsGeneral =
        isGsUmum;


    /*
    |--------------------------------------------------------------------------
    | BUAT URL QR
    |--------------------------------------------------------------------------
    |
    | Sekarang QR TIDAK LAGI berisi:
    |
    | GS_GENERAL
    |
    | Tetapi berisi URL lengkap ke form.
    |
    */

    const params =
        new URLSearchParams({

            jenis_kendaraan: 'gs',

            kendaraan_gs_id: id

        });


    const qrValue =
        formPengisianBbmUrl +
        '?' +
        params.toString();


    currentGsQrValue =
        qrValue;


    /*
    |--------------------------------------------------------------------------
    | JUDUL
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        'gsQrUnit'
    ).textContent =

        isGsUmum
            ? 'GS-UMUM'
            : kodeGs;


    /*
    |--------------------------------------------------------------------------
    | PLAT
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        'gsQrPlat'
    ).textContent =

        isGsUmum
            ? 'Kendaraan GS Umum'
            : (platNomor || '-');


    /*
    |--------------------------------------------------------------------------
    | TAMPILKAN URL
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        'gsQrValue'
    ).textContent =

        qrValue;


    /*
    |--------------------------------------------------------------------------
    | DESKRIPSI
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        'gsQrDescription'
    ).textContent =

        isGsUmum

            ? 'Scan QR ini untuk membuka form pengisian BBM kendaraan GS umum.'

            : 'Scan QR ini untuk membuka form pengisian BBM kendaraan GS.';


    /*
    |--------------------------------------------------------------------------
    | GENERATE QR
    |--------------------------------------------------------------------------
    */

    const qrContainer =
        document.getElementById(
            'gsQrcode'
        );


    qrContainer.innerHTML = '';


    new QRCode(
        qrContainer,
        {

            text: qrValue,

            width: 300,

            height: 300,

            colorDark: '#000000',

            colorLight: '#ffffff',

            correctLevel:
                QRCode.CorrectLevel.H

        }
    );


    /*
    |--------------------------------------------------------------------------
    | TAMPILKAN MODAL
    |--------------------------------------------------------------------------
    */

    const modalElement =
        document.getElementById(
            'gsQrModal'
        );


    const modal =
        bootstrap.Modal.getOrCreateInstance(
            modalElement
        );


    modal.show();

}


/*
|--------------------------------------------------------------------------
| DOWNLOAD QR
|--------------------------------------------------------------------------
*/

function downloadGsQrCode() {

    const qrContainer =
        document.getElementById(
            'gsQrcode'
        );


    const canvas =
        qrContainer.querySelector(
            'canvas'
        );


    if (!canvas) {

        alert(
            'QR Code belum siap.'
        );

        return;

    }


    const kodeGs =
        document.getElementById(
            'gsQrUnit'
        ).textContent;


    const platNomor =
        document.getElementById(
            'gsQrPlat'
        ).textContent;


    const padding = 40;

    const titleHeight = 90;

    const footerHeight = 90;


    const qrSize =
        canvas.width;


    const outputWidth =
        qrSize +
        (padding * 2);


    const outputHeight =
        titleHeight +
        qrSize +
        footerHeight +
        (padding * 2);


    const outputCanvas =
        document.createElement(
            'canvas'
        );


    outputCanvas.width =
        outputWidth;


    outputCanvas.height =
        outputHeight;


    const ctx =
        outputCanvas.getContext(
            '2d'
        );


    /*
    |--------------------------------------------------------------------------
    | BACKGROUND
    |--------------------------------------------------------------------------
    */

    ctx.fillStyle =
        '#ffffff';


    ctx.fillRect(
        0,
        0,
        outputWidth,
        outputHeight
    );


    /*
    |--------------------------------------------------------------------------
    | JUDUL
    |--------------------------------------------------------------------------
    */

    ctx.fillStyle =
        '#222222';


    ctx.textAlign =
        'center';


    ctx.font =
        'bold 28px Arial';


    ctx.fillText(
        kodeGs,
        outputWidth / 2,
        40
    );


    ctx.font =
        '18px Arial';


    ctx.fillText(
        platNomor,
        outputWidth / 2,
        68
    );


    /*
    |--------------------------------------------------------------------------
    | QR
    |--------------------------------------------------------------------------
    */

    ctx.drawImage(
        canvas,
        padding,
        titleHeight + padding
    );


    /*
    |--------------------------------------------------------------------------
    | FOOTER
    |--------------------------------------------------------------------------
    */

    ctx.font =
        '16px Arial';


    ctx.fillText(
        currentGsQrIsGeneral
            ? 'Scan QR untuk pengisian BBM GS umum'
            : 'Scan QR untuk pengisian BBM',
        outputWidth / 2,
        outputHeight - 45
    );


    /*
    |--------------------------------------------------------------------------
    | URL PENDEK
    |--------------------------------------------------------------------------
    */

    ctx.font =
        '11px Arial';


    ctx.fillText(
        'Scan QR menggunakan kamera HP',
        outputWidth / 2,
        outputHeight - 20
    );


    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD
    |--------------------------------------------------------------------------
    */

    const link =
        document.createElement(
            'a'
        );


    link.download =
        'QR-' +
        kodeGs +
        '.png';


    link.href =
        outputCanvas.toDataURL(
            'image/png'
        );


    link.click();

}

</script>

@endpush