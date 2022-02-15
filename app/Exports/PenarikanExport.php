<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;

use App\Models\Penarikan;

class PenarikanExport implements FromView
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $listPenarikan = Penarikan::with('anggota')->where('paid_by_cashier',1);

        if ($this->request->kode_anggota)
        {
            $listPenarikan = $listPenarikan->where('kode_anggota', $this->request->kode_anggota);
        }

        if (!$request->from) {
            $request->from = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (!$request->to) {
          $request->to = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $listPenarikan->whereBetween('tgl_transaksi',[$request->from,$request->to]);

        $listPenarikan = $listPenarikan->orderBy('tgl_transaksi','desc')
                                        ->has('anggota')
                                        ->get();
        return view('penarikan.excel', [
            'listPenarikan' => $listPenarikan
        ]);
    }
}
