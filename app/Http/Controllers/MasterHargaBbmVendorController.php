<?php

namespace App\Http\Controllers;

use App\Models\MasterHargaBbmVendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterHargaBbmVendorController extends Controller
{
    public function index(): View
    {
        $hargaBbm = MasterHargaBbmVendor::latest('id')->get();

        return view('master_harga_bbm_vendor.index', [
            'hargaBbm' => $hargaBbm,
        ]);
    }

    public function create(): View
    {
        return view('master_harga_bbm_vendor.form', [
            'hargaBbm' => new MasterHargaBbmVendor(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_bbm' => [
                'required',
                'string',
                'max:50',
                'unique:master_harga_bbm_vendor,jenis_bbm',
            ],
            'harga' => [
                'required',
                'numeric',
                'min:0',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ]);

        MasterHargaBbmVendor::create($validated);

        return redirect()
            ->route('master-harga-bbm-vendor.index')
            ->with('success', 'Harga BBM berhasil ditambahkan.');
    }

    public function edit(MasterHargaBbmVendor $masterHargaBbmVendor): View
    {
        return view('master_harga_bbm_vendor.form', [
            'hargaBbm' => $masterHargaBbmVendor,
        ]);
    }

    public function update(
        Request $request,
        MasterHargaBbmVendor $masterHargaBbmVendor
    ): RedirectResponse {
        $validated = $request->validate([
            'jenis_bbm' => [
                'required',
                'string',
                'max:50',
                'unique:master_harga_bbm_vendor,jenis_bbm,' . $masterHargaBbmVendor->id,
            ],
            'harga' => [
                'required',
                'numeric',
                'min:0',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ]);

        $masterHargaBbmVendor->update($validated);

        return redirect()
            ->route('master-harga-bbm-vendor.index')
            ->with('success', 'Harga BBM berhasil diperbarui.');
    }

    public function destroy(
        MasterHargaBbmVendor $masterHargaBbmVendor
    ): RedirectResponse {
        $masterHargaBbmVendor->delete();

        return redirect()
            ->route('master-harga-bbm-vendor.index')
            ->with('success', 'Harga BBM berhasil dihapus.');
    }
}