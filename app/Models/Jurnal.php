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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['view_created_at'];


    /**
     * Get the tipeJurnal that owns the Jurnal
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipeJurnal()
    {
        return $this->belongsTo(TipeJurnal::class, 'id_tipe_jurnal');
    }

    /**
     * Get the updatedBy that owns the Jurnal
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getViewCreatedAtAttribute()
    {
        return $this->created_at->format('d F Y');
    }
}
