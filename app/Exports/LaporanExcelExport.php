<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanExcelExport implements WithMultipleSheets
{
     use Exportable;
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function sheets(): array
    {

       return [
            'Resume Tahun' => new LaporanExcelResumeAllExport($this->data),
            'Saldo Anggota' => new SaldoAnggotaSheet($this->data['request']),
            'trx Tanpa Anggota' => new WithoutAnggotaSheet($this->data['request']),
        ];
    }
}

