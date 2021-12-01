<?php

namespace App\Console\Commands;

use App\Models\PengajuanTopup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
        try
        {
            $listTopup = PengajuanTopup::has('pengajuan')
                                        ->has('pinjaman')
                                        ->get();
            $listTopup->each(function ($pengajuanTopup)
            {
                $pinjaman = $pengajuanTopup->pinjaman;
                $jenisPinjaman = $pinjaman->jenisPinjaman;
                // echo $pinjaman->kode_pinjam."\n";
                // echo $jenisPinjaman->kode_jenis_pinjam."\n";
                // echo $jenisPinjaman->jasa_pelunasan_dipercepat."\n";
                if($jenisPinjaman->jasa_pelunasan_dipercepat == 0)
                {
                    $pengajuanTopup->jasa_pelunasan_dipercepat = 0;
                    $pengajuanTopup->save();
                }
                else
                {
                    echo "jasa pelunasan dipercepat untuk id ". $pengajuanTopup->id."\n";
                }
            });
        }
        catch (\Throwable $th)
        {
            Log::error($th);
        }
    }
}
