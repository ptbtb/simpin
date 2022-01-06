<?php

namespace App\Console\Commands;

use App\Models\PengajuanTopup;
use Illuminate\Console\Command;

class UpdatePinjamanTopup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'topuppinjaman:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update topup pinjaman';

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
        $pengajuanTopup = PengajuanTopup::whereNull('total_bayar_pelunasan_dipercepat')
        ->orWherenull('jasa_pelunasan_dipercepat')
        ->orwhere('jasa_pelunasan_dipercepat',0)
                                        ->get();
        $pengajuanTopup->each(function ($topup)
        {
            if($topup->pinjaman){
              $pinjaman = $topup->pinjaman;
            $topup->jasa_pelunasan_dipercepat = $pinjaman->jasaTopup;
            $topup->total_bayar_pelunasan_dipercepat = $pinjaman->totalBayarTopup;
            $topup->save();

            echo "topup id ".$topup->id." updated\n";  
            }
            
        });
    }
}
