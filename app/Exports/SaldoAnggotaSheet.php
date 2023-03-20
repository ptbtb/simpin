<?php
namespace App\Exports;

use App\Models\BukuBesarJurnal;
use App\Models\JenisSimpanan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Tabungan;
use Carbon\Carbon;
use Maatwebsite\Excel\Events\AfterSheet;

class SaldoAnggotaSheet implements FromQuery, WithTitle,WithHeadings, ShouldAutoSize, WithEvents, WithMapping
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
        $jenisSimpanan = JenisSimpanan::orderBy('sequence', 'asc')->pluck('kode_jenis_simpan');
//        $anggota = Anggota::limit(500)->get()->pluck('kode_anggota');
        return BukuBesarJurnal::selectRaw('sum(amount) as saldo,kode,anggota,nama_anggota,nama_transaksi')
            ->wherenotNull('anggota')
            ->wherein('kode',$jenisSimpanan)
            ->where('tgl_transaksi','<=',$this->request->tahun)
            ->groupBy('anggota')
            ->groupBy('nama_anggota')
            ->groupBy('kode')
            ->groupBy('nama_transaksi')
//            ->groupBy('kode')
            ->orderBy('anggota', 'asc');
    }

    /**
     * @var Invoice $invoice
     */
    public function map($pinjaman): array
    {
//        dd($pinjaman);

        return [
            $pinjaman->nama_anggota,
            $pinjaman->anggota,
            $pinjaman->kode,
            $pinjaman->nama_transaksi,
            $pinjaman->saldo*-1,
//            $pinjaman->nama,
//            $pinjaman->kode_jenis_pinjam,
//            $pinjaman->nama_pinjaman,
//            $pinjaman->besar_pinjam,
//            $pinjaman->lama_angsuran,
//            $pinjaman->getSisaPinjaman($this->request->period),
//            $pinjaman->sisa_angsuran
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Saldo Anggota Per '.Carbon::createFromFormat('Y-m-d',$this->request->tahun)->format('d m Y');
    }

    public function headings(): array
    {
        return [
            "Nama anggota",
            "kode anggota",
            "kode ",
            "Nama Transaksi",
            "saldo",
//                "Unit Kerja",
//                "Kode Jenis Pinjaman",
//                "Nama Pinjaman",
//                "Jumlah Pinjaman",
//                "Tenor",
//                "Sisa Pinjaman",
//                "Sisa Angsuran"
        ];
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
