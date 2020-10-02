<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penarikan extends Model
{
    use HasFactory;

    protected $table = "t_pengambilan";
    protected $primaryKey = "kode_ambil";
    protected $dates = ['tgl_ambil'];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota');
    }

    public function tabungan()
    {
        return $this->belongsTo(Tabungan::class, 'kode_tabungan');
    }
}
