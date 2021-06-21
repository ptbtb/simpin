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
        $pinjaman = Pinjaman::where('saldo_mutasi','>',0)
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
