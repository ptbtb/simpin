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
        $listPinjaman = Pinjaman::where('kode_anggota', $this->request->anggota->kode_anggota);
        if ($this->request->from)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','>=', $this->request->from);
        }
        if ($this->request->to)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','<=', $this->request->to);
        }
        if ($this->request->status)
        {
            $listPinjaman = $listPinjaman->where('status', $this->request->status);
        }
        $listPinjaman = $listPinjaman->get();
        return view('pinjaman.excel', [
            'listPinjaman' => $listPinjaman
        ]);
    }
}
