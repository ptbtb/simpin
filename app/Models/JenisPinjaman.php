<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPinjaman extends Model
{
    use HasFactory;

    protected $table = "t_jenis_pinjam";
    protected $primaryKey = "kode_jenis_pinjam";
    protected $keyType = 'string';
    protected $dates = ['tgl_entry'];
    
    public function listPinjaman()
    {
        return $this->hasMany(Pinjaman::class,'kode_jenis_pinjam');
    }
}
