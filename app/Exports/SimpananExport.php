<?php

namespace App\Exports;

use App\Models\Simpanan;
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
        if ($this->request->from)
        {
            $listSimpanan = $listSimpanan->where('tgl_entri','>=', $this->request->from);
        }
        if ($this->request->to)
        {
            $listSimpanan = $listSimpanan->where('tgl_entri','<=', $this->request->to);
        }
        // $listSimpanan = $listSimpanan->get();
        $listSimpanan = $listSimpanan->orderBy('tgl_entri','desc')->take(200)->get();
        return view('simpanan.excel', [
            'listSimpanan' => $listSimpanan
        ]);
    }
}
