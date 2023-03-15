<?php

namespace App\Console\Commands;

use App\Managers\JurnalManager;
use App\Models\Pinjaman;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateJurnalPinjamanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:jurnalpinjaman
                            {--periode=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Jurnal Pinjaman';

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
        $log = "Updating Jurnal Pinjaman...";
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
        $pinjaman = Pinjaman::whereBetween('tgl_transaksi', [$start, $end])
                    ->whereDoesntHave('jurnals');
        if ($pinjaman->count() > 0){
            $pinjaman = $pinjaman->orderBy('id', 'asc')->get();
            foreach ($pinjaman as $value) {
                JurnalManager::createJurnalPinjaman($value);
                $this->info("Jurnal Pinjaman with id $value->id is created!");
                $log = $log . "\nJurnal Pinjaman with id $value->id is created!";
            }
        } else {
            $this->info("There's no pinjaman without journals in periode $periode");
            $log = $log . "\nThere's no pinjaman without journals in periode $periode";
            return 1;
        }
        $this->info("Update Jurnal Pinjaman is Done");
        $log = $log . "\nUpdate Jurnal Pinjaman is Done";
        Log::info($log);
        return 0;
    }
}
