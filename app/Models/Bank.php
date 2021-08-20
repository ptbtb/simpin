<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Bank extends Model
{
    use HasFactory;
    use Userstamps;
    use SoftDeletes;
    protected $table = "t_bank";
    protected $dates = ['deleted_at'];
}
