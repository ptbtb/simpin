<?php

namespace App\Exports;

use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;

class SimpananExport implements FromView
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function view(): View
    {
        $listSimpanan = Simpanan::with('anggota');
        if ($this->request->anggota)
        {
            $listSimpanan = $listSimpanan->where('kode_anggota', $this->request->anggota->kode_anggota);
        }
        if ($this->request->from || $this->request->to)
        {
            if ($this->request->from)
            {
                $listSimpanan = $listSimpanan->where('tgl_entri','>=', $this->request->from);
            }
            if ($this->request->to)
            {
                $listSimpanan = $listSimpanan->where('tgl_entri','<=', $this->request->to);
            }
        }
        else
        {
            $from = Carbon::now()->addDays(-30)->format('Y-m-d');
            $to = Carbon::now()->format('Y-m-d');
            $listSimpanan = $listSimpanan->where('tgl_entri','>=', $from)
                                        ->where('tgl_entri','<=', $to);
        }
        if ($this->request->jenis_simpanan)
        {
            $listSimpanan = $listSimpanan->where('kode_jenis_simpan',$this->request->jenis_simpanan);
        } 

        if ($this->request->kode_anggota)
        {
            $listSimpanan = $listSimpanan->where('kode_anggota', $this->request->kode_anggota);
        }
        // $listSimpanan = $listSimpanan->get();
        $listSimpanan = $listSimpanan->orderBy('tgl_entri','desc')->get();
        return view('simpanan.excel', [
            'listSimpanan' => $listSimpanan
        ]);
    }
}
