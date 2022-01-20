<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model implements Auditable
{
  use HasFactory;
  use Userstamps;
  use SoftDeletes;
  use \OwenIt\Auditing\Auditable;

    protected $table = 't_company';

    public function companyGroup()
    {
        return $this->belongsTo(CompanyGroup::class, 'company_group_id', 'id');
    }
    public function anggota()
    {
        return $this->hasMany(Anggota::class, 'company_id', 'id');
    }

    public function kelasCompany()
    {
        return $this->hasMany(kelasCompany::class, 'company_id', 'id');
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
