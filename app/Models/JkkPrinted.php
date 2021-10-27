<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JkkPrinted extends Model
{
    use HasFactory;

    protected $table = 'jkk_printed';
    protected $dates = ['printed_at'];
    protected $appends = ['printed_at_view', 'printed_by_view'];

    public function isPengajuanPinjaman()
    {
        return $this->jkk_printed_type_id == JKK_PRINTED_TYPE_PENGAJUAN_PINJAMAN;
    }

    public function isPenarikanSimpanan()
    {
        return $this->jkk_printed_type_id == JKK_PRINTED_TYPE_PENARIKAN_SIMPANAN;
    }

    public function jkkPengajuan()
    {
        return $this->hasMany(Pengajuan::class, 'no_jkk', 'jkk_number');
    }

    public function printedBy()
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

    public function jkkPenarikan()
    {
        return $this->hasMany(Penarikan::class, 'no_jkk', 'jkk_number');
    }

    public function jkkPrintedType()
    {
        return $this->belongsTo(JkkPrintedType::class);
    }

    public function getPrintedAtViewAttribute()
    {
        return $this->printed_at->format('d M Y');
    }

    public function getPrintedByViewAttribute()
    {
        return $this->printedBy->name;
    }
}