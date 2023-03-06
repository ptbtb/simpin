<?php

namespace App\Console\Commands;

use App\Managers\JurnalManager;
use App\Managers\JurnalUmumManager;
use App\Models\Jurnal;
use App\Models\JurnalUmum;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateJurnalJUCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:jurnalju
    {--periode=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Jurnal Jurnal Umum';

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
        $log = "Updating Jurnal Umum...";
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
        $jurnalUmum = JurnalUmum::where('import', 1)
                        ->whereBetween('tgl_transaksi', [$start, $end]);
        if ($jurnalUmum->count() > 0){
            $jurnalUmum = $jurnalUmum->orderBy('id', 'asc')->get();
            foreach ($jurnalUmum as $value) {
                $jurnal = Jurnal::where('jurnalable_id', $value->id)
                ->where('jurnalable_type', 'App\Models\JurnalUmum')->get();
                if ($jurnal->count() < 1){
                    // cek debet = kredit
                    $jurnalUmumItems = $value->jurnalUmumItems;
                    $debet = 0;
                    $kredit = 0;
                    foreach ($jurnalUmumItems as $key => $jurnalUmumItem) {
                        if ($jurnalUmumItem->normal_balance_id == NORMAL_BALANCE_DEBET) {
                            $debet += $jurnalUmumItem->nominal;
                        }
                        elseif ($jurnalUmumItem->normal_balance_id == NORMAL_BALANCE_KREDIT) {
                            $kredit += $jurnalUmumItem->nominal;
                        }
                    }
                    if ($debet != $kredit) {
                        $this->info("Debet != Kredit for Jurnal Umum with id $value->id");
                        $log = $log . "\nDebet != Kredit for Jurnal Umum with id $value->id";
                        continue;
                    }

                    // create serial_number
                    $value->serial_number = JurnalUmumManager::getSerialNumber($value->tgl_transaksi->format('Y-m-d'));
                    $value->save();

                    // create jurnal
                    JurnalManager::createJurnalUmum($value);
                    $this->info("Jurnal Umum with id $value->id is created!");
                    $log = $log . "\nJurnal Umum with id $value->id is created!";
                }
            }
            if ($log == "Updating Jurnal Umum..."){
                $log = $log . "\nThere's no jurnal umum without journal in periode $periode";
                Log::info($log);
                return 0;
            }
        } else {
            $this->info("There's no jurnal umum with import=1 in periode $periode");
            return 0;
        }
        $this->info("Update Jurnal Umum is Done");
        $log = $log . "\nUpdate Jurnal Umum is Done";
        Log::info($log);
    }
}
