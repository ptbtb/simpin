<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pinjaman;
use App\Models\JenisPinjaman;
use App\Models\SimpinRule;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\DB;

class UpdatePotonganPinjaman extends Command
{

	/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:pinjaman';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update potongan Pinjaman';

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
        $listPengajuan = Pengajuan::where('id_status_pengajuan','<',8)->get();
        foreach($listPengajuan as $pengajuan){
        	if ($pengajuan->pinjaman){
        		$pinjaman = $pengajuan->pinjaman;
        		$pinjaman->biaya_asuransi=$pinjaman->besar_pinjam * $pinjaman->jenisPinjaman->asuransi;
        		$pinjaman->biaya_provisi=$pinjaman->besar_pinjam * $pinjaman->jenisPinjaman->provisi;

        		$simpinRule = SimpinRule::find(SIMPIN_RULE_ADMINISTRASI);
        		$biayaAdministrasi = 0;
            if ($pengajuan->besar_pinjam >= $simpinRule->value)
            {
                $biayaAdministrasi = $simpinRule->amount;
            }
            	$pinjaman->biaya_administrasi = $biayaAdministrasi;
            	$pinjaman->save();
            	echo "Pinjaman ".$pinjaman->kode_pinjam." updated \r\n";
        	}
        }
    }

}