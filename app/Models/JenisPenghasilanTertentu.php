<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPenghasilanTertentu extends Model
{
    use HasFactory;

    protected $table = 'jenis_penghasilan_tertentu';
    protected $appends = ['input_name'];

    public function scopeShow($query)
    {
        return $query->where('is_visible', 1);
    }

    public function getInputNameAttribute()
    {
        $name = strtolower($this->name);
        return str_replace(' ','_',$name);
    }
}
