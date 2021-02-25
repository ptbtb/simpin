<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingJurnal extends Model
{
    use HasFactory;
    use \Wildside\Userstamps;
    protected $table = 't_jurnal';
    protected $primaryKey = 'id';
}
