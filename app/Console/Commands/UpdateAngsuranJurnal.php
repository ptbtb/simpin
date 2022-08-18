<?php

namespace App\Console\Commands;

Use App\Models\Angsuran;
use App\Managers\JurnalManager;
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
        $listangs = Angsuran::
                        whereDoesntHave('jurnals')
                        ->where('id_akun_kredit','>',0)
                        ->where('id_status_angsuran',2)
                        ->get();
//        dd($listangs);
        foreach($listangs as $angsuran){
            echo $angsuran->kode_angsur."\n";
        DB::beginTransaction();
        try
        {

            JurnalManager::createJurnalAngsuran($angsuran);
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
}
