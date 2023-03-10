<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class SaldoAwal extends Model
{
    use HasFactory;
    use Userstamps;

    protected $table = "t_saldo_awal";
    protected $fillable = ['code_id', 'nominal', 'batch'];
    protected $appends = ['nominal_rupiah'];
    protected $dates = ['created_at, batch'];

    public function code()
    {
        return $this->belongsTo(Code::class, 'code_id');
    }

    /**
     * Get all of the saldo awal's jurnals.
     */
    public function jurnals()
    {
        return $this->morphMany(Jurnal::class, 'jurnalable');
    }

    public function getNominalRupiahAttribute()
    {
        if ($this->nominal)
        {
            return number_format($this->nominal,2,",",".");
        }
        return $this->nominal;
    }
}
