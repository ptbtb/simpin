<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tabungan extends Model
{
    use HasFactory;

    protected $table = "t_tabungan";
    protected $primaryKey = "kode_tabungan";
    protected $dates = ['tgl_mulai'];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota');
    }

    public function listPenarikan()
    {
        return $this->hasMany(Anggota::class, 'kode_tabungan');
    }
}
