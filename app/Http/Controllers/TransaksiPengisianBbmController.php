<?php

namespace App\Http\Controllers;

use App\Models\KendaraanGs;
use App\Models\KendaraanOperasional;
use App\Models\MasterHargaBbmVendor;
use App\Models\TransaksiPengisianBbm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TransaksiPengisianBbmController extends Controller
{
    public function index(Request $request): View
    {
        $query = TransaksiPengisianBbm::with([
            'user',
            'kendaraanOperasional',
            'kendaraanGs',
            'masterHargaBbmVendor',
        ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('driver', 'like', '%' . $search . '%')
                    ->orWhere('departemen_snapshot', 'like', '%' . $search . '%')
                    ->orWhere('cost_center_snapshot', 'like', '%' . $search . '%')
                    ->orWhere('jenis_kendaraan', 'like', '%' . $search . '%')
                    ->orWhereHas('kendaraanOperasional', function ($q) use ($search) {
                        $q->where('kode_unit', 'like', '%' . $search . '%')
                            ->orWhere('plat_nomor', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('kendaraanGs', function ($q) use ($search) {
                        $q->where('kode_gs', 'like', '%' . $search . '%')
                            ->orWhere('plat_nomor', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(
                'tanggal_pengisian',
                '>=',
                $request->tanggal_mulai
            );
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate(
                'tanggal_pengisian',
                '<=',
                $request->tanggal_akhir
            );
        }

        if ($request->filled('jenis_bbm')) {
            $query->whereHas('masterHargaBbmVendor', function ($q) use ($request) {
                $q->where(
                    'jenis_bbm',
                    $request->jenis_bbm
                );
            });
        }

        if ($request->filled('departemen')) {
            $query->where(
                'departemen_snapshot',
                $request->departemen
            );
        }

        if ($request->filled('cost_center')) {
            $query->where(
                'cost_center_snapshot',
                $request->cost_center
            );
        }

        $transaksi = $query
            ->latest('tanggal_pengisian')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $jenisBbm = MasterHargaBbmVendor::query()
            ->whereNotNull('jenis_bbm')
            ->select('jenis_bbm')
            ->distinct()
            ->orderBy('jenis_bbm')
            ->pluck('jenis_bbm');

        $departemen = TransaksiPengisianBbm::query()
            ->whereNotNull('departemen_snapshot')
            ->where(
                'departemen_snapshot',
                '!=',
                ''
            )
            ->select('departemen_snapshot')
            ->distinct()
            ->orderBy('departemen_snapshot')
            ->pluck('departemen_snapshot');

        $costCenter = TransaksiPengisianBbm::query()
            ->whereNotNull('cost_center_snapshot')
            ->where(
                'cost_center_snapshot',
                '!=',
                ''
            )
            ->select('cost_center_snapshot')
            ->distinct()
            ->orderBy('cost_center_snapshot')
            ->pluck('cost_center_snapshot');

        return view('transaksi_pengisian_bbm.index', [
            'transaksi' => $transaksi,
            'jenisBbm' => $jenisBbm,
            'departemen' => $departemen,
            'costCenter' => $costCenter,
        ]);
    }

    public function create(Request $request): View
    {
        $kendaraanOperasional = KendaraanOperasional::aktif()
            ->orderBy('kode_unit')
            ->get();

        $kendaraanGs = KendaraanGs::aktif()
            ->orderBy('kode_gs')
            ->get();

        $hargaBbm = MasterHargaBbmVendor::aktif()
            ->orderBy('jenis_bbm')
            ->get();

        $kendaraanOperasionalId = $request->query(
            'kendaraan_operasional_id'
        );

        $kendaraanGsId = $request->query(
            'kendaraan_gs_id'
        );

        $jenisKendaraan = $request->query(
            'jenis_kendaraan'
        );

        if ($kendaraanGsId) {
            $kendaraanGsTerpilih = KendaraanGs::aktif()
                ->find($kendaraanGsId);

            if (! $kendaraanGsTerpilih) {
                abort(
                    404,
                    'Kendaraan GS tidak ditemukan atau tidak aktif.'
                );
            }

            $jenisKendaraan = 'gs';
        }

        if ($kendaraanOperasionalId) {
            $kendaraanOperasionalTerpilih =
                KendaraanOperasional::aktif()
                    ->find($kendaraanOperasionalId);

            if (! $kendaraanOperasionalTerpilih) {
                abort(
                    404,
                    'Kendaraan operasional tidak ditemukan atau tidak aktif.'
                );
            }

            $jenisKendaraan = 'operasional';
        }

        return view('transaksi_pengisian_bbm.form', [
            'kendaraanOperasional' => $kendaraanOperasional,
            'kendaraanGs' => $kendaraanGs,
            'hargaBbm' => $hargaBbm,
            'kendaraanOperasionalId' =>
                $kendaraanOperasionalId,
            'kendaraanGsId' =>
                $kendaraanGsId,
            'jenisKendaraan' =>
                $jenisKendaraan,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kendaraan_operasional_id' => [
                'nullable',
                'integer',
                'exists:kendaraan_operasional,id',
            ],
            'kendaraan_gs_id' => [
                'nullable',
                'integer',
                'exists:kendaraan_gs,id',
            ],
            'jenis_kendaraan' => [
                'required',
                'in:operasional,gs',
            ],
            'master_harga_bbm_vendor_id' => [
                'required',
                'integer',
                'exists:master_harga_bbm_vendor,id',
            ],
            'driver' => [
                'required',
                'string',
                'max:100',
            ],
            'kilometer' => [
                'required',
                'integer',
                'min:0',
            ],
            'jumlah_liter' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
            'tanggal_pengisian' => [
                'required',
                'date',
            ],
        ]);

        $kendaraanOperasionalId =
            $validated['kendaraan_operasional_id'] ?? null;

        $kendaraanGsId =
            $validated['kendaraan_gs_id'] ?? null;

        $jenisKendaraan =
            $validated['jenis_kendaraan'];

        if ($jenisKendaraan === 'operasional') {

            if (! $kendaraanOperasionalId) {
                return back()
                    ->withErrors([
                        'kendaraan_operasional_id' =>
                            'Kendaraan operasional wajib dipilih.',
                    ])
                    ->withInput();
            }

            if ($kendaraanGsId) {
                return back()
                    ->withErrors([
                        'kendaraan_gs_id' =>
                            'Transaksi operasional tidak boleh memiliki kendaraan GS.',
                    ])
                    ->withInput();
            }
        }

        if ($jenisKendaraan === 'gs') {

            if (! $kendaraanGsId) {
                return back()
                    ->withErrors([
                        'kendaraan_gs_id' =>
                            'Kendaraan GS wajib dipilih.',
                    ])
                    ->withInput();
            }

            if (! $kendaraanOperasionalId) {
                return back()
                    ->withErrors([
                        'kendaraan_operasional_id' =>
                            'Untuk kendaraan GS, pilih kendaraan operasional yang menjadi kendaraan pengganti atau pembebanan BBM.',
                    ])
                    ->withInput();
            }
        }

        $kendaraanOperasional = null;
        $kendaraanGs = null;

        if ($kendaraanOperasionalId) {
            $kendaraanOperasional =
                KendaraanOperasional::aktif()
                    ->findOrFail($kendaraanOperasionalId);
        }

        if ($kendaraanGsId) {
            $kendaraanGs =
                KendaraanGs::aktif()
                    ->findOrFail($kendaraanGsId);
        }

        $queryRiwayat = TransaksiPengisianBbm::query();

        if ($jenisKendaraan === 'operasional') {
            $queryRiwayat
                ->where(
                    'kendaraan_operasional_id',
                    $kendaraanOperasionalId
                )
                ->whereNull('kendaraan_gs_id');
        }

        if ($jenisKendaraan === 'gs') {
            $queryRiwayat
                ->where('kendaraan_gs_id', $kendaraanGsId)
                ->where(
                    'kendaraan_operasional_id',
                    $kendaraanOperasionalId
                );
        }

        $kilometerTerakhir = null;

        if (
            $jenisKendaraan === 'operasional' ||
            $jenisKendaraan === 'gs'
        ) {
            $kilometerTerakhir =
                $queryRiwayat->max('kilometer');
        }

        if (
            $kilometerTerakhir !== null &&
            $validated['kilometer'] < $kilometerTerakhir
        ) {
            return back()
                ->withErrors([
                    'kilometer' =>
                        'Kilometer tidak boleh lebih kecil dari kilometer terakhir kendaraan, yaitu ' .
                        number_format(
                            $kilometerTerakhir,
                            0,
                            ',',
                            '.'
                        ) .
                        ' km.',
                ])
                ->withInput();
        }

        $hargaBbm =
            MasterHargaBbmVendor::aktif()
                ->findOrFail(
                    $validated['master_harga_bbm_vendor_id']
                );

        $totalBiaya =
            (float) $validated['jumlah_liter'] *
            (float) $hargaBbm->harga;

        $data = [
            'user_id' =>
                $request->user()?->id,

            'jenis_kendaraan' =>
                $jenisKendaraan,

            'kendaraan_operasional_id' =>
                $kendaraanOperasional?->id,

            'kendaraan_gs_id' =>
                $kendaraanGs?->id,

            'master_harga_bbm_vendor_id' =>
                $hargaBbm->id,

            'driver' =>
                $validated['driver'],

            'kilometer' =>
                $validated['kilometer'],

            'jumlah_liter' =>
                $validated['jumlah_liter'],

            'harga_bbm_snapshot' =>
                $hargaBbm->harga,

            'departemen_snapshot' =>
                $kendaraanOperasional?->departemen,

            'cost_center_snapshot' =>
                $kendaraanOperasional?->cost_center,

            'total_biaya' =>
                $totalBiaya,

            'keterangan' =>
                $validated['keterangan'] ?? null,

            'tanggal_pengisian' =>
                $validated['tanggal_pengisian'],
        ];

        DB::transaction(function () use ($data) {
            TransaksiPengisianBbm::create($data);
        });

        return redirect()
            ->route('transaksi-pengisian-bbm.create')
            ->with(
                'success',
                'Transaksi pengisian BBM berhasil disimpan.'
            );
    }

    public function edit(int $id): View
    {
        $transaksi = TransaksiPengisianBbm::with([
            'kendaraanOperasional',
            'kendaraanGs',
            'masterHargaBbmVendor',
        ])->findOrFail($id);

        $kendaraanOperasional = KendaraanOperasional::aktif()
            ->orderBy('kode_unit')
            ->get();

        $kendaraanGs = KendaraanGs::aktif()
            ->orderBy('kode_gs')
            ->get();

        $hargaBbm = MasterHargaBbmVendor::aktif()
            ->orderBy('jenis_bbm')
            ->get();

        return view('transaksi_pengisian_bbm.edit', [
            'transaksi' => $transaksi,
            'kendaraanOperasional' => $kendaraanOperasional,
            'kendaraanGs' => $kendaraanGs,
            'hargaBbm' => $hargaBbm,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $transaksi = TransaksiPengisianBbm::findOrFail($id);

        $validated = $request->validate([
            'kendaraan_operasional_id' => [
                'nullable',
                'integer',
                'exists:kendaraan_operasional,id',
            ],
            'kendaraan_gs_id' => [
                'nullable',
                'integer',
                'exists:kendaraan_gs,id',
            ],
            'jenis_kendaraan' => [
                'required',
                'in:operasional,gs',
            ],
            'master_harga_bbm_vendor_id' => [
                'required',
                'integer',
                'exists:master_harga_bbm_vendor,id',
            ],
            'driver' => [
                'required',
                'string',
                'max:100',
            ],
            'kilometer' => [
                'required',
                'integer',
                'min:0',
            ],
            'jumlah_liter' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
            'tanggal_pengisian' => [
                'required',
                'date',
            ],
        ]);

        $kendaraanOperasionalId =
            $validated['kendaraan_operasional_id'] ?? null;

        $kendaraanGsId =
            $validated['kendaraan_gs_id'] ?? null;

        $jenisKendaraan =
            $validated['jenis_kendaraan'];

        if ($jenisKendaraan === 'operasional') {
            if (! $kendaraanOperasionalId) {
                return back()
                    ->withErrors([
                        'kendaraan_operasional_id' =>
                            'Kendaraan operasional wajib dipilih.',
                    ])
                    ->withInput();
            }

            $kendaraanGsId = null;
        }

        if ($jenisKendaraan === 'gs') {
            if ($kendaraanOperasionalId && ! $kendaraanGsId) {
                return back()
                    ->withErrors([
                        'kendaraan_operasional_id' =>
                            'Pembebanan kendaraan GS belum dapat ditentukan pada transaksi ini.',
                    ])
                    ->withInput();
            }
        }

        $kendaraanOperasional = null;
        $kendaraanGs = null;

        if ($kendaraanOperasionalId) {
            $kendaraanOperasional =
                KendaraanOperasional::aktif()
                    ->findOrFail($kendaraanOperasionalId);
        }

        if ($kendaraanGsId) {
            $kendaraanGs =
                KendaraanGs::aktif()
                    ->findOrFail($kendaraanGsId);
        }

        $queryRiwayat = TransaksiPengisianBbm::query()
            ->where('id', '!=', $transaksi->id);

        if ($jenisKendaraan === 'operasional') {
            $queryRiwayat
                ->where(
                    'kendaraan_operasional_id',
                    $kendaraanOperasionalId
                )
                ->whereNull('kendaraan_gs_id');
        } elseif ($kendaraanGsId) {
            $queryRiwayat
                ->where('kendaraan_gs_id', $kendaraanGsId);
        }

        $kilometerTerakhir = null;

        if (
            $jenisKendaraan === 'operasional' ||
            $kendaraanGsId
        ) {
            $kilometerTerakhir = $queryRiwayat
                ->max('kilometer');
        }

        if (
            $kilometerTerakhir !== null &&
            $validated['kilometer'] < $kilometerTerakhir
        ) {
            return back()
                ->withErrors([
                    'kilometer' =>
                        'Kilometer tidak boleh lebih kecil dari kilometer terakhir kendaraan, yaitu ' .
                        number_format(
                            $kilometerTerakhir,
                            0,
                            ',',
                            '.'
                        ) .
                        ' km.',
                ])
                ->withInput();
        }

        $hargaBbm =
            MasterHargaBbmVendor::aktif()
                ->findOrFail(
                    $validated[
                        'master_harga_bbm_vendor_id'
                    ]
                );

        $totalBiaya =
            (float) $validated['jumlah_liter'] *
            (float) $hargaBbm->harga;

        $transaksi->update([
            'jenis_kendaraan' =>
                $jenisKendaraan,

            'kendaraan_operasional_id' =>
                $kendaraanOperasional?->id,

            'kendaraan_gs_id' =>
                $kendaraanGs?->id,

            'master_harga_bbm_vendor_id' =>
                $hargaBbm->id,

            'driver' =>
                $validated['driver'],

            'kilometer' =>
                $validated['kilometer'],

            'jumlah_liter' =>
                $validated['jumlah_liter'],

            'harga_bbm_snapshot' =>
                $hargaBbm->harga,

            'departemen_snapshot' =>
                $kendaraanOperasional?->departemen,

            'cost_center_snapshot' =>
                $kendaraanOperasional?->cost_center,

            'total_biaya' =>
                $totalBiaya,

            'keterangan' =>
                $validated['keterangan'] ?? null,

            'tanggal_pengisian' =>
                $validated['tanggal_pengisian'],
        ]);

        return redirect()
            ->route('transaksi-pengisian-bbm.index')
            ->with(
                'success',
                'Transaksi pengisian BBM berhasil diperbarui.'
            );
    }

    public function destroy(int $id): RedirectResponse
    {
        $transaksi = TransaksiPengisianBbm::findOrFail($id);

        DB::transaction(function () use ($transaksi) {
            $transaksi->delete();
        });

        return redirect()
            ->route('transaksi-pengisian-bbm.index')
            ->with(
                'success',
                'Transaksi pengisian BBM berhasil dihapus.'
            );
    }
}