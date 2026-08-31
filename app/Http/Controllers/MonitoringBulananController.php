<?php

namespace App\Http\Controllers;

use App\Models\KendaraanGs;
use App\Models\KendaraanOperasional;
use App\Models\MasterHargaBbmVendor;
use App\Models\StandarKonsumsiBbm;
use App\Models\TransaksiPengisianBbm;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class MonitoringBulananController extends Controller
{
    public function index(Request $request): View
    {
        $data = $this->getMonitoringData($request, false);

        return view('monitoring.bulanan', $data);
    }

    public function aiInsight(Request $request): View
    {
        $data = $this->getMonitoringData($request, true);

        return view('monitoring.ai-insight', $data);
    }

    public function downloadPdf(Request $request)
    {
        $data = $this->getMonitoringData($request, false);

        $pdf = Pdf::loadView(
            'monitoring.bulanan-pdf',
            $data
        );

        $pdf->setPaper('a4', 'landscape');

        $namaFile =
            'summary-monitoring-bbm-bulanan-' .
            $data['awalBulan']->format('Y-m') .
            '.pdf';

        return $pdf->download($namaFile);
    }

    private function getMonitoringData(
        Request $request,
        bool $includeAi = false
    ): array {
        /*
        |--------------------------------------------------------------------------
        | PERIODE BULAN
        |--------------------------------------------------------------------------
        */

        $tanggalMulai = $request->query('tanggal_mulai');

        if ($tanggalMulai) {
            try {
                $awalBulan = Carbon::parse($tanggalMulai)
                    ->startOfMonth();
            } catch (\Exception $e) {
                $awalBulan = Carbon::now()
                    ->startOfMonth();
            }
        } else {
            $awalBulan = Carbon::now()
                ->startOfMonth();
        }

        /*
        |--------------------------------------------------------------------------
        | PENTING:
        | Gunakan startOfDay() dan endOfDay() supaya transaksi pada tanggal
        | terakhir bulan tetap ikut terbaca, termasuk jika kolom database
        | menyimpan jam.
        |--------------------------------------------------------------------------
        */

        $akhirBulan = $awalBulan
            ->copy()
            ->endOfMonth();

        $tanggalAwalQuery = $awalBulan
            ->copy()
            ->startOfDay();

        $tanggalAkhirQuery = $akhirBulan
            ->copy()
            ->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | MASTER DATA
        |--------------------------------------------------------------------------
        */

        $kendaraanOperasional = KendaraanOperasional::aktif()
            ->orderBy('kode_unit')
            ->get();

        $kendaraanGs = KendaraanGs::aktif()
            ->orderBy('kode_gs')
            ->get();

        $hargaBbm = MasterHargaBbmVendor::aktif()
            ->orderBy('jenis_bbm')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $kendaraanFilter = $request->query(
            'kendaraan',
            'all'
        );

        $jenisBbmFilter = $request->query(
            'jenis_bbm',
            'all'
        );

        /*
        |--------------------------------------------------------------------------
        | QUERY TRANSAKSI
        |--------------------------------------------------------------------------
        */

        $query = TransaksiPengisianBbm::with([
            'kendaraanOperasional',
            'kendaraanGs',
            'masterHargaBbmVendor',
        ])->whereBetween('tanggal_pengisian', [
            $tanggalAwalQuery,
            $tanggalAkhirQuery,
        ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER KENDARAAN OPERASIONAL
        |--------------------------------------------------------------------------
        */

        if (
            $kendaraanFilter !== 'all' &&
            str_starts_with(
                $kendaraanFilter,
                'operasional:'
            )
        ) {
            $kendaraanId = (int) str_replace(
                'operasional:',
                '',
                $kendaraanFilter
            );

            $query->where(
                'kendaraan_operasional_id',
                $kendaraanId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER KENDARAAN GS
        |--------------------------------------------------------------------------
        */

        if (
            $kendaraanFilter !== 'all' &&
            str_starts_with(
                $kendaraanFilter,
                'gs:'
            )
        ) {
            $kendaraanId = (int) str_replace(
                'gs:',
                '',
                $kendaraanFilter
            );

            $query->where(
                'kendaraan_gs_id',
                $kendaraanId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER JENIS BBM
        |--------------------------------------------------------------------------
        */

        if ($jenisBbmFilter !== 'all') {
            $query->where(
                'master_harga_bbm_vendor_id',
                $jenisBbmFilter
            );
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL TRANSAKSI
        |--------------------------------------------------------------------------
        */

        $transaksi = $query
            ->orderBy('tanggal_pengisian')
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        $totalTransaksi = $transaksi->count();

        $totalLiter =
            (float) $transaksi->sum('jumlah_liter');

        $totalBiaya =
            (float) $transaksi->sum('total_biaya');

        $rataRataLiter =
            $totalTransaksi > 0
                ? $totalLiter / $totalTransaksi
                : 0;

        /*
        |--------------------------------------------------------------------------
        | MONITORING PER KENDARAAN OPERASIONAL
        |--------------------------------------------------------------------------
        */

        $perKendaraan = $transaksi
            ->filter(function ($item) {
                return $item->kendaraan_operasional_id !== null;
            })
            ->groupBy(function ($item) {
                return $item->kendaraan_operasional_id;
            })
            ->map(function ($items) use ($awalBulan) {

                $itemPertama = $items->first();

                $kendaraanOperasional =
                    $itemPertama->kendaraanOperasional;

                $namaKendaraan =
                    $kendaraanOperasional?->kode_unit
                    ?? '-';

                $jenisKendaraan =
                    $kendaraanOperasional?->jenis_kendaraan
                    ?? '-';

                $totalLiter =
                    (float) $items->sum('jumlah_liter');

                $totalBiaya =
                    (float) $items->sum('total_biaya');

                /*
                |--------------------------------------------------------------------------
                | TRANSAKSI YANG MEMILIKI KM
                |--------------------------------------------------------------------------
                */

                $transaksiDenganKm = $items
                    ->filter(function ($item) {
                        return $item->kilometer !== null;
                    })
                    ->sortBy(function ($item) {
                        return [
                            $item->kilometer,
                            $item->tanggal_pengisian,
                            $item->id,
                        ];
                    })
                    ->values();

                $kmSebelumnya = null;
                $kmSekarang = null;
                $totalJarak = null;
                $literUntukRasio = 0;

                /*
                |--------------------------------------------------------------------------
                | JIKA ADA 2 ATAU LEBIH TRANSAKSI DENGAN KM
                |--------------------------------------------------------------------------
                */

                if ($transaksiDenganKm->count() >= 2) {

                    $kmSebelumnya =
                        (float) $transaksiDenganKm
                            ->first()
                            ->kilometer;

                    $kmSekarang =
                        (float) $transaksiDenganKm
                            ->last()
                            ->kilometer;

                    $totalJarak =
                        $kmSekarang -
                        $kmSebelumnya;

                    if ($totalJarak >= 0) {
                        $literUntukRasio =
                            (float) $transaksiDenganKm
                                ->sum('jumlah_liter');
                    } else {
                        $totalJarak = null;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | JIKA HANYA ADA 1 TRANSAKSI DENGAN KM
                |--------------------------------------------------------------------------
                */

                elseif ($transaksiDenganKm->count() === 1) {

                    $kmSekarang =
                        (float) $transaksiDenganKm
                            ->first()
                            ->kilometer;

                    /*
                    |--------------------------------------------------------------------------
                    | CARI TRANSAKSI SEBELUM BULAN INI
                    |--------------------------------------------------------------------------
                    */

                    $transaksiSebelumnya =
                        TransaksiPengisianBbm::query()
                            ->where(
                                'kendaraan_operasional_id',
                                $kendaraanOperasional->id
                            )
                            ->where(
                                'tanggal_pengisian',
                                '<',
                                $awalBulan->copy()->startOfMonth()
                            )
                            ->whereNotNull('kilometer')
                            ->orderByDesc('tanggal_pengisian')
                            ->orderByDesc('id')
                            ->first();

                    if ($transaksiSebelumnya) {

                        $kmSebelumnya =
                            (float) $transaksiSebelumnya
                                ->kilometer;

                        $totalJarak =
                            $kmSekarang -
                            $kmSebelumnya;

                        if ($totalJarak >= 0) {
                            $literUntukRasio =
                                (float) $transaksiDenganKm
                                    ->first()
                                    ->jumlah_liter;
                        } else {
                            $totalJarak = null;
                        }
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | RASIO AKTUAL
                |--------------------------------------------------------------------------
                */

                $rasioAktual = null;

                if (
                    $totalJarak !== null &&
                    $totalJarak > 0 &&
                    $literUntukRasio > 0
                ) {
                    $rasioAktual =
                        $totalJarak /
                        $literUntukRasio;
                }

                /*
                |--------------------------------------------------------------------------
                | STANDAR KONSUMSI
                |--------------------------------------------------------------------------
                */

                $standarKonsumsi = null;

                if ($jenisKendaraan !== '-') {
                    $standarKonsumsi =
                        StandarKonsumsiBbm::query()
                            ->where(
                                'jenis_kendaraan',
                                $jenisKendaraan
                            )
                            ->where(
                                'status',
                                true
                            )
                            ->first();
                }

                $rasioStandarMin = null;
                $rasioStandarMax = null;

                if ($standarKonsumsi) {

                    $rasioStandarMin =
                        $standarKonsumsi
                            ->standar_min_km_per_liter;

                    $rasioStandarMax =
                        $standarKonsumsi
                            ->standar_max_km_per_liter;

                    if ($rasioStandarMin !== null) {
                        $rasioStandarMin =
                            (float) $rasioStandarMin;
                    }

                    if ($rasioStandarMax !== null) {
                        $rasioStandarMax =
                            (float) $rasioStandarMax;
                    }
                }

                $rasioStandar =
                    $rasioStandarMin;

                /*
                |--------------------------------------------------------------------------
                | EVALUASI
                |--------------------------------------------------------------------------
                */

                $evaluasi = null;

                if (
                    $rasioAktual !== null &&
                    $rasioStandarMin !== null
                ) {
                    if (
                        $rasioAktual >=
                        $rasioStandarMin
                    ) {
                        $evaluasi = 'Wajar';
                    } else {
                        $evaluasi = 'Tidak Wajar';
                    }
                } elseif (
                    $rasioStandarMin !== null
                ) {
                    $evaluasi =
                        'Belum Dievaluasi';
                }

                /*
                |--------------------------------------------------------------------------
                | VARIAN PEMBOROSAN
                |--------------------------------------------------------------------------
                */

                $varianPemborosan = 0;

                if (
                    $rasioAktual !== null &&
                    $rasioStandarMin !== null &&
                    $rasioAktual < $rasioStandarMin &&
                    $totalJarak !== null &&
                    $rasioStandarMin > 0
                ) {
                    $literSeharusnya =
                        $totalJarak /
                        $rasioStandarMin;

                    $varianPemborosan =
                        max(
                            0,
                            $literUntukRasio -
                            $literSeharusnya
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | ESTIMASI BIAYA KERUGIAN
                |--------------------------------------------------------------------------
                */

                $biayaKerugian = 0;

                if (
                    $varianPemborosan > 0 &&
                    $literUntukRasio > 0 &&
                    $totalLiter > 0
                ) {
                    $hargaPerLiter =
                        $totalBiaya /
                        $totalLiter;

                    $biayaKerugian =
                        $varianPemborosan *
                        $hargaPerLiter;
                }

                return [
                    'kendaraan' =>
                        $namaKendaraan,

                    'jenis_kendaraan' =>
                        $jenisKendaraan,

                    'total_transaksi' =>
                        $items->count(),

                    'total_liter' =>
                        $totalLiter,

                    'liter_untuk_rasio' =>
                        $literUntukRasio,

                    'total_biaya' =>
                        $totalBiaya,

                    'km_sebelumnya' =>
                        $kmSebelumnya,

                    'km_sekarang' =>
                        $kmSekarang,

                    'total_jarak' =>
                        $totalJarak,

                    'rasio_aktual' =>
                        $rasioAktual,

                    'rasio_standar' =>
                        $rasioStandar,

                    'rasio_standar_min' =>
                        $rasioStandarMin,

                    'rasio_standar_max' =>
                        $rasioStandarMax,

                    'evaluasi' =>
                        $evaluasi,

                    'varian_pemborosan' =>
                        $varianPemborosan,

                    'biaya_kerugian' =>
                        $biayaKerugian,
                ];
            })
            ->sortBy('kendaraan')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | PERSENTASE PER KENDARAAN
        |--------------------------------------------------------------------------
        */

        $totalLiterSemuaKendaraan =
            $perKendaraan->sum('total_liter');

        $perKendaraan = $perKendaraan
            ->map(function ($item) use (
                $totalLiterSemuaKendaraan
            ) {
                $item['persentase'] =
                    $totalLiterSemuaKendaraan > 0
                        ? (
                            $item['total_liter'] /
                            $totalLiterSemuaKendaraan
                        ) * 100
                        : 0;

                return $item;
            });

        /*
        |--------------------------------------------------------------------------
        | PER JENIS KENDARAAN
        |--------------------------------------------------------------------------
        */

        $perJenisKendaraan =
            $transaksi
                ->groupBy(function ($item) {

                    if ($item->kendaraanOperasional) {
                        return $item
                            ->kendaraanOperasional
                            ->jenis_kendaraan
                            ?? 'Kendaraan Operasional';
                    }

                    if ($item->kendaraanGs) {
                        return 'Kendaraan GS';
                    }

                    return '-';
                })
                ->map(function ($items) {
                    return [
                        'total_transaksi' =>
                            $items->count(),

                        'total_liter' =>
                            $items->sum('jumlah_liter'),

                        'total_biaya' =>
                            $items->sum('total_biaya'),
                    ];
                });

        /*
        |--------------------------------------------------------------------------
        | PER JENIS BBM
        |--------------------------------------------------------------------------
        */

        $perBbm =
            $transaksi
                ->groupBy(function ($item) {
                    return $item
                        ->masterHargaBbmVendor
                        ?->jenis_bbm ?? '-';
                })
                ->map(function ($items) {
                    return [
                        'total_transaksi' =>
                            $items->count(),

                        'total_liter' =>
                            $items->sum('jumlah_liter'),

                        'total_biaya' =>
                            $items->sum('total_biaya'),
                    ];
                });

        /*
        |--------------------------------------------------------------------------
        | PER DEPARTEMEN
        |--------------------------------------------------------------------------
        */

        $perDepartemen =
            $transaksi
                ->groupBy(function ($item) {

                    $departemen = trim(
                        (string)
                        $item->departemen_snapshot
                    );

                    return $departemen !== ''
                        ? $departemen
                        : 'Belum Ditentukan';
                })
                ->map(function ($items) {
                    return [
                        'total_transaksi' =>
                            $items->count(),

                        'total_liter' =>
                            $items->sum('jumlah_liter'),

                        'total_biaya' =>
                            $items->sum('total_biaya'),
                    ];
                });

        /*
        |--------------------------------------------------------------------------
        | PER COST CENTER
        |--------------------------------------------------------------------------
        */

        $perCostCenter =
            $transaksi
                ->groupBy(function ($item) {

                    $costCenter = trim(
                        (string)
                        $item->cost_center_snapshot
                    );

                    return $costCenter !== ''
                        ? $costCenter
                        : 'Belum Ditentukan';
                })
                ->map(function ($items) {
                    return [
                        'total_transaksi' =>
                            $items->count(),

                        'total_liter' =>
                            $items->sum('jumlah_liter'),

                        'total_biaya' =>
                            $items->sum('total_biaya'),
                    ];
                });

        /*
        |--------------------------------------------------------------------------
        | AI INSIGHT
        |--------------------------------------------------------------------------
        */

        $aiSummary = null;
        $aiError = null;

        $apiKey = env('GEMINI_API_KEY');

        if (
            $apiKey &&
            $totalTransaksi > 0
        ) {

            $kendaraanSummary =
                $perKendaraan
                    ->map(function ($item) {
                        return [
                            'kendaraan' =>
                                $item['kendaraan'],

                            'jenis_kendaraan' =>
                                $item['jenis_kendaraan'],

                            'transaksi' =>
                                $item['total_transaksi'],

                            'liter' =>
                                (float)
                                $item['total_liter'],

                            'biaya' =>
                                (float)
                                $item['total_biaya'],

                            'km_sebelumnya' =>
                                $item['km_sebelumnya'],

                            'km_sekarang' =>
                                $item['km_sekarang'],

                            'total_jarak' =>
                                $item['total_jarak'],

                            'rasio_aktual' =>
                                $item['rasio_aktual'],

                            'rasio_standar' =>
                                $item['rasio_standar'],

                            'evaluasi' =>
                                $item['evaluasi'],

                            'varian_pemborosan' =>
                                $item['varian_pemborosan'],

                            'biaya_kerugian' =>
                                $item['biaya_kerugian'],
                        ];
                    })
                    ->values()
                    ->toArray();

            $bbmSummary =
                $perBbm
                    ->map(function (
                        $item,
                        $jenis
                    ) {
                        return [
                            'jenis_bbm' =>
                                $jenis,

                            'transaksi' =>
                                $item['total_transaksi'],

                            'liter' =>
                                (float)
                                $item['total_liter'],

                            'biaya' =>
                                (float)
                                $item['total_biaya'],
                        ];
                    })
                    ->values()
                    ->toArray();

            $departemenSummary =
                $perDepartemen
                    ->map(function (
                        $item,
                        $departemen
                    ) {
                        return [
                            'departemen' =>
                                $departemen,

                            'transaksi' =>
                                $item['total_transaksi'],

                            'liter' =>
                                (float)
                                $item['total_liter'],

                            'biaya' =>
                                (float)
                                $item['total_biaya'],
                        ];
                    })
                    ->values()
                    ->toArray();

            $costCenterSummary =
                $perCostCenter
                    ->map(function (
                        $item,
                        $costCenter
                    ) {
                        return [
                            'cost_center' =>
                                $costCenter,

                            'transaksi' =>
                                $item['total_transaksi'],

                            'liter' =>
                                (float)
                                $item['total_liter'],

                            'biaya' =>
                                (float)
                                $item['total_biaya'],
                        ];
                    })
                    ->values()
                    ->toArray();

            $filterKendaraanText =
                $kendaraanFilter === 'all'
                    ? 'Semua kendaraan'
                    : $kendaraanFilter;

            $filterBbmText =
                $jenisBbmFilter === 'all'
                    ? 'Semua jenis BBM'
                    : $jenisBbmFilter;

            /*
            |--------------------------------------------------------------------------
            | CACHE AI
            |--------------------------------------------------------------------------
            */

            $cacheKey =
                'ai_insight_bbm_' .
                md5(
                    json_encode([
                        'bulan' =>
                            $awalBulan->format('Y-m'),

                        'kendaraan' =>
                            $kendaraanFilter,

                        'jenis_bbm' =>
                            $jenisBbmFilter,

                        'total_transaksi' =>
                            $totalTransaksi,

                        'total_liter' =>
                            $totalLiter,

                        'total_biaya' =>
                            $totalBiaya,
                    ])
                );

            $aiSummary =
                Cache::get($cacheKey);

            if (! $aiSummary) {

                $prompt =
                    'Anda adalah asisten analisis penggunaan BBM untuk sistem FuelVision.

Buat ringkasan analisis penggunaan BBM berdasarkan data yang diberikan.

Periode: ' .
                    $awalBulan->format('d/m/Y') .
                    ' - ' .
                    $akhirBulan->format('d/m/Y') . '

Filter kendaraan: ' .
                    $filterKendaraanText . '

Filter jenis BBM: ' .
                    $filterBbmText . '

Data utama:
- Total transaksi: ' .
                    $totalTransaksi . '
- Total liter: ' .
                    number_format(
                        $totalLiter,
                        2,
                        ',',
                        '.'
                    ) . ' L
- Total biaya: Rp ' .
                    number_format(
                        $totalBiaya,
                        0,
                        ',',
                        '.'
                    ) . '
- Rata-rata liter per transaksi: ' .
                    number_format(
                        $rataRataLiter,
                        2,
                        ',',
                        '.'
                    ) . ' L

Penggunaan dan evaluasi berdasarkan kendaraan:
' .
                    json_encode(
                        $kendaraanSummary,
                        JSON_PRETTY_PRINT
                    ) . '

Penggunaan berdasarkan jenis BBM:
' .
                    json_encode(
                        $bbmSummary,
                        JSON_PRETTY_PRINT
                    ) . '

Penggunaan berdasarkan departemen:
' .
                    json_encode(
                        $departemenSummary,
                        JSON_PRETTY_PRINT
                    ) . '

Penggunaan berdasarkan cost center:
' .
                    json_encode(
                        $costCenterSummary,
                        JSON_PRETTY_PRINT
                    ) . '

Buat jawaban dalam Bahasa Indonesia yang profesional dan mudah dipahami oleh Staff Finance.

Perhatikan evaluasi konsumsi kendaraan:
- Wajar berarti rasio aktual sama dengan atau lebih tinggi dari standar konsumsi minimum kendaraan tersebut.
- Tidak Wajar berarti rasio aktual berada di bawah standar konsumsi minimum kendaraan tersebut.
- Semakin tinggi nilai km/L menunjukkan konsumsi BBM yang semakin efisien.
- Setiap kendaraan harus dievaluasi menggunakan standar konsumsi sesuai jenis kendaraannya masing-masing.

Jangan membuat angka baru.
Jangan melakukan perhitungan yang tidak berdasarkan data.
Jangan memberikan diagnosis kerusakan kendaraan.
Jangan menyebut bahwa data berasal dari AI.
Gunakan angka yang tersedia pada data.

Format jawaban:
1. Ringkasan umum penggunaan BBM bulanan.
2. Kendaraan dengan penggunaan BBM terbesar.
3. Kendaraan dengan rasio konsumsi yang tidak wajar jika ada.
4. Jenis BBM yang paling banyak digunakan.
5. Kesimpulan dan hal yang perlu diperhatikan.

Jawaban maksimal 5 paragraf.';

                try {

                    $response =
                        Http::timeout(30)
                            ->withHeaders([
                                'x-goog-api-key' =>
                                    $apiKey,

                                'Content-Type' =>
                                    'application/json',
                            ])
                            ->post(
                                'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent',
                                [
                                    'contents' => [
                                        [
                                            'parts' => [
                                                [
                                                    'text' =>
                                                        $prompt,
                                                ],
                                            ],
                                        ],
                                    ],
                                ]
                            );

                    if ($response->successful()) {

                        $aiSummary =
                            $response->json(
                                'candidates.0.content.parts.0.text'
                            );

                        if (! $aiSummary) {

                            $aiSummary =
                                $this->generateLocalInsight(
                                    $totalTransaksi,
                                    $totalLiter,
                                    $totalBiaya,
                                    $rataRataLiter,
                                    $perKendaraan,
                                    $perBbm,
                                    $perDepartemen
                                );

                            $aiError = null;

                        } else {

                            Cache::put(
                                $cacheKey,
                                $aiSummary,
                                now()->addMinutes(30)
                            );

                            $aiError = null;
                        }

                    } else {

                        $aiSummary =
                            $this->generateLocalInsight(
                                $totalTransaksi,
                                $totalLiter,
                                $totalBiaya,
                                $rataRataLiter,
                                $perKendaraan,
                                $perBbm,
                                $perDepartemen
                            );

                        $aiError = null;
                    }

                } catch (\Throwable $e) {

                    $aiSummary =
                        $this->generateLocalInsight(
                            $totalTransaksi,
                            $totalLiter,
                            $totalBiaya,
                            $rataRataLiter,
                            $perKendaraan,
                            $perBbm,
                            $perDepartemen
                        );

                    $aiError = null;
                }
            }

        } elseif (! $apiKey) {

            $aiError =
                'GEMINI_API_KEY belum ditemukan di file .env.';

            if ($totalTransaksi > 0) {

                $aiSummary =
                    $this->generateLocalInsight(
                        $totalTransaksi,
                        $totalLiter,
                        $totalBiaya,
                        $rataRataLiter,
                        $perKendaraan,
                        $perBbm,
                        $perDepartemen
                    );
            }

        } elseif ($totalTransaksi <= 0) {

            $aiError =
                'Tidak ada transaksi pada bulan dan filter yang dipilih.';
        }

        /*
        |--------------------------------------------------------------------------
        | DATA UNTUK VIEW
        |--------------------------------------------------------------------------
        */

        return [
            'awalBulan' =>
                $awalBulan,

            'akhirBulan' =>
                $akhirBulan,

            'kendaraanFilter' =>
                $kendaraanFilter,

            'jenisBbmFilter' =>
                $jenisBbmFilter,

            'kendaraanOperasional' =>
                $kendaraanOperasional,

            'kendaraanGs' =>
                $kendaraanGs,

            'hargaBbm' =>
                $hargaBbm,

            'totalTransaksi' =>
                $totalTransaksi,

            'totalLiter' =>
                $totalLiter,

            'totalBiaya' =>
                $totalBiaya,

            'rataRataLiter' =>
                $rataRataLiter,

            'perKendaraan' =>
                $perKendaraan,

            'perJenisKendaraan' =>
                $perJenisKendaraan,

            'perBbm' =>
                $perBbm,

            'perDepartemen' =>
                $perDepartemen,

            'perCostCenter' =>
                $perCostCenter,

            'transaksi' =>
                $transaksi,

            'aiSummary' =>
                $aiSummary,

            'aiError' =>
                $aiError,
        ];
    }

    private function generateLocalInsight(
        int $totalTransaksi,
        float $totalLiter,
        float $totalBiaya,
        float $rataRataLiter,
        $perKendaraan,
        $perBbm,
        $perDepartemen
    ): string {

        $kendaraanTidakWajar =
            $perKendaraan
                ->filter(function ($item) {
                    return $item['evaluasi'] === 'Tidak Wajar';
                })
                ->values();

        $totalPemborosan =
            (float) $perKendaraan->sum(
                'varian_pemborosan'
            );

        $totalKerugian =
            (float) $perKendaraan->sum(
                'biaya_kerugian'
            );

        $kendaraanTerbesar =
            $perKendaraan
                ->sortByDesc('total_liter')
                ->first();

        $bbmTerbesar =
            $perBbm
                ->sortByDesc(function ($item) {
                    return (float) $item['total_liter'];
                })
                ->first();

        $departemenTerbesar =
            $perDepartemen
                ->sortByDesc(function ($item) {
                    return (float) $item['total_liter'];
                })
                ->first();

        $formatLiter = function ($value) {
            return number_format(
                (float) $value,
                2,
                ',',
                '.'
            ) . ' L';
        };

        $formatRupiah = function ($value) {
            return 'Rp ' . number_format(
                (float) $value,
                0,
                ',',
                '.'
            );
        };

        $kendaraanTerbesarText =
            $kendaraanTerbesar
                ? $kendaraanTerbesar['kendaraan'] .
                    ' dengan penggunaan ' .
                    $formatLiter(
                        $kendaraanTerbesar['total_liter']
                    )
                : 'belum dapat ditentukan';

        $bbmTerbesarText = null;

        if ($bbmTerbesar && $perBbm->count() > 0) {
            $bbmTerbesarText =
                $perBbm
                    ->sortByDesc(function ($item) {
                        return (float) $item['total_liter'];
                    })
                    ->keys()
                    ->first();
        }

        $departemenTerbesarText = null;

        if (
            $departemenTerbesar &&
            $perDepartemen->count() > 0
        ) {
            $departemenTerbesarText =
                $perDepartemen
                    ->sortByDesc(function ($item) {
                        return (float) $item['total_liter'];
                    })
                    ->keys()
                    ->first();
        }

        $insight = [];

        $insight[] =
            '1. Ringkasan umum: Pada periode yang dipilih terdapat ' .
            $totalTransaksi .
            ' transaksi pengisian BBM dengan total penggunaan ' .
            $formatLiter($totalLiter) .
            ' dan total biaya ' .
            $formatRupiah($totalBiaya) .
            '. Rata-rata penggunaan per transaksi sebesar ' .
            $formatLiter($rataRataLiter) .
            '.';

        $insight[] =
            '2. Penggunaan terbesar: Kendaraan dengan penggunaan BBM terbesar adalah ' .
            $kendaraanTerbesarText .
            '.';

        if ($kendaraanTidakWajar->count() > 0) {

            $daftarTidakWajar =
                $kendaraanTidakWajar
                    ->map(function ($item) use (
                        $formatLiter,
                        $formatRupiah
                    ) {

                        $text =
                            $item['kendaraan'];

                        if (
                            $item['rasio_aktual'] !== null &&
                            $item['rasio_standar'] !== null
                        ) {

                            $text .=
                                ' dengan rasio aktual ' .
                                number_format(
                                    (float)
                                    $item['rasio_aktual'],
                                    2,
                                    ',',
                                    '.'
                                ) .
                                ' km/L dibandingkan standar ' .
                                number_format(
                                    (float)
                                    $item['rasio_standar'],
                                    2,
                                    ',',
                                    '.'
                                ) .
                                ' km/L';
                        }

                        if (
                            (float)
                            $item['varian_pemborosan'] > 0
                        ) {

                            $text .=
                                ', pemborosan ' .
                                $formatLiter(
                                    $item[
                                        'varian_pemborosan'
                                    ]
                                ) .
                                ' dengan estimasi biaya kerugian ' .
                                $formatRupiah(
                                    $item[
                                        'biaya_kerugian'
                                    ]
                                );
                        }

                        return $text;
                    })
                    ->implode('; ');

            $insight[] =
                '3. Kendaraan yang perlu diperhatikan: Terdapat ' .
                $kendaraanTidakWajar->count() .
                ' kendaraan dengan evaluasi tidak wajar, yaitu ' .
                $daftarTidakWajar .
                '.';

        } else {

            $insight[] =
                '3. Evaluasi konsumsi: Tidak ditemukan kendaraan dengan konsumsi tidak wajar pada periode yang dipilih berdasarkan standar konsumsi yang tersedia.';
        }

        if ($bbmTerbesarText) {

            $insight[] =
                '4. Jenis BBM: Jenis BBM dengan penggunaan terbesar adalah ' .
                $bbmTerbesarText .
                '.';

        } else {

            $insight[] =
                '4. Jenis BBM: Belum terdapat data jenis BBM yang dapat dianalisis.';
        }

        if ($departemenTerbesarText) {

            $insight[] =
                '5. Fokus Finance: Departemen dengan penggunaan BBM terbesar adalah ' .
                $departemenTerbesarText .
                '. Total pemborosan yang teridentifikasi sebesar ' .
                $formatLiter($totalPemborosan) .
                ' dengan estimasi biaya kerugian sebesar ' .
                $formatRupiah($totalKerugian) .
                '.';

        } else {

            $insight[] =
                '5. Fokus Finance: Total pemborosan yang teridentifikasi sebesar ' .
                $formatLiter($totalPemborosan) .
                ' dengan estimasi biaya kerugian sebesar ' .
                $formatRupiah($totalKerugian) .
                '.';
        }

        return implode(
            "\n\n",
            $insight
        );
    }
}