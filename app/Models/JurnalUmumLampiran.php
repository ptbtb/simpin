<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JurnalUmumLampiran extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "t_jurnal_umum_lampiran";
    protected $fillable = ['lampiran'];

    public function jurnalUmum()
    {
        return $this->belongsTo(JurnalUmum::class);
    }
}
