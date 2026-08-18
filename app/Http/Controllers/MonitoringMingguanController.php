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
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class MonitoringMingguanController extends Controller
{
    public function index(Request $request): View
    {
        $data = $this->getMonitoringData($request);

        return view('monitoring.mingguan', $data);
    }

    public function downloadPdf(Request $request)
    {
        $data = $this->getMonitoringData($request);

        $pdf = Pdf::loadView(
            'monitoring.mingguan-pdf',
            $data
        );

        $pdf->setPaper('a4', 'landscape');

        $namaFile =
            'summary-monitoring-bbm-' .
            $data['awalMinggu']->format('Y-m-d') .
            '-' .
            $data['akhirMinggu']->format('Y-m-d') .
            '.pdf';

        return $pdf->download($namaFile);
    }

    private function getMonitoringData(Request $request): array
    {
        $tanggalMulai = $request->query('tanggal_mulai');

        if ($tanggalMulai) {
            try {
                $awalMinggu = Carbon::parse($tanggalMulai)->startOfWeek();
            } catch (\Exception $e) {
                $awalMinggu = Carbon::now()->startOfWeek();
            }
        } else {
            $awalMinggu = Carbon::now()->startOfWeek();
        }

        $akhirMinggu = $awalMinggu->copy()->endOfWeek();

        $kendaraanOperasional = KendaraanOperasional::aktif()
            ->orderBy('kode_unit')
            ->get();

        $kendaraanGs = KendaraanGs::aktif()
            ->orderBy('kode_gs')
            ->get();

        $hargaBbm = MasterHargaBbmVendor::aktif()
            ->orderBy('jenis_bbm')
            ->get();

        $kendaraanFilter = $request->query('kendaraan', 'all');
        $jenisBbmFilter = $request->query('jenis_bbm', 'all');

        $query = TransaksiPengisianBbm::with([
            'kendaraanOperasional',
            'kendaraanGs',
            'masterHargaBbmVendor',
        ])->whereBetween('tanggal_pengisian', [
            $awalMinggu,
            $akhirMinggu,
        ]);

        if (
            $kendaraanFilter !== 'all' &&
            str_starts_with($kendaraanFilter, 'operasional:')
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

        if (
            $kendaraanFilter !== 'all' &&
            str_starts_with($kendaraanFilter, 'gs:')
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

        if ($jenisBbmFilter !== 'all') {
            $query->where(
                'master_harga_bbm_vendor_id',
                $jenisBbmFilter
            );
        }

        $transaksi = $query
            ->orderBy('tanggal_pengisian')
            ->orderBy('id')
            ->get();

        $totalTransaksi = $transaksi->count();

        $totalLiter = (float) $transaksi->sum('jumlah_liter');

        $totalBiaya = (float) $transaksi->sum('total_biaya');

        $rataRataLiter = $totalTransaksi > 0
            ? $totalLiter / $totalTransaksi
            : 0;

        $perKendaraan = $transaksi
            ->filter(function ($item) {
                return $item->kendaraan_operasional_id !== null;
            })
            ->groupBy(function ($item) {
                return $item->kendaraan_operasional_id;
            })
            ->map(function ($items) use ($awalMinggu) {
                $itemPertama = $items->first();

                $kendaraanOperasional =
                    $itemPertama->kendaraanOperasional;

                $namaKendaraan =
                    $kendaraanOperasional?->kode_unit ?? '-';

                $jenisKendaraan =
                    $kendaraanOperasional?->jenis_kendaraan ?? '-';

                $totalLiter =
                    (float) $items->sum('jumlah_liter');

                $totalBiaya =
                    (float) $items->sum('total_biaya');

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
                        $kmSekarang - $kmSebelumnya;

                    if ($totalJarak >= 0) {
                        $literUntukRasio =
                            (float) $transaksiDenganKm
                                ->sum('jumlah_liter');
                    } else {
                        $totalJarak = null;
                    }
                } elseif ($transaksiDenganKm->count() === 1) {
                    $kmSekarang =
                        (float) $transaksiDenganKm
                            ->first()
                            ->kilometer;

                    $transaksiSebelumnya =
                        TransaksiPengisianBbm::query()
                            ->where(
                                'kendaraan_operasional_id',
                                $kendaraanOperasional->id
                            )
                            ->where(
                                'tanggal_pengisian',
                                '<',
                                $awalMinggu
                            )
                            ->whereNotNull('kilometer')
                            ->orderByDesc('tanggal_pengisian')
                            ->orderByDesc('id')
                            ->first();

                    if ($transaksiSebelumnya) {
                        $kmSebelumnya =
                            (float) $transaksiSebelumnya->kilometer;

                        $totalJarak =
                            $kmSekarang - $kmSebelumnya;

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

                $rasioAktual = null;

                if (
                    $totalJarak !== null &&
                    $totalJarak > 0 &&
                    $literUntukRasio > 0
                ) {
                    $rasioAktual =
                        $totalJarak / $literUntukRasio;
                }

                $standarKonsumsi = null;

                if ($jenisKendaraan !== '-') {
                    $standarKonsumsi =
                        StandarKonsumsiBbm::query()
                            ->where(
                                'jenis_kendaraan',
                                $jenisKendaraan
                            )
                            ->where('status', true)
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

                $rasioStandar = $rasioStandarMin;

                $evaluasi = null;

                if (
                    $rasioAktual !== null &&
                    $rasioStandarMin !== null
                ) {
                    if ($rasioAktual >= $rasioStandarMin) {
                        $evaluasi = 'Wajar';
                    } else {
                        $evaluasi = 'Tidak Wajar';
                    }
                } elseif ($rasioStandarMin !== null) {
                    $evaluasi = 'Belum Dievaluasi';
                }

                $varianPemborosan = 0;

                if (
                    $rasioAktual !== null &&
                    $rasioStandarMin !== null &&
                    $rasioAktual < $rasioStandarMin &&
                    $totalJarak !== null &&
                    $rasioStandarMin > 0
                ) {
                    $literSeharusnya =
                        $totalJarak / $rasioStandarMin;

                    $varianPemborosan = max(
                        0,
                        $literUntukRasio -
                        $literSeharusnya
                    );
                }

                $biayaKerugian = 0;

                if (
                    $varianPemborosan > 0 &&
                    $literUntukRasio > 0 &&
                    $totalLiter > 0
                ) {
                    $hargaPerLiter =
                        $totalBiaya / $totalLiter;

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

        $totalLiterSemuaKendaraan =
            $perKendaraan->sum('total_liter');

        $perKendaraan = $perKendaraan->map(
            function ($item) use (
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
            }
        );

        $perJenisKendaraan = $transaksi
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

        $perBbm = $transaksi
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

        $perDepartemen = $transaksi
            ->groupBy(function ($item) {
                $departemen = trim(
                    (string) $item->departemen_snapshot
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

        $perCostCenter = $transaksi
            ->groupBy(function ($item) {
                $costCenter = trim(
                    (string) $item->cost_center_snapshot
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

        $aiSummary = null;
        $aiError = null;

        $apiKey = env('GEMINI_API_KEY');

        if (
            $apiKey &&
            $totalTransaksi > 0
        ) {
            $kendaraanSummary = $perKendaraan
                ->map(function ($item) {
                    return [
                        'kendaraan' =>
                            $item['kendaraan'],
                        'jenis_kendaraan' =>
                            $item['jenis_kendaraan'],
                        'transaksi' =>
                            $item['total_transaksi'],
                        'liter' =>
                            (float) $item['total_liter'],
                        'biaya' =>
                            (float) $item['total_biaya'],
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

            $bbmSummary = $perBbm
                ->map(function ($item, $jenis) {
                    return [
                        'jenis_bbm' =>
                            $jenis,
                        'transaksi' =>
                            $item['total_transaksi'],
                        'liter' =>
                            (float) $item['total_liter'],
                        'biaya' =>
                            (float) $item['total_biaya'],
                    ];
                })
                ->values()
                ->toArray();

            $departemenSummary = $perDepartemen
                ->map(function ($item, $departemen) {
                    return [
                        'departemen' =>
                            $departemen,
                        'transaksi' =>
                            $item['total_transaksi'],
                        'liter' =>
                            (float) $item['total_liter'],
                        'biaya' =>
                            (float) $item['total_biaya'],
                    ];
                })
                ->values()
                ->toArray();

            $costCenterSummary = $perCostCenter
                ->map(function ($item, $costCenter) {
                    return [
                        'cost_center' =>
                            $costCenter,
                        'transaksi' =>
                            $item['total_transaksi'],
                        'liter' =>
                            (float) $item['total_liter'],
                        'biaya' =>
                            (float) $item['total_biaya'],
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

            $prompt =
                'Anda adalah asisten analisis penggunaan BBM untuk sistem FuelVision.

Buat ringkasan analisis penggunaan BBM berdasarkan data yang diberikan.

Periode: ' .
                $awalMinggu->format('d/m/Y') .
                ' - ' .
                $akhirMinggu->format('d/m/Y') . '

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
1. Ringkasan umum penggunaan BBM.
2. Kendaraan dengan penggunaan BBM terbesar.
3. Kendaraan dengan rasio konsumsi yang tidak wajar jika ada.
4. Jenis BBM yang paling banyak digunakan.
5. Kesimpulan dan hal yang perlu diperhatikan.

Jawaban maksimal 5 paragraf.';

            try {
                $response = Http::timeout(30)
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
        } elseif (! $apiKey) {
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
            } else {
                $aiError =
                    'Tidak ada transaksi pada periode dan filter yang dipilih.';
            }
        } elseif ($totalTransaksi <= 0) {
            $aiError =
                'Tidak ada transaksi pada periode dan filter yang dipilih.';
        }

        return [
            'awalMinggu' =>
                $awalMinggu,
            'akhirMinggu' =>
                $akhirMinggu,
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
        $insights = [];

        $insights[] =
            '1. Ringkasan umum: Pada periode yang dipilih terdapat ' .
            $totalTransaksi .
            ' transaksi pengisian BBM dengan total penggunaan ' .
            number_format(
                $totalLiter,
                2,
                ',',
                '.'
            ) .
            ' L dan total biaya Rp ' .
            number_format(
                $totalBiaya,
                0,
                ',',
                '.'
            ) .
            '. Rata-rata penggunaan per transaksi sebesar ' .
            number_format(
                $rataRataLiter,
                2,
                ',',
                '.'
            ) .
            ' L.';

        $kendaraanTerbesar = $perKendaraan
            ->sortByDesc('total_liter')
            ->first();

        if ($kendaraanTerbesar) {
            $insights[] =
                '2. Penggunaan terbesar: Kendaraan dengan penggunaan BBM terbesar adalah ' .
                $kendaraanTerbesar['kendaraan'] .
                ' dengan penggunaan ' .
                number_format(
                    (float) $kendaraanTerbesar['total_liter'],
                    2,
                    ',',
                    '.'
                ) .
                ' L.';
        } else {
            $insights[] =
                '2. Penggunaan terbesar: Belum terdapat data penggunaan berdasarkan kendaraan.';
        }

        $kendaraanTidakWajar = $perKendaraan
            ->filter(function ($item) {
                return $item['evaluasi'] === 'Tidak Wajar';
            })
            ->sortByDesc('varian_pemborosan')
            ->first();

        if ($kendaraanTidakWajar) {
            $rasioAktual = $kendaraanTidakWajar['rasio_aktual'] !== null
                ? number_format(
                    (float) $kendaraanTidakWajar['rasio_aktual'],
                    2,
                    ',',
                    '.'
                )
                : '-';

            $rasioStandar = $kendaraanTidakWajar['rasio_standar'] !== null
                ? number_format(
                    (float) $kendaraanTidakWajar['rasio_standar'],
                    2,
                    ',',
                    '.'
                )
                : '-';

            $pemborosan =
                number_format(
                    (float) $kendaraanTidakWajar['varian_pemborosan'],
                    2,
                    ',',
                    '.'
                );

            $kerugian =
                number_format(
                    (float) $kendaraanTidakWajar['biaya_kerugian'],
                    0,
                    ',',
                    '.'
                );

            $insights[] =
                '3. Kendaraan yang perlu diperhatikan: Terdapat kendaraan dengan evaluasi tidak wajar, yaitu ' .
                $kendaraanTidakWajar['kendaraan'] .
                ' dengan rasio aktual ' .
                $rasioAktual .
                ' km/L dibandingkan standar ' .
                $rasioStandar .
                ' km/L, pemborosan ' .
                $pemborosan .
                ' L dengan estimasi biaya kerugian Rp ' .
                $kerugian .
                '.';
        } else {
            $insights[] =
                '3. Kendaraan yang perlu diperhatikan: Tidak ditemukan kendaraan dengan evaluasi konsumsi tidak wajar pada periode yang dipilih.';
        }

        $bbmTerbesar = $perBbm
            ->sortByDesc('total_liter')
            ->first();

        if ($bbmTerbesar) {
            $jenisBbm = $perBbm
                ->filter(function ($item) use ($bbmTerbesar) {
                    return $item === $bbmTerbesar;
                });

            $jenisBbmNama = $perBbm
                ->filter(function ($item) use ($bbmTerbesar) {
                    return $item['total_liter'] === $bbmTerbesar['total_liter'];
                })
                ->keys()
                ->first();

            $insights[] =
                '4. Jenis BBM: Jenis BBM dengan penggunaan terbesar adalah ' .
                ($jenisBbmNama ?: '-') .
                '.';
        } else {
            $insights[] =
                '4. Jenis BBM: Belum terdapat data penggunaan berdasarkan jenis BBM.';
        }

        $departemenTerbesar = $perDepartemen
            ->sortByDesc('total_liter')
            ->first();

        $totalPemborosan = (float) $perKendaraan
            ->sum('varian_pemborosan');

        $totalKerugian = (float) $perKendaraan
            ->sum('biaya_kerugian');

        if ($departemenTerbesar) {
            $namaDepartemen = $perDepartemen
                ->filter(function ($item) use ($departemenTerbesar) {
                    return $item['total_liter'] === $departemenTerbesar['total_liter'];
                })
                ->keys()
                ->first();

            $insights[] =
                '5. Fokus Finance: Departemen dengan penggunaan BBM terbesar adalah ' .
                ($namaDepartemen ?: '-') .
                '. Total pemborosan yang teridentifikasi sebesar ' .
                number_format(
                    $totalPemborosan,
                    2,
                    ',',
                    '.'
                ) .
                ' L dengan estimasi biaya kerugian sebesar Rp ' .
                number_format(
                    $totalKerugian,
                    0,
                    ',',
                    '.'
                ) .
                '.';
        } else {
            $insights[] =
                '5. Fokus Finance: Belum terdapat data departemen yang dapat dianalisis.';
        }

        return implode("\n\n", $insights);
    }
}