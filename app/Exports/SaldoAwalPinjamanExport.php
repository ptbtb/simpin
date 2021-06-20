<?php

namespace App\Exports;

use App\Models\Pinjaman;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SaldoAwalPinjamanExport implements FromView, ShouldAutoSize, WithEvents
{
    public function view(): View
    {
        $pinjaman = Pinjaman::
                            join('t_anggota', 't_pinjam.kode_anggota', 't_anggota.kode_anggota')
                            ->join('t_jenis_pinjam', 't_pinjam.kode_jenis_pinjam', 't_jenis_pinjam.kode_jenis_pinjam')
                            ->whereNotNull('saldo_mutasi')
                            ->where('saldo_mutasi','>',0)
                            ->select(
                                        't_pinjam.kode_pinjam',
                                        't_anggota.nama_anggota',
                                        't_pinjam.kode_anggota as nomor_anggota',
                                        't_pinjam.tgl_entri',
                                        't_jenis_pinjam.nama_pinjaman',
                                        't_pinjam.besar_pinjam',
                                        't_pinjam.saldo_mutasi'
                                    )
                            ->get();
        return view('pinjaman.exportSaldoAwalPinjaman', ['listPinjaman' => $pinjaman]);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $workSheet = $event->sheet->getDelegate();
                $workSheet->freezePane('A2'); // freezing here
            },
        ];
    }
}
