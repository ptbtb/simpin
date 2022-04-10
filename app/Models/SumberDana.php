<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumberDana extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't_sumber_dana';

    public function codes()
    {
        return $this->hasMany('App\Models\Code', 'sumber_dana_id');
    }
}
