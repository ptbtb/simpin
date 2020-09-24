<?php

namespace App\Exports;

use App\Models\Anggota;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;

class AnggotaExport implements FromView
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $anggotas = Anggota::with('jenisAnggota');
        if ($this->request->status)
        {
            $anggotas = $anggotas->where('status', $this->request->status);
        }
        if ($this->request->id_jenis_anggota)
        {
            $anggotas = $anggotas->where('id_jenis_anggota', $this->request->id_jenis_anggota);
        }

        $anggotas = $anggotas->get();
        return view('anggota.excel', [
            'anggotas' => $anggotas
        ]);
    }
}
