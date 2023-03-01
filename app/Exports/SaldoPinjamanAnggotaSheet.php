<?php
namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Pinjaman;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class SaldoPinjamanAnggotaSheet implements FromQuery, WithTitle,WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    /**
     * @return Builder
     */
    public function query()
    {
        return Pinjaman::
                        join('t_anggota', 't_anggota.kode_anggota', '=', 't_pinjam.kode_anggota')
                        ->join('t_jenis_pinjam', 't_jenis_pinjam.kode_jenis_pinjam', '=', 't_pinjam.kode_jenis_pinjam')
                        ->join('t_company', 't_anggota.company_id', '=', 't_company.id')
                        ->wherenotin('t_pinjam.kode_anggota', [0])
//                        ->wherenotin('t_anggota.status', ['keluar'])
                        ->orderBy('t_pinjam.kode_anggota','asc')
                        ->orderBy('t_pinjam.kode_jenis_pinjam','asc')
                        ->select(
                                    't_anggota.kode_anggota',
                                    't_anggota.nama_anggota',
                                    't_company.nama',
                                    't_pinjam.kode_jenis_pinjam',
                                    't_jenis_pinjam.nama_pinjaman',
                                    't_pinjam.besar_pinjam',
                                    't_pinjam.lama_angsuran',
                                    't_pinjam.sisa_pinjaman',
                                    't_pinjam.sisa_angsuran' );
//                        ->limit(1);
    }

    /**
    * @var Invoice $invoice
    */
    public function map($pinjaman): array
    {
        return [
            $pinjaman->kode_anggota,
            $pinjaman->nama_anggota,
            $pinjaman->nama,
            $pinjaman->kode_jenis_pinjam,
            $pinjaman->nama_pinjaman,
            $pinjaman->besar_pinjam,
            $pinjaman->lama_angsuran,
            $pinjaman->getSisaPinjaman($this->request->period),
            $pinjaman->sisa_angsuran
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Saldo Anggota Per '.Carbon::createFromFormat('Y-m-d',$this->request->period)->format('d m Y');
    }

    public function headings(): array
    {
        return [
                "kode anggota",
                "Nama",
                "Unit Kerja",
                "Kode Jenis Pinjaman",
                "Nama Pinjaman",
                "Jumlah Pinjaman",
                "Tenor",
                "Sisa Pinjaman",
                "Sisa Angsuran"];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $workSheet = $event->sheet->getDelegate();
                $workSheet->setAutoFilter('A1:I1');
                $workSheet->freezePane('A2'); // freezing here
            },
        ];
    }
}
