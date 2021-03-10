<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class CodeCategory extends Model
{
    use HasFactory;

    protected $table = "t_code_category";
    protected $primaryKey = "id";
    public $timestamps = false;

    public function codes()
    {
        return $this->hasMany(Code::class);
    }
}
