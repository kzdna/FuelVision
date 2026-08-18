@extends('layouts.app')

@section('title', 'Scan QR Code')

@section('content')

<div class="mb-4">
    <h2>Scan QR Code Kendaraan</h2>

    <p class="text-muted mb-0">
        Scan QR Code kendaraan untuk melanjutkan pengisian BBM.
    </p>
</div>

<div class="card">
    <div class="card-body">

        <div class="row">

            <div class="col-md-7">

                <div
                    id="reader-wrapper"
                    style="
                        width: 100%;
                        max-width: 700px;
                        margin: auto;
                        overflow: hidden;
                    "
                >
                    <div id="reader"></div>
                </div>

            </div>

            <div class="col-md-5">

                <div id="result">

                    <div class="alert alert-info">
                        Menyiapkan kamera...
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

<style>
    #reader video {
        transform: scaleX(-1) !important;
        -webkit-transform: scaleX(-1) !important;
    }
</style>

<script
    src="https://unpkg.com/html5-qrcode"
    type="text/javascript"
></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const resultElement =
        document.getElementById('result');

    let scanner = null;
    let sudahScan = false;

    function tampilkanHasil(html) {

        resultElement.innerHTML = html;

    }

    function perbaikiKamera() {

        const video =
            document.querySelector('#reader video');

        if (video) {

            video.style.transform = 'none';
            video.style.webkitTransform = 'none';
            video.style.objectFit = 'cover';

        }

    }

    function mulaiScanner() {

        scanner =
            new Html5Qrcode('reader');

        const config = {

            fps: 10,

            qrbox: {
                width: 280,
                height: 280
            },

            aspectRatio: 1.0

        };

        scanner.start(

            {
                facingMode: 'environment'
            },

            config,

            function (decodedText) {

                if (sudahScan) {
                    return;
                }

                sudahScan = true;

                scanner.stop()
                    .then(function () {

                        prosesQrCode(decodedText);

                    })
                    .catch(function () {

                        prosesQrCode(decodedText);

                    });

            },

            function () {

                perbaikiKamera();

            }

        )
        .then(function () {

            perbaikiKamera();

            tampilkanHasil(`
                <div class="alert alert-success">

                    <strong>Kamera aktif.</strong>

                    <br>

                    Arahkan QR Code kendaraan
                    ke kotak scanner.

                </div>
            `);

        })
        .catch(function (error) {

            tampilkanHasil(`
                <div class="alert alert-danger">

                    <strong>
                        Kamera tidak dapat digunakan.
                    </strong>

                    <br>

                    Pastikan izin kamera
                    sudah diberikan.

                    <br><br>

                    <small>
                        ${error}
                    </small>

                </div>
            `);

        });

    }

    function prosesQrCode(qrCode) {

        tampilkanHasil(`
            <div class="alert alert-info">

                <strong>
                    QR Code berhasil dibaca.
                </strong>

                <br>

                Memeriksa data kendaraan...

            </div>
        `);

        fetch(
            '{{ route('scan.find') }}',
            {
                method: 'POST',

                headers: {

                    'Content-Type':
                        'application/json',

                    'Accept':
                        'application/json',

                    'X-CSRF-TOKEN':
                        '{{ csrf_token() }}'

                },

                body: JSON.stringify({

                    qr_code: qrCode

                })

            }
        )

        .then(async function (response) {

            const data =
                await response.json();

            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'QR Code tidak valid.'
                );

            }

            return data;

        })

        .then(function (data) {

            if (!data.success) {

                throw new Error(
                    data.message ||
                    'Data kendaraan tidak ditemukan.'
                );

            }

            const kendaraan =
                data.data;

            /*
             * =====================================================
             * KENDARAAN GS
             * =====================================================
             */

            if (
                kendaraan.jenis_kendaraan === 'gs'
            ) {

                tampilkanHasil(`

                    <div class="alert alert-success">

                        <h5 class="mb-3">
                            Kendaraan GS ditemukan
                        </h5>

                        <div class="mb-2">

                            <strong>
                                Kode GS:
                            </strong>

                            ${kendaraan.kode_gs ?? '-'}

                        </div>

                        <div class="mb-2">

                            <strong>
                                Plat Nomor:
                            </strong>

                            ${
                                kendaraan.plat_nomor
                                || 'GS Umum'
                            }

                        </div>

                        <div class="mb-2">

                            <strong>
                                QR Code:
                            </strong>

                            ${kendaraan.qr_code ?? '-'}

                        </div>

                        <div class="mt-3">

                            <div
                                class="spinner-border spinner-border-sm text-primary me-2"
                                role="status"
                            ></div>

                            <span>
                                Mengarahkan ke form
                                pengisian BBM...
                            </span>

                        </div>

                    </div>

                `);

                /*
                 * redirect_url dibuat oleh
                 * ScanQrController.
                 *
                 * Contoh:
                 *
                 * /transaksi-pengisian-bbm/create
                 * ?jenis_kendaraan=gs
                 * &kendaraan_gs_id=1
                 */

                if (!data.redirect_url) {

                    throw new Error(
                        'URL form pengisian BBM tidak tersedia.'
                    );

                }

                setTimeout(function () {

                    window.location.href =
                        data.redirect_url;

                }, 700);

                return;

            }

            /*
             * =====================================================
             * KENDARAAN OPERASIONAL
             * =====================================================
             */

            if (
                kendaraan.jenis_kendaraan ===
                'operasional'
            ) {

                tampilkanHasil(`

                    <div class="alert alert-success">

                        <h5 class="mb-3">
                            Kendaraan ditemukan
                        </h5>

                        <div class="mb-2">

                            <strong>
                                Kode Unit:
                            </strong>

                            ${kendaraan.kode_unit ?? '-'}

                        </div>

                        <div class="mb-2">

                            <strong>
                                Plat Nomor:
                            </strong>

                            ${kendaraan.plat_nomor ?? '-'}

                        </div>

                        <div class="mb-2">

                            <strong>
                                Jenis:
                            </strong>

                            ${
                                kendaraan.jenis_kendaraan_nama
                                ?? '-'
                            }

                        </div>

                        <div class="mb-2">

                            <strong>
                                Departemen:
                            </strong>

                            ${kendaraan.departemen ?? '-'}

                        </div>

                        <div class="mb-3">

                            <strong>
                                Cost Center:
                            </strong>

                            ${kendaraan.cost_center ?? '-'}

                        </div>

                        <div class="mt-3">

                            <div
                                class="spinner-border spinner-border-sm text-primary me-2"
                                role="status"
                            ></div>

                            <span>
                                Mengarahkan ke form
                                pengisian BBM...
                            </span>

                        </div>

                    </div>

                `);

                if (!data.redirect_url) {

                    throw new Error(
                        'URL form pengisian BBM tidak tersedia.'
                    );

                }

                setTimeout(function () {

                    window.location.href =
                        data.redirect_url;

                }, 700);

                return;

            }

            throw new Error(
                'Jenis kendaraan tidak dikenali.'
            );

        })

        .catch(function (error) {

            console.error(
                'Scan QR Error:',
                error
            );

            tampilkanHasil(`

                <div class="alert alert-danger">

                    <strong>
                        QR Code tidak dapat diproses.
                    </strong>

                    <br>

                    ${error.message}

                    <div class="mt-3">

                        <button
                            type="button"
                            class="btn btn-secondary btn-sm"
                            onclick="location.reload()"
                        >
                            Scan Ulang
                        </button>

                    </div>

                </div>

            `);

        });

    }

    mulaiScanner();

});
</script>

@endsection