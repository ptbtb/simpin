<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Tabungan;
use Carbon\Carbon;

class SaldoAnggotaSheet implements FromQuery, WithTitle,WithHeadings
{
   

    

    /**
     * @return Builder
     */
    public function query()
    {
        return Tabungan::join('t_anggota', 't_anggota.kode_anggota', '=', 't_tabungan.kode_anggota')
        ->join('t_jenis_simpan', 't_jenis_simpan.kode_jenis_simpan', '=', 't_tabungan.kode_trans')
            ->select('t_anggota.kode_anggota','t_anggota.nama_anggota','kode_trans','t_jenis_simpan.nama_simpanan','besar_tabungan')
            ->wherenotin('t_tabungan.kode_anggota', [0])
            ->wherenotin('t_anggota.status', ['keluar'])
            ->orderBy('t_tabungan.kode_anggota','asc')
            ->orderBy('kode_trans','asc');
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
        return ["kode anggota","Nama", "Kode Jenis Simpanan","Nama Simpanan", "Jumlah"];
    }
}
