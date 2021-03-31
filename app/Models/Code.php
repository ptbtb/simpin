<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class Code extends Model
{
    use HasFactory;

    protected $table = "t_code";
    protected $primaryKey = "id";
    public $timestamps = true;

    public function codeCategory()
    {
        return $this->belongsTo(CodeCategory::class, 'code_category_id');
    }

    public function codeType()
    {
        return $this->belongsTo(CodeType::class, 'code_type_id');
    }

    public function saldoAwals()
    {
        return $this->hasMany(SaldoAwal::class);
    }

    public function jurnalUmumItems()
    {
        return $this->hasMany(JurnalUmumItem::class);
    }
}
