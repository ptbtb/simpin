<?php

namespace App\Console\Commands;

use App\Models\Pengajuan;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GeneratePinjaman extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simpanpinjam:udpate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update simpan pinjam';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /**
         * update tanggal pinjaman 1 oktober keatas
         */

        $startDate = Carbon::createFromFormat('Y-m-d', '2021-10-01');
        $listPengajuan = Pengajuan::has('pinjaman')
                                ->whereDate('tgl_transaksi', '>=', $startDate)
                                ->get();

        echo 'start looping pengajuan \n';
        $listPengajuan->each(function ($pengajuan)
        {
            // update pinjaman
            $pinjaman = $pengajuan->pinjaman;
            $pinjaman->tgl_entri = $pengajuan->tgl_transaksi;
            $pinjaman->tgl_tempo = $pengajuan->tgl_transaksi->addMonths($pengajuan->tenor);
            $pinjaman->save();
            echo 'pinjaman updated '. $pinjaman->kode_pinjam."\n";

            // udpate simpanan
            $simpanan = Simpanan::where('keterangan', 'like', "Simpanan pagu dari pengajuan pinjaman ". $pengajuan->kode_pengajuan)
                                ->first();

            if($simpanan)
            {
                echo "update pinjaman \n";
                $simpanan->tgl_entri = $pengajuan->tgl_transaksi;
                $simpanan->tgl_transaksi = $pengajuan->tgl_transaksi;
                $simpanan->periode = $pengajuan->tgl_transaksi;
                $simpanan->save();
            }
        });
    }
}
