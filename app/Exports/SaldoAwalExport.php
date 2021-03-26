<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Models\SaldoAwal;

class SaldoAwalExport implements FromView
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $listSaldoAwal = SaldoAwal::with('code');

        $listSaldoAwal = $listSaldoAwal->get();
        return view('saldo_awal.excel', [
            'listSaldoAwal' => $listSaldoAwal
        ]);
    }
}
