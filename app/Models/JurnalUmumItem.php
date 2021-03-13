<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JurnalUmumItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "t_jurnal_umum_item";
    protected $fillable = ['code_id', 'nominal'];
    protected $appends = ['nominal_rupiah'];

    public function code()
    {
        return $this->belongsTo(Code::class, 'code_id');
    }

    public function jurnalUmum()
    {
        return $this->belongsTo(JurnalUmum::class);
    }

    public function getNominalRupiahAttribute()
    {
        if ($this->nominal)
        {
            return 'Rp.' . number_format($this->nominal,0,",",".");
        }
        return $this->nominal;
    }
}
