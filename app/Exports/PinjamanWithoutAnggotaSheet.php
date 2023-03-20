<?php
namespace App\Exports;

use App\Models\BukuBesarJurnal;
use App\Models\JenisSimpanan;
use App\Models\Jurnal;
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

class PinjamanWithoutAnggotaSheet implements FromQuery, WithTitle,WithHeadings, ShouldAutoSize, WithEvents, WithMapping
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
        return Jurnal::selectRaw('anggota,nomer,akun_kredit,kredit,akun_debet,debet,keterangan,tgl_transaksi')
            ->whereRaw('(akun_debet in (select kode_jenis_pinjam from t_jenis_pinjam) or akun_kredit in(select kode_jenis_pinjam from t_jenis_pinjam)) and (anggota is null or anggota not in (select kode_anggota from t_anggota))')
            ->where('tgl_transaksi','<=',$this->request->tahun)
            ;
    }

    /**
     * @var Invoice $invoice
     */
    public function map($pinjaman): array
    {
//        dd($pinjaman);

        return [
            $pinjaman->nomer,
            $pinjaman->anggota,
            $pinjaman->akun_kredit,
            $pinjaman->kredit,
            $pinjaman->akun_debet,
            $pinjaman->debet,
            $pinjaman->tgl_transaksi,
            $pinjaman->keterangan,
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
        return 'Trx Tanpa Anngota  Per '.Carbon::createFromFormat('Y-m-d',$this->request->tahun)->format('d m Y');
    }

    public function headings(): array
    {
        return [
            "Nomer",
            "kode anggota",
            "Akun Kredit ",
            "Kredit",
            "Akun debet",
            "Debet",
            "Tgl TRX",
            "keterangan",
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
