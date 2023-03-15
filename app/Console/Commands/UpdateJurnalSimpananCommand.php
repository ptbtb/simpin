<?php

namespace App\Console\Commands;

use App\Managers\JurnalManager;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateJurnalSimpananCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:jurnalsimpanan
                            {--periode=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Jurnal Simpanan';

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
        $log = "Updating Jurnal Simpanan...";
        $periode = $this->option('periode');
        if (empty($periode)){
            $this->error("Please add an argument '--periode='. It Must Be Input In Format YYYY-MM");
            return 0;
        }
        $start = null; $end = null;
        try {
            $start = Carbon::createFromFormat('Y-m', $periode)->startOfMonth();
            $end = Carbon::createFromFormat('Y-m', $periode)->endOfMonth();
        } catch (\Throwable $th) {
            $this->error("Periode Must Be Input In Format YYYY-MM");
            return 0;
        }
        $simpanan = Simpanan::whereBetween('tgl_transaksi', [$start, $end])
                    ->whereDoesntHave('jurnals')->where('besar_simpanan','<>',0);
        $simpanan = $simpanan->orderBy('kode_simpan', 'asc')->get();
//        dd($simpanan->count());
        if ($simpanan->count() > 0){

            foreach ($simpanan as $value) {
                if ($value->mutasi==1){
                    JurnalManager::createJurnalSaldoSimpanan($value);
                }else{
                    JurnalManager::createJurnalSimpanan($value);
                }
                $this->info("Jurnal Simpanan with kode_simpan $value->kode_simpan is created!");
                $log = $log . "\nJurnal Simpanan with kode_simpan $value->kode_simpan is created!";
            }
        } else {
            $this->info("There's no simpanan without journals in periode $periode");
            $log = $log . "\nThere's no simpanan without journals in periode $periode";
            return 1;
        }
        $this->info("Update Jurnal Simpanan is Done");
        $log = $log . "\nUpdate Jurnal Simpanan is Done";
        Log::info($log);
        return 0;
    }
}
