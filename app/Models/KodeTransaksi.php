<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeTransaksi extends Model
{
    use HasFactory;

    protected $table = "t_code";
    protected $primaryKey = "CODE";
    protected $keyType = 'string';
}
