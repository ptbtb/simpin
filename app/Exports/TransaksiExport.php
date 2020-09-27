<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Models\View\ViewTransaksi;

class TransaksiExport implements FromView
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $kode_anggota = $this->request->anggota->kode_anggota;
        $listTransaksi = ViewTransaksi::where('kode_anggota', $kode_anggota);
        if ($this->request->from)
        {
            $listTransaksi = $listTransaksi->where('tgl_entri','>=', $this->request->from);
        }
        if ($this->request->to)
        {
            $listTransaksi = $listTransaksi->where('tgl_entri','<=', $this->request->to);
        }
        $listTransaksi = $listTransaksi->get();
        return view('transaksi.excel', [
            'listTransaksi' => $listTransaksi
        ]);
    }
}
