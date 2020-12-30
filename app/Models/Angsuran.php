<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Angsuran extends Model
{
    use HasFactory;

    protected $table = "t_angsur";
    protected $primaryKey = "kode_angsur";
    protected $dates = ['tgl_entri','paid_at'];
}
