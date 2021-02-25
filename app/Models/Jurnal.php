<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;
    // use \Wildside\Userstamps;
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

    /**
     * Get the tipeJurnal that owns the Jurnal
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipeJurnal()
    {
        return $this->belongsTo(TipeJurnal::class, 'id_tipe_jurnal');
    }
}
