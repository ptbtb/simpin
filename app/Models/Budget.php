<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $table = 'budget';
    protected $dates = ['date'];
    protected $appends = ['date_view', 'created_by_view', 'numeric_amount'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function code()
    {
        return $this->belongsTo(Code::class, 'code', 'CODE');
    }

    public function getDateViewAttribute()
    {
        if ($this->date)
        {
            return $this->date->format('M Y');
        }

        return '-';
    }

    public function getCreatedByViewAttribute()
    {
        return $this->createdBy->name;
    }
    
    /**
     * Get the numericAmount
     *
     * @param  string  $value
     * @return integer
     */
    public function getNumericAmountAttribute()
    {
        if(!is_null($this->amount))
        {
            $str = str_replace(',', '', $this->amount);
            return (int) $str;
        }
        return null;
    }
}
