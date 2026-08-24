<?php

namespace App\Http\Controllers;

use App\Models\StandarKonsumsiBbm;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StandarKonsumsiBbmController extends Controller
{
    /**
     * Menampilkan daftar standar konsumsi BBM.
     */
    public function index(): View
    {
        $standarKonsumsi = StandarKonsumsiBbm::query()
            ->orderBy('jenis_kendaraan')
            ->get();

        return view(
            'standar_konsumsi_bbm.index',
            compact('standarKonsumsi')
        );
    }

    /**
     * Form tambah standar konsumsi.
     *
     * Untuk sementara tidak digunakan karena
     * jenis kendaraan sudah tersedia dari seeder.
     */
    public function create(): View
    {
        abort(404);
    }

    /**
     * Menyimpan standar konsumsi baru.
     *
     * Untuk sementara tidak digunakan.
     */
    public function store(Request $request): RedirectResponse
    {
        abort(404);
    }

    /**
     * Menampilkan detail standar konsumsi.
     *
     * Untuk sementara tidak digunakan.
     */
    public function show(string $id): View
    {
        abort(404);
    }

    /**
     * Menampilkan form edit standar konsumsi.
     */
    public function edit(string $id): View
    {
        $standarKonsumsi =
            StandarKonsumsiBbm::findOrFail($id);

        return view(
            'standar_konsumsi_bbm.edit',
            compact('standarKonsumsi')
        );
    }

    /**
     * Memperbarui standar konsumsi.
     */
    public function update(
        Request $request,
        string $id
    ): RedirectResponse {
        $standarKonsumsi =
            StandarKonsumsiBbm::findOrFail($id);

        $validated = $request->validate([
            'standar_min_km_per_liter' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'standar_max_km_per_liter' => [
                'nullable',
                'numeric',
                'min:0',
                'gte:standar_min_km_per_liter',
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'standar_max_km_per_liter.gte' =>
                'Standar maksimum tidak boleh lebih kecil dari standar minimum.',
        ]);

        $standarKonsumsi->update($validated);

        return redirect()
            ->route('standar-konsumsi-bbm.index')
            ->with(
                'success',
                'Standar konsumsi BBM berhasil diperbarui.'
            );
    }

    /**
     * Menghapus standar konsumsi.
     *
     * Untuk sementara tidak digunakan agar
     * data jenis kendaraan tidak ikut hilang.
     */
    public function destroy(string $id): RedirectResponse
    {
        abort(404);
    }
}