<?php

namespace App\Console\Commands;

use App\Managers\JurnalManager;
use App\Models\Jurnal;
use App\Models\Penarikan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateJurnalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:jurnalpenarikan
                            {--periode=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Jurnal Penarikan';

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
        $log = "Updating Jurnal Penarikan...";
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
        $tarik = Penarikan::whereBetween('tgl_transaksi', [$start, $end])
                ->whereDoesntHave('jurnals');
        if ($tarik->count() > 0){
            $tarik = $tarik->orderBy('kode_ambil', 'asc')->get();
            foreach ($tarik as $value) {
                JurnalManager::createJurnalPenarikan($value);
                $this->info("Jurnal Penarikan with kode_ambil $value->kode_ambil is created!");
                $log = $log . "\nJurnal Penarikan with kode_ambil $value->kode_ambil is created!";
            }
        } else {
            $this->info("There's no penarikan without journals in periode $periode");
            $log = $log . "\nThere's no penarikan without journals in periode $periode";
            return 1;
        }
        $this->info("Update Jurnal Penarikan is Done");
        $log = $log . "\nUpdate Jurnal Penarikan is Done";
        Log::info($log);
    }
}
