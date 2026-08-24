<?php

namespace App\Http\Controllers;

use App\Models\KendaraanOperasional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KendaraanOperasionalController extends Controller
{
    /**
     * Daftar jenis kendaraan yang digunakan di database.
     */
    private function jenisKendaraan(): array
    {
        return [
            'B/U FMC JB HDX TRITON',
            'LV BOX HDX TRITON',
            'B/U FIELD HDX TRITON',
            'B/U FIELD HILUX',
            'B/U GOH HDX TRITON',
            'LV OPS ASC HDX TRITON',
            'LV OPS P2U HILUX',
            'B/U FMC BAGONG',
            'B/U FMC JB HDX TRITON SPV',
            'BUS HAICE',
        ];
    }

    /**
     * Daftar departemen dan cost center yang sesuai.
     */
    private function departemenCostCenter(): array
    {
        return [
            'Service FMC' => [
                '01-STI-FMC',
            ],
            'Spare Part' => [
                '01-STI-WHS',
            ],
            'Service Non FMC' => [
                '01-STI-Non FMC',
            ],
            'Administration' => [
                '01-STI-ADM',
            ],
        ];
    }

    /**
     * Menampilkan daftar kendaraan operasional.
     */
    public function index(): View
    {
        $kendaraan = KendaraanOperasional::latest('id')->get();

        return view('kendaraan_operasional.index', [
            'kendaraan' => $kendaraan,
        ]);
    }

    /**
     * Menampilkan form tambah kendaraan.
     */
    public function create(): View
    {
        return view('kendaraan_operasional.form', [
            'kendaraan' => new KendaraanOperasional(),
            'jenisKendaraan' => $this->jenisKendaraan(),
            'departemenCostCenter' => $this->departemenCostCenter(),
        ]);
    }

    /**
     * Menyimpan kendaraan baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $departemenCostCenter = $this->departemenCostCenter();

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
                'in:' . implode(',', $this->jenisKendaraan()),
            ],

            'departemen' => [
                'required',
                'string',
                'in:' . implode(',', array_keys($departemenCostCenter)),
            ],

            'cost_center' => [
                'required',
                'string',
                'in:' . implode(
                    ',',
                    array_unique(
                        array_merge(...array_values($departemenCostCenter))
                    )
                ),
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ]);

        // Pastikan Cost Center sesuai dengan Departemen.
        if (
            ! in_array(
                $validated['cost_center'],
                $departemenCostCenter[$validated['departemen']] ?? [],
                true
            )
        ) {
            return back()
                ->withErrors([
                    'cost_center' => 'Cost Center tidak sesuai dengan Departemen yang dipilih.',
                ])
                ->withInput();
        }

        // QR Code menggunakan kode unit kendaraan.
        $validated['qr_code'] = $validated['kode_unit'];

        KendaraanOperasional::create($validated);

        return redirect()
            ->route('kendaraan-operasional.index')
            ->with(
                'success',
                'Kendaraan operasional berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan form edit kendaraan.
     */
    public function edit(KendaraanOperasional $kendaraanOperasional): View
    {
        return view('kendaraan_operasional.form', [
            'kendaraan' => $kendaraanOperasional,
            'jenisKendaraan' => $this->jenisKendaraan(),
            'departemenCostCenter' => $this->departemenCostCenter(),
        ]);
    }

    /**
     * Memperbarui kendaraan.
     */
    public function update(
        Request $request,
        KendaraanOperasional $kendaraanOperasional
    ): RedirectResponse {
        $departemenCostCenter = $this->departemenCostCenter();

        $validated = $request->validate([
            'kode_unit' => [
                'required',
                'string',
                'max:20',
                'unique:kendaraan_operasional,kode_unit,' .
                    $kendaraanOperasional->id,
            ],

            'plat_nomor' => [
                'required',
                'string',
                'max:30',
                'unique:kendaraan_operasional,plat_nomor,' .
                    $kendaraanOperasional->id,
            ],

            'jenis_kendaraan' => [
                'required',
                'string',
                'max:100',
                'in:' . implode(',', $this->jenisKendaraan()),
            ],

            'departemen' => [
                'required',
                'string',
                'in:' . implode(',', array_keys($departemenCostCenter)),
            ],

            'cost_center' => [
                'required',
                'string',
                'in:' . implode(
                    ',',
                    array_unique(
                        array_merge(...array_values($departemenCostCenter))
                    )
                ),
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ]);

        // Pastikan Cost Center sesuai dengan Departemen.
        if (
            ! in_array(
                $validated['cost_center'],
                $departemenCostCenter[$validated['departemen']] ?? [],
                true
            )
        ) {
            return back()
                ->withErrors([
                    'cost_center' => 'Cost Center tidak sesuai dengan Departemen yang dipilih.',
                ])
                ->withInput();
        }

        // QR Code mengikuti kode unit.
        $validated['qr_code'] = $validated['kode_unit'];

        $kendaraanOperasional->update($validated);

        return redirect()
            ->route('kendaraan-operasional.index')
            ->with(
                'success',
                'Kendaraan operasional berhasil diperbarui.'
            );
    }

    /**
     * Menghapus kendaraan.
     */
    public function destroy(
        KendaraanOperasional $kendaraanOperasional
    ): RedirectResponse {
        $kendaraanOperasional->delete();

        return redirect()
            ->route('kendaraan-operasional.index')
            ->with(
                'success',
                'Kendaraan operasional berhasil dihapus.'
            );
    }
}