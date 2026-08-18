<?php

namespace App\Http\Controllers;

use App\Models\KendaraanOperasional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KendaraanOperasionalController extends Controller
{
    public function index(): View
    {
        $kendaraan = KendaraanOperasional::latest('id')->get();

        return view('kendaraan_operasional.index', [
            'kendaraan' => $kendaraan,
        ]);
    }

    public function create(): View
    {
        return view('kendaraan_operasional.form', [
            'kendaraan' => new KendaraanOperasional(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_unit' => [
                'required',
                'string',
                'max:20',
                'unique:kendaraan_operasional,kode_unit',
            ],
            'plat_nomor' => [
                'required',
                'string',
                'max:30',
                'unique:kendaraan_operasional,plat_nomor',
            ],
            'jenis_kendaraan' => [
                'required',
                'string',
                'max:100',
                Rule::in([
                    'Mobil Operasional',
                    'Pickup',
                    'Truck',
                    'Alat Berat',
                    'Kendaraan GS',
                ]),
            ],
            'departemen' => [
                'required',
                'string',
                Rule::in([
                    'Administration',
                    'SparePart',
                    'Service',
                ]),
            ],
            'cost_center' => [
                'required',
                'string',
                Rule::in([
                    '01-STI-ADM',
                    '01-STI-WHS',
                    '01-STI-Non PMC',
                    '01-STI-PMC',
                ]),
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ]);

        $costCenterByDepartemen = [
            'Administration' => [
                '01-STI-ADM',
            ],
            'SparePart' => [
                '01-STI-WHS',
            ],
            'Service' => [
                '01-STI-Non PMC',
                '01-STI-PMC',
            ],
        ];

        if (
            ! in_array(
                $validated['cost_center'],
                $costCenterByDepartemen[$validated['departemen']] ?? [],
                true
            )
        ) {
            return back()
                ->withErrors([
                    'cost_center' => 'Cost Center tidak sesuai dengan Departemen yang dipilih.',
                ])
                ->withInput();
        }

        $validated['qr_code'] = $validated['kode_unit'];

        KendaraanOperasional::create($validated);

        return redirect()
            ->route('kendaraan-operasional.index')
            ->with('success', 'Kendaraan operasional berhasil ditambahkan.');
    }

    public function edit(KendaraanOperasional $kendaraanOperasional): View
    {
        return view('kendaraan_operasional.form', [
            'kendaraan' => $kendaraanOperasional,
        ]);
    }

    public function update(
        Request $request,
        KendaraanOperasional $kendaraanOperasional
    ): RedirectResponse {
        $validated = $request->validate([
            'kode_unit' => [
                'required',
                'string',
                'max:20',
                'unique:kendaraan_operasional,kode_unit,' . $kendaraanOperasional->id,
            ],
            'plat_nomor' => [
                'required',
                'string',
                'max:30',
                'unique:kendaraan_operasional,plat_nomor,' . $kendaraanOperasional->id,
            ],
            'jenis_kendaraan' => [
                'required',
                'string',
                'max:100',
                Rule::in([
                    'Mobil Operasional',
                    'Pickup',
                    'Truck',
                    'Alat Berat',
                    'Kendaraan GS',
                ]),
            ],
            'departemen' => [
                'required',
                'string',
                Rule::in([
                    'Administration',
                    'SparePart',
                    'Service',
                ]),
            ],
            'cost_center' => [
                'required',
                'string',
                Rule::in([
                    '01-STI-ADM',
                    '01-STI-WHS',
                    '01-STI-Non PMC',
                    '01-STI-PMC',
                ]),
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ]);

        $costCenterByDepartemen = [
            'Administration' => [
                '01-STI-ADM',
            ],
            'SparePart' => [
                '01-STI-WHS',
            ],
            'Service' => [
                '01-STI-Non PMC',
                '01-STI-PMC',
            ],
        ];

        if (
            ! in_array(
                $validated['cost_center'],
                $costCenterByDepartemen[$validated['departemen']] ?? [],
                true
            )
        ) {
            return back()
                ->withErrors([
                    'cost_center' => 'Cost Center tidak sesuai dengan Departemen yang dipilih.',
                ])
                ->withInput();
        }

        $validated['qr_code'] = $validated['kode_unit'];

        $kendaraanOperasional->update($validated);

        return redirect()
            ->route('kendaraan-operasional.index')
            ->with('success', 'Kendaraan operasional berhasil diperbarui.');
    }

    public function destroy(
        KendaraanOperasional $kendaraanOperasional
    ): RedirectResponse {
        $kendaraanOperasional->delete();

        return redirect()
            ->route('kendaraan-operasional.index')
            ->with('success', 'Kendaraan operasional berhasil dihapus.');
    }
}