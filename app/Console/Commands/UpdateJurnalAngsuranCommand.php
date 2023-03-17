<?php

namespace App\Console\Commands;

use App\Managers\JurnalManager;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateJurnalAngsuranCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:jurnalangsuran
                            {--periode=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Jurnal Angsuran';

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
        $log = "Updating Jurnal Angsuran...";
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
        $angsuran = Angsuran::whereBetween('tgl_transaksi', [$start, $end])
                    ->whereDoesntHave('jurnals');
        
        if ($angsuran->count() > 0){
            $angsuran = $angsuran->orderBy('kode_angsur', 'asc')->get();
            foreach ($angsuran as $value) {
                JurnalManager::createJurnalAngsuran($value);
                $this->info("Jurnal Angsuran with kode_angsur $value->kode_angsur is created!");
                $log = $log . "\nJurnal Angsuran with kode_angsur $value->kode_angsur is created!";
            }
        } else {
            $this->info("There's no angsuran without journals in periode $periode");
            $log = $log . "\nThere's no angsuran without journals in periode $periode";
            return 1;
        }
        $this->info("Update Jurnal Angsuran is Done");
        $log = $log . "\nUpdate Jurnal Angsuran is Done";
        Log::info($log);
        return 0;
    }
}
