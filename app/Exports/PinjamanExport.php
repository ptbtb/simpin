<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Models\Pinjaman;
use Carbon\Carbon;

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
        if (!$this->request->from) {
            $request->from = Carbon::today()->firstOfMonth()->format('Y-m-d');

        }
        if (!$this->request->to) {
            $request->to = Carbon::today()->format('Y-m-d');
        }
        if ($this->request->status)
        {
            $listPinjaman = $listPinjaman->where('id_status_pinjaman', $this->request->status);
        }
        if($this->request->jenistrans){
            if($this->request->jenistrans=='A'){
                $listPinjaman = Pinjaman::where('saldo_mutasi','>',0);
            }
            if($this->request->jenistrans=='T'){
                $listPinjaman = Pinjaman::where('saldo_mutasi',0);
            }

        }
        if ($this->request->unit_kerja)
        {
            $r = $this->request;
            $listPinjaman = $listPinjaman->whereHas('anggota', function ($query) use ($r)
                                        {
                                            return $query->where('company_id', $r->unit_kerja);
                                        });
        }
       if ($this->request->tenor)
        {
            $listPinjaman = $listPinjaman->where('lama_angsuran',$request->tenor);
        }
        $listPinjaman = $listPinjaman->whereBetween('tgl_entri', [$this->request->from,$this->request->to]);
        $listPinjaman = $listPinjaman->get();
        return view('pinjaman.excel', [
            'listPinjaman' => $listPinjaman
        ]);
    }
}
