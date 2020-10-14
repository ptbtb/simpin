<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    use HasFactory;

    protected $table = "t_simpan";
    protected $primaryKey = "kode_simpan";
    protected $dates = ['tgl_mulai', 'tgl_entri'];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota');
    }
}
