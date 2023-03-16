<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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

    protected static function booted()
    {
        // you can do the same thing using anonymous function
        // let's add another scope using anonymous function
        static::addGlobalScope('real', function (Builder $builder) {
            $date = Carbon::parse(ActiveSaldoAwal::where('status', 1)->first()->tgl_saldo);
            return $builder->whereDate('batch', '>=', $date);
        });
    }
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
