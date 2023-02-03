<?php

namespace App\Console\Commands;

use App\Models\Code;
use App\Models\Jurnal;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ConsoleJurnalResolverCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'console:jurnal
                            {--periode=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Console Jurnal';

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
        $log = "Resolving disbalanced journal..";
        $periode = $this->option('periode');
        if (empty($periode)){
            $this->error("Please add an argument '--periode='. It Must Be Input In Format YYYY-MM");
            return 0;
        }
        $start = null; $end = null;
        try {
            $start = Carbon::createFromFormat('Y-m', $periode)->startOfMonth()->startOfDay();
            $end = Carbon::createFromFormat('Y-m', $periode)->endOfMonth()->endOfDay();
        } catch (\Throwable $th) {
            $this->error("Periode Must Be Input In Format YYYY-MM");
            return 0;
        }
        $jurnal = Jurnal::whereBetween('tgl_transaksi', [$start, $end])->get();
        $totalDebet = $jurnal->sum('debet');
        $totalKredit = $jurnal->sum('kredit');
        $this->info("Total Debet: $totalDebet \nTotal Kredit : $totalKredit");

        if ($totalDebet == $totalKredit){
            $this->info("Debet and Kredit are equal!");
            $log = $log . "\nDebet and Kredit are equal!";
        } else {
            $this->info("Debet and Kredit are not equal, please wait for resolving...");
            $jurnal = $jurnal->groupBy('trans_id');
            foreach ($jurnal as $key => $value) {
                $kredit = $value->sum('kredit');
                $debet = $value->sum('debet');
                if ($kredit == $debet){
                    continue;
                }
                $listCoaBank = Code::where('sumber_dana_id', 2)->pluck('CODE');
                $coaBank = $value->whereIn('akun_kredit', $listCoaBank)->first();
                $japan = $value->where('akun_kredit', COA_JASA_TOP_UP_PINJ_JANGKA_PANJANG)->first();
                $japen = $value->where('akun_kredit', COA_JASA_TOP_UP_PINJ_JANGKA_PENDEK)->first();
                // $this->info("japan = $japan \njapen = $japen \ncoaBank = $coaBank");
                $coaBankKreditBefore = $coaBank->kredit;
                if ($japan)
                {
                    $coaBank->kredit = $coaBank->kredit - $japan->kredit;
                } 
                else if ($japen)
                {
                    $coaBank->kredit = $coaBank->kredit - $japen->kredit;
                }

                // check balance or not
                $kredit = $value->sum('kredit');
                $debet = $value->sum('debet');
                if ($kredit == $debet){
                    // if balance, save the new Value
                    $coaBank->audit_track = 'Resolved By Jurnal Resolver';
                    $coaBank->save();
                    $this->info("$key is Resolved By Jurnal Resolver");
                    $log = $log . "\n$key is Resolved By Jurnal Resolver";
                } else {
                    // if not balance, return kredit value before
                    $coaBank->kredit = $coaBankKreditBefore;
                    $coaBank->audit_track = 'Cannot Resolve By Jurnal Resolver';
                    $coaBank->save();
                    $this->info("$key Cannot Resolve By Jurnal Resolver");
                    $log = $log . "\n$key Cannot Resolve By Jurnal Resolver";
                }
            }
        }
        Log::info($log);
    }
}
