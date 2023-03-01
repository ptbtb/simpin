<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Models\Pinjaman;
use Carbon\Carbon;

class PinjamanReportExport implements FromView
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        // data collection
        $reports = collect();

        $today = Carbon::today();

        // period
        // check if period date has been selected
        if(!$this->request->period)
        {
            $this->request->period = Carbon::today()->format('Y-m-d');
        }

        // get start and end of year
        $startOfYear = Carbon::createFromFormat('Y-m-d', $this->request->period)->startOfYear()->toDateTimeString();
        $endOfYear   = Carbon::createFromFormat('Y-m-d', $this->request->period)->endOfYear()->toDateTimeString();

        $pinjamanJapens = Pinjaman::whereBetween('tgl_transaksi', [$startOfYear, $endOfYear])
                                ->orderBy('tgl_transaksi')
                                ->japen()
                                ->get()
                                ->groupBy(function($query) {
                                    return Carbon::parse($query->tgl_entri)->format('m');
                                });

        $pinjamanJapans = Pinjaman::whereBetween('tgl_transaksi', [$startOfYear, $endOfYear])
                                ->orderBy('tgl_transaksi')
                                ->japan()
                                ->get()
                                ->groupBy(function($query) {
                                    return Carbon::parse($query->tgl_entri)->format('m');
                                });

        $totalJapenDiterima = 0;
        $totalJapenApproved = 0;
        $totalJapanDiterima = 0;
        $totalJapanApproved = 0;
        $totalJapanTrx = 0;
        $totalJapenTrx = 0;

        // loop for every month in year
        for ($i=1; $i <=12 ; $i++)
        {
            $japenDiterima = 0;
            $japenApproved = 0;
            $japanDiterima = 0;
            $japanApproved = 0;
            $japenTemp = [];
            $japanTemp = [];

            if($i < 10)
            {
                if(property_exists((object)$pinjamanJapens->toArray(), '0' . $i))
                {

                    $japenTemp = $pinjamanJapens['0' . $i];
                }

                if(property_exists((object)$pinjamanJapans->toArray(), '0' . $i))
                {
                    $japanTemp = $pinjamanJapans['0' . $i];
                }
            }
            else
            {
                if(property_exists((object)$pinjamanJapens->toArray(), $i))
                {
                    $japenTemp = $pinjamanJapens[$i];
                }

                if(property_exists((object)$pinjamanJapans->toArray(), $i))
                {
                    $japanTemp = $pinjamanJapans[$i];
                }
            }

            $trxJapen = count($japenTemp);
            $trxJapan = count($japanTemp);

            foreach($japenTemp as $japen)
            {
                if($japen->pengajuan)
                {
                    if($japen->pengajuan->bukti_pembayaran == null)
                    {
                        $japenApproved += (int)$japen->besar_pinjam;
                    }
                    else
                    {
                        $japenDiterima += (int)$japen->besar_pinjam;
                    }
                }
                else
                {
                    $japenDiterima += (int)$japen->besar_pinjam;
                }
            }

            foreach($japanTemp as $japan)
            {
                if($japan->pengajuan)
                {
                    if($japan->pengajuan->bukti_pembayaran == null)
                    {
                        $japanApproved += (int)$japan->besar_pinjam;
                    }
                    else
                    {
                        $japanDiterima += (int)$japan->besar_pinjam;
                    }
                }
                else
                {
                    $japanDiterima += (int)$japan->besar_pinjam;
                }
            }

            $reports->put($i, ['trxJapen' => $trxJapen,
                                    'trxJapan' => $trxJapan,
                                    'japenDiterima' => $japenDiterima,
                                    'japenApproved' => $japenApproved,
                                    'japanDiterima' => $japanDiterima,
                                    'japanApproved' => $japanApproved
                                ]);

            // total data
            $totalJapanTrx += $trxJapan;
            $totalJapenTrx += $trxJapen;
            $totalJapenApproved += $japenApproved;
            $totalJapanApproved += $japanApproved;
            $totalJapanDiterima += $japanDiterima;
            $totalJapenDiterima += $japenDiterima;
        }

        return view('pinjaman.reportExcel', [
            'reports' => $reports,
            'totalJapanTrx' => $totalJapanTrx,
            'totalJapenTrx' => $totalJapenTrx,
            'totalJapenApproved' => $totalJapenApproved,
            'totalJapanApproved' => $totalJapanApproved,
            'totalJapanDiterima' => $totalJapanDiterima,
            'totalJapenDiterima' => $totalJapenDiterima,
            'request' => $this->request,
        ]);
    }
}
