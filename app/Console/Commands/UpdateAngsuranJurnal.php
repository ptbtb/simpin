<?php

namespace App\Console\Commands;

Use App\Models\Angsuran;
Use App\Models\Jurnal;
use App\Managers\AngsuranPartialManager;
use Illuminate\Console\Command;
use DB;

class UpdateAngsuranJurnal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:angsuranjurnal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Angsuran';

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
        $listAngsuran = DB::table('t_jurnal')
                        ->where('jurnalable_type','App\\Models\\Angsuran')
                        ->wherenull('deleted_at')
                        ->where('akun_debet','like','106%')
                        ->get();
//        dd($listAngsuran);
        foreach($listAngsuran as $angs){

        DB::beginTransaction();
        try
        {
            $rawjurnal = Jurnal::
                        where('jurnalable_type','App\\Models\\Angsuran')
                        ->where('jurnalable_id',$angs->jurnalable_id)
                        ->wherenull('deleted_at')
                        ->get();
//            dd($rawjurnal);
            foreach ($rawjurnal as $jurnal){
                echo $jurnal->jurnalable_id."\n";
                $jurnal->akun_debet = $jurnal->akun_kredit;
                $jurnal->debet = $jurnal->kredit;
                $jurnal->akun_kredit = $jurnal->akun_debet;
                $jurnal->kredit = $jurnal->debet;
                $jurnal->save();
                DB::commit();
            }

        }
        catch(\Exception $e)
        {
        DB::rollback();
        \Log::info($e->getMessage());
        throw new \Exception($e->getMessage());
        }


        }


    }
}
