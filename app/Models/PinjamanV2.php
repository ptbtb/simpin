<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;
use OwenIt\Auditing\Contracts\Auditable;

class PinjamanV2 extends Model implements Auditable
{

    use HasFactory;
    use Userstamps;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = "t_pinjam_sa_3107";
    protected $primaryKey = "id";
    // protected $keyType = 'string';
    // public $incrementing = false;
    protected $dates = ['tgl_posting'];


    public function getPinjamanLama() {

        if ( $this->tgl_posting > '2021-01-01'){
           $tgl_banding=$this->tgl_posting;
        }else{
            $tgl_banding='2021-01-01';
        }

        $pinjaman_lama = Pinjaman::where('kode_anggota',$this->kode_anggota)
                            ->where('kode_jenis_pinjam',$this->kode_jenis_pinjam)
                            ->where('kode_jenis_pinjam',$this->kode_jenis_pinjam)
                            ->where('besar_pinjam',$this->besar_pinjam)
                            ->where('tgl_entri',$tgl_banding)
                            ->first();
        return $pinjaman_lama;
    }


}
