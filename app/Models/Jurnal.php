<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;
    use \Wildside\Userstamps;
    protected $table = 't_jurnal';
    protected $primaryKey = 'id';
    protected $fillable = ['id',
        // 'kode_tabungan',
        'jenis',
        'nomer',
        'akun_kredit',
        'kredit',
        'akun_debet',
        'debet',
        
        ];
}
