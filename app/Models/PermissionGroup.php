<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class PermissionGroup extends Model
{
    use HasFactory;

    protected $table = "permissions_group";

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'permissions_group_id', 'id');
    }
}
