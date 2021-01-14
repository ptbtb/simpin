<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AngsuranSimpanan extends Model
{
    use HasFactory;

    protected $table = "t_angsur_simpan";
    protected $primaryKey = "kode_angsur";
    protected $dates = ['tgl_entri'];
}