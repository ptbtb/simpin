<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    use HasFactory;

    protected $table = "t_jurnal_umum";
    protected $fillable = ['code_id', 'nominal'];
    protected $appends = ['nominal_rupiah'];

    public function code()
    {
        return $this->belongsTo(Code::class, 'code_id');
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
