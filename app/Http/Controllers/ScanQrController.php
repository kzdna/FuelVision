<?php

namespace App\Http\Controllers;

use App\Models\KendaraanGs;
use App\Models\KendaraanOperasional;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScanQrController extends Controller
{
    private const QR_GS_UMUM = 'GS_GENERAL';

    public function index(): View
    {
        return view('scan.index');
    }

    public function find(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_code' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $qrCode = trim($validated['qr_code']);

        $kendaraanOperasional = KendaraanOperasional::where(
            'qr_code',
            $qrCode
        )->first();

        if ($kendaraanOperasional) {
            if (! $kendaraanOperasional->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kendaraan operasional tidak aktif.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kendaraan operasional ditemukan.',
                'redirect_url' => route(
                    'transaksi-pengisian-bbm.create',
                    [
                        'jenis_kendaraan' => 'operasional',
                        'kendaraan_operasional_id' =>
                            $kendaraanOperasional->id,
                    ]
                ),
                'data' => [
                    'jenis_kendaraan' => 'operasional',
                    'id' => $kendaraanOperasional->id,
                    'kendaraan_operasional_id' =>
                        $kendaraanOperasional->id,
                    'kendaraan_gs_id' => null,
                    'kode_unit' =>
                        $kendaraanOperasional->kode_unit,
                    'plat_nomor' =>
                        $kendaraanOperasional->plat_nomor,
                    'jenis_kendaraan_nama' =>
                        $kendaraanOperasional->jenis_kendaraan,
                    'departemen' =>
                        $kendaraanOperasional->departemen,
                    'cost_center' =>
                        $kendaraanOperasional->cost_center,
                    'qr_code' =>
                        $kendaraanOperasional->qr_code,
                ],
            ]);
        }

        $kendaraanGs = KendaraanGs::where(
            'qr_code',
            $qrCode
        )->first();

        if ($kendaraanGs) {
            if (! $kendaraanGs->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kendaraan GS tidak aktif.',
                ], 422);
            }

            $isGsUmum =
                $kendaraanGs->kode_gs === 'GS-UMUM' ||
                $kendaraanGs->qr_code === self::QR_GS_UMUM;

            return response()->json([
                'success' => true,
                'message' => $isGsUmum
                    ? 'GS-UMUM ditemukan.'
                    : 'Kendaraan GS ditemukan.',
                'redirect_url' => route(
                    'transaksi-pengisian-bbm.create',
                    [
                        'jenis_kendaraan' => 'gs',
                        'kendaraan_gs_id' =>
                            $kendaraanGs->id,
                    ]
                ),
                'data' => [
                    'jenis_kendaraan' => 'gs',
                    'id' => $kendaraanGs->id,
                    'kendaraan_operasional_id' => null,
                    'kendaraan_gs_id' =>
                        $kendaraanGs->id,
                    'kode_gs' =>
                        $kendaraanGs->kode_gs,
                    'plat_nomor' =>
                        $kendaraanGs->plat_nomor,
                    'qr_code' =>
                        $kendaraanGs->qr_code,
                    'gs_umum' =>
                        $isGsUmum,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' =>
                'QR Code tidak terdaftar pada kendaraan operasional maupun kendaraan GS.',
        ], 404);
    }
}