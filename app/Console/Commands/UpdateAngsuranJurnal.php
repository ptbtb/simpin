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
        $listAngsuran = DB::table('t_jurnal_backup_060121')
                        ->where('jurnalable_type','App\\Models\\Angsuran')
                        ->wherenull('deleted_at')
                        ->where('akun_debet','like','106%')
                        ->get();

        foreach($listAngsuran as $angs){

        DB::beginTransaction();
        try
        {
             echo $angs->id."\n";
            $jurnal = Jurnal::find($angs->id);
            $jurnal->akun_debet = $angs->akun_kredit;
            $jurnal->debet = $angs->kredit;
            $jurnal->akun_kredit = $angs->akun_debet;
            $jurnal->kredit = $angs->debet;
            $jurnal->save();
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
