<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Pinjaman;
use Carbon\Carbon;

class SaldoPinjamanAnggotaSheet implements FromQuery, WithTitle,WithHeadings
{
   

    

    /**
     * @return Builder
     */
    public function query()
    {
        return Pinjaman::join('t_anggota', 't_anggota.kode_anggota', '=', 't_pinjam.kode_anggota')
        ->join('t_jenis_pinjam', 't_jenis_pinjam.kode_jenis_pinjam', '=', 't_pinjam.kode_jenis_pinjam')
            ->select('t_anggota.kode_anggota','t_anggota.nama_anggota','t_pinjam.kode_jenis_pinjam','t_jenis_pinjam.nama_pinjaman','t_pinjam.besar_pinjam','t_pinjam.lama_angsuran','t_pinjam.sisa_pinjaman','t_pinjam.sisa_angsuran' )
            ->wherenotin('t_pinjam.kode_anggota', [0])
            ->wherenotin('t_anggota.status', ['keluar'])
            ->orderBy('t_pinjam.kode_anggota','asc')
            ->orderBy('t_pinjam.kode_jenis_pinjam','asc');
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Saldo Anggota Per '.Carbon::now()->format('d m Y');
    }

     public function headings(): array
    {
        return ["kode anggota","Nama", "Kode Jenis Pinjaman","Nama Pinjaman", "Jumlah Pinjaman","Tenor","Sisa Pinjaman","Sisa Angsuran"];
    }
}
