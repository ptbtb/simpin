<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JurnalTemp extends Model
{
    use HasFactory;
    protected $table = "jurnal_temp";
    protected $fillable = ['code', 'kode_anggota', 'nama', 'nip', 'normal_balance', 'jumlah', 'tgl_posting', 'no_bukti', 'kd_bukti', 'uraian_2', 'uraian_3'];

    public function jurnals()
    {
        return $this->morphMany(Jurnal::class, 'jurnalable');
    }
    public function getSerialNumberViewAttribute()
    {
        return 'MTS'  .$this->tgl_posting->format('Y') . $this->tgl_posting->format('m') . str_pad($this->no_bukti, 4, "0", STR_PAD_LEFT);
    }

}
