<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Models\Jurnal;
use App\Models\Code;

class BukuBesarExport implements FromView
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $codes = Code::where('is_parent', 0)->get();

        $jurnal = Jurnal::get();

        // buku besar collection
        $bukuBesars = collect();

        foreach ($codes as $key => $code) 
        {
            $saldo = 0;
            // get code's normal balance 
            if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
            {
                $saldoDebet = $jurnal->where('akun_debet', $code->CODE)->sum('debet');
                $saldoKredit = $jurnal->where('akun_kredit', $code->CODE)->sum('kredit');

                $saldo += $saldoDebet;
                $saldo -= $saldoKredit;

                $bukuBesars->push([
                    'code' => $code->CODE,
                    'name' => $code->NAMA_TRANSAKSI,
                    'type' => $code->codeType->name,
                    'saldo' => $saldo,
                ]);
            }
            else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
            {
                $saldoDebet = $jurnal->where('akun_debet', $code->CODE)->sum('debet');
                $saldoKredit = $jurnal->where('akun_kredit', $code->CODE)->sum('kredit');

                $saldo -= $saldoDebet;
                $saldo += $saldoKredit;

                $bukuBesars->push([
                    'code' => $code->CODE,
                    'name' => $code->NAMA_TRANSAKSI,
                    'type' => $code->codeType->name,
                    'saldo' => $saldo,
                ]);
            }
        }

        $bukuBesars = $bukuBesars->sortBy('code');

        return view('buku_besar.excel', [
            'bukuBesars' => $bukuBesars
        ]);
    }
}
