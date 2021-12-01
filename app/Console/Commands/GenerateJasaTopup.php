<?php

namespace App\Console\Commands;

use App\Models\PengajuanTopup;
use Illuminate\Console\Command;

class GenerateJasaTopup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jasatopup:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate jasa topup';

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
        $listTopup = PengajuanTopup::all();
        $listTopup->each(function ($pengajuanTopup)
        {
            $pinjaman = $pengajuanTopup->pinjaman;
            $jenisPinjaman = $pinjaman->jenisPinjaman;
            $jasaPelunasanDipercepat = $jenisPinjaman->jasa_pelunasan_dipercepat;
            if ($jasaPelunasanDipercepat == 0)
            {
                echo $pinjaman->kode_pinjam."\n";
                echo $jenisPinjaman->kode_jenis_pinjam."\n";
                echo $jenisPinjaman->jasaPelunasanDipercepat."\n";
            }
        });
    }
}
