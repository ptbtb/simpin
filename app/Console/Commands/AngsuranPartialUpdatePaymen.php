<?php

namespace App\Console\Commands;

Use App\Models\AngsuranPartial;
use App\Managers\AngsuranPartialManager;
use App\Managers\JurnalManager;
use Illuminate\Console\Command;
use DB;

class AngsuranPartialUpdatePaymen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:updatepaymentangspartial';

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
    {
        DB::beginTransaction();  
        try
        {
        $listAngsuran = AngsuranPartial::whereraw('besar_pembayaran <> besar_angsuran+jasa')
                        ->get();
        
        foreach($listAngsuran as $angs){
            // dd($angs);
            foreach ($angs->jurnals  as $jurnal){
                $jurnal->delete();
            }
            $angs->besar_pembayaran=$angs->jasa+$angs->besar_angsuran;
            $angs->save();
            
            
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
