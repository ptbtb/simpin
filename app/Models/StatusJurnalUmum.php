<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusJurnalUmum extends Model
{
    use HasFactory;

    protected $table = 't_status_jurnal_umum';
    protected $fillable = [
        'id'
    ];

}
