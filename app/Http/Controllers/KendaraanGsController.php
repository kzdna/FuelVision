<?php

namespace App\Http\Controllers;

use App\Models\KendaraanGs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KendaraanGsController extends Controller
{
    private const KODE_GS_UMUM = 'GS-UMUM';
    private const QR_GS_UMUM = 'GS_GENERAL';

    public function index(): View
    {
        $kendaraan = KendaraanGs::orderByRaw(
            "CASE WHEN kode_gs = ? THEN 0 ELSE 1 END",
            [self::KODE_GS_UMUM]
        )
            ->orderBy('kode_gs')
            ->get();

        return view('kendaraan_gs.index', [
            'kendaraan' => $kendaraan,
        ]);
    }

    public function create(): View
    {
        return view('kendaraan_gs.form', [
            'kendaraan' => new KendaraanGs(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_gs' => [
                'required',
                'string',
                'max:30',
                'unique:kendaraan_gs,kode_gs',
            ],
            'plat_nomor' => [
                'nullable',
                'string',
                'max:20',
                'unique:kendaraan_gs,plat_nomor',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ]);

        $isGsUmum =
            strtoupper(trim($validated['kode_gs'])) === self::KODE_GS_UMUM;

        if ($isGsUmum) {
            $gsUmumSudahAda = KendaraanGs::where(
                'kode_gs',
                self::KODE_GS_UMUM
            )->exists();

            if ($gsUmumSudahAda) {
                return back()
                    ->withErrors([
                        'kode_gs' =>
                            'Data GS-UMUM sudah tersedia. GS-UMUM hanya dapat dibuat satu kali.',
                    ])
                    ->withInput();
            }

            $validated['kode_gs'] = self::KODE_GS_UMUM;
            $validated['plat_nomor'] = null;
            $validated['qr_code'] = self::QR_GS_UMUM;
        } else {
            $validated['qr_code'] = $validated['kode_gs'];
        }

        KendaraanGs::create($validated);

        return redirect()
            ->route('kendaraan-gs.index')
            ->with(
                'success',
                $isGsUmum
                    ? 'GS-UMUM berhasil ditambahkan.'
                    : 'Kendaraan GS berhasil ditambahkan.'
            );
    }

    public function edit(KendaraanGs $kendaraanGs): View
    {
        return view('kendaraan_gs.form', [
            'kendaraan' => $kendaraanGs,
        ]);
    }

    public function update(
        Request $request,
        KendaraanGs $kendaraanGs
    ): RedirectResponse {
        $validated = $request->validate([
            'kode_gs' => [
                'required',
                'string',
                'max:30',
                'unique:kendaraan_gs,kode_gs,' . $kendaraanGs->id,
            ],
            'plat_nomor' => [
                'nullable',
                'string',
                'max:20',
                'unique:kendaraan_gs,plat_nomor,' . $kendaraanGs->id,
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ]);

        $isGsUmum =
            strtoupper(trim($validated['kode_gs'])) === self::KODE_GS_UMUM;

        if ($isGsUmum) {
            $gsUmumSudahAda = KendaraanGs::where(
                'kode_gs',
                self::KODE_GS_UMUM
            )
                ->where('id', '!=', $kendaraanGs->id)
                ->exists();

            if ($gsUmumSudahAda) {
                return back()
                    ->withErrors([
                        'kode_gs' =>
                            'Data GS-UMUM sudah tersedia.',
                    ])
                    ->withInput();
            }

            $validated['kode_gs'] = self::KODE_GS_UMUM;
            $validated['plat_nomor'] = null;
            $validated['qr_code'] = self::QR_GS_UMUM;
        } else {
            $validated['qr_code'] = $validated['kode_gs'];
        }

        $kendaraanGs->update($validated);

        return redirect()
            ->route('kendaraan-gs.index')
            ->with(
                'success',
                $isGsUmum
                    ? 'GS-UMUM berhasil diperbarui.'
                    : 'Kendaraan GS berhasil diperbarui.'
            );
    }

    public function destroy(KendaraanGs $kendaraanGs): RedirectResponse
    {
        if ($kendaraanGs->kode_gs === self::KODE_GS_UMUM) {
            return redirect()
                ->route('kendaraan-gs.index')
                ->with(
                    'error',
                    'GS-UMUM tidak dapat dihapus karena digunakan sebagai QR umum untuk kendaraan GS.'
                );
        }

        $kendaraanGs->delete();

        return redirect()
            ->route('kendaraan-gs.index')
            ->with(
                'success',
                'Kendaraan GS berhasil dihapus.'
            );
    }
}