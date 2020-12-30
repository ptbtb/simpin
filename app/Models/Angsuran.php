<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Angsuran extends Model
{
    use HasFactory;

    protected $table = "t_angsur";
    protected $primaryKey = "kode_angsur";
    protected $dates = ['tgl_entri','paid_at','jatuh_tempo'];

    public function statusAngsuran()
    {
        return $this->belongsTo(StatusAngsuran::class, 'id_status_angsuran');
    }

    public function getTotalAngsuranAttribute()
    {
        return $this->besar_angsuran + $this->jasa;
    }

    public function getSisaPinjamanAttribute()
    {
        return $this->sisa_pinjam + $this->totalAngsuran - $this->besar_pembayaran;
    }
}
