<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 't_company';

    public function companyGroup()
    {
        return $this->belongsTo(CompanyGroup::class, 'company_group_id', 'id');
    }

    public function isKopegmarGroup()
    {
        return $this->company_group_id == COMPANY_GROUP_KOPEGMAR;
    }

    public function isKojaGroup()
    {
        return $this->company_group_id == COMPANY_GROUP_KOJA;
    }
}
