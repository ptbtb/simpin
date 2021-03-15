<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoAwal extends Model
{
    use HasFactory;

    protected $table = "t_saldo_awal";
    protected $fillable = ['code_id', 'nominal'];
    protected $appends = ['nominal_rupiah'];
    protected $dates = ['created_at'];

    public function code()
    {
        return $this->belongsTo(Code::class, 'code_id');
    }

    public function getNominalRupiahAttribute()
    {
        if ($this->nominal)
        {
            return number_format($this->nominal,0,",",".");
        }
        return $this->nominal;
    }
}
