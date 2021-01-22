<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;


class Tabungan extends Model
{
    use HasFactory;
    use Userstamps;

    protected $table = "t_tabungan";
    protected $primaryKey = "id";
    protected $keyType = 'string';
    public $incrementing = false;
    protected $dates = ['tgl_mulai'];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota');
    }

    public function listPenarikan()
    {
        return $this->hasMany(Anggota::class, 'kode_tabungan');
    }

    public function jenisSimpanan()
    {
        return $this->belongsTo(JenisSimpanan::class, 'kode_trans' ,'kode_jenis_simpan');
    }
}
