<?php

namespace App\Console\Commands;
use App\Managers\PinjamanManager;
use App\Models\Pinjaman;
use App\Models\PinjamanV2;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;

class UpdatePinjamanV2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:pinjamanv2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Pinjaman Saldo Awal';

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
    {DB::beginTransaction();
        try
        {
            $pinjaman = PinjamanV2::wherenull('sync')->get();

            foreach($pinjaman as $pinj){

                $cc=$pinj->getPinjamanLama();
                if($cc){
                    $tgl =   Carbon::now()->format('Y-m-d');
                    $cc->tgl_transaksi = $pinj->tgl_posting;
                    $cc->sisa_pinjaman = $pinj->saldo_akhir-$cc->getJumlahAngsuran($tgl);
                    $cc->sisa_angsuran = $pinj->sisa_angsuran;
                    $cc->id_status_pinjaman = 1;
                    $cc->tgl_pelunasan = null;
                    $cc->lama_angsuran = $pinj->lama_angsuran;
                    $cc->mutasi_juli = $pinj->saldo_akhir;
                    $cc->update();
                    $pinj->sync=1;
                    $pinj->update();
                }else{
                    PinjamanManager::createPinjamanMutasiJuli($pinj);
//                    $pinj->sync=1;
//                    $pinj->update();
                }


//                var_dump($cc);
            }

            DB::commit();

        }
        catch(\Exception $e)
        {
            DB::rollback();
            \Log::info($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}
