<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Jurnal extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
     use Userstamps;
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
        'tgl_transaksi',
        
        ];
        protected $dates = ['deleted_at'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['view_created_at', 'jurnalable_view','nominal_rupiah_debet','nominal_rupiah_kredit','ser_num_view','kode_anggota_view'];


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
     * Get the parent jurnalable model.
     */
    public function jurnalable()
    {
        return $this->morphTo();
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
    
    public function getJurnalableViewAttribute()
    {
        if($this->jurnalable)
        {
            
            return $this->jurnalable;
        }
        else
        {
            return '';
        }
    }
    public function getSerNumViewAttribute()
    {
        if($this->jurnalable)
        {
            if ($this->id_tipe_jurnal==TIPE_JURNAL_JKK && $this->jurnalable_type=='App\Models\Pinjaman' ){
                return $this->jurnalable->serial_number_kredit_view;
            }
            return $this->jurnalable->serial_number_view;
        }
        else
        {
            return '';
        }
    }
    public function getKodeAnggotaViewAttribute()
    {
        if($this->jurnalable)
        {
            return $this->jurnalable->kode_anggota;
        }
        else
        {
            return '';
        }
    }
    public function getNominalRupiahDebetAttribute()
    {
        if ($this->debet)
        {
            return number_format($this->debet,2,",",".");
        }
        return $this->debet;
    }
    public function getNominalRupiahKreditAttribute()
    {
        if ($this->kredit)
        {
            return number_format($this->kredit,2,",",".");
        }
        return $this->kredit;
    }
}
