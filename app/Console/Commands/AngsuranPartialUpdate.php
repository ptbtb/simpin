<?php

namespace App\Console\Commands;

Use App\Models\Angsuran;
use App\Managers\AngsuranPartialManager;
use Illuminate\Console\Command;
use DB;

class AngsuranPartialUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:angsuranpartial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Partial Angsuran';

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
        $listAngsuran = Angsuran::has('jurnals')
                        ->where('id_status_angsuran',1)
                        ->where('besar_pembayaran','>',0)
                        ->get();

        foreach($listAngsuran as $angs){
            
            AngsuranPartialManager::generatetanpaposting($angs);
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
