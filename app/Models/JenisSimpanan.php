<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSimpanan extends Model
{
    use HasFactory;
    protected $table = "t_jenis_simpan";
    protected $primaryKey = "kode_jenis_simpan";
    protected $keyType = 'string';
    protected $dates = ['tgl_entri'];
}
