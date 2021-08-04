<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Models\Pinjaman;

class PinjamanExport implements FromView
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $listPinjaman = Pinjaman::with('anggota');
        if ($this->request->anggota)
        {
            $listPinjaman = $listPinjaman->where('kode_anggota', $this->request->anggota->kode_anggota);
        }
        if (!$request->from) {
            $request->from = Carbon::today()->firstOfMonth()->format('Y-m-d');
          
        }
        if (!$request->to) {
            $request->to = Carbon::today()->format('Y-m-d');
        }
        if ($this->request->status)
        {
            $listPinjaman = $listPinjaman->where('id_status_pinjaman', $this->request->status);
        }
        if($request->jenistrans){
            if($request->jenistrans=='A'){
                $listPinjaman = Pinjaman::where('saldo_mutasi','>',0);
            }
            if($request->jenistrans=='T'){
                $listPinjaman = Pinjaman::where('saldo_mutasi',0);
            }

        }
        $listPinjaman = $listPinjaman->whereBetween('tgl_entri', [$request->from,$request->to]);
        $listPinjaman = $listPinjaman->get();
        return view('pinjaman.excel', [
            'listPinjaman' => $listPinjaman
        ]);
    }
}
