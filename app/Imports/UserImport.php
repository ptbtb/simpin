<?php

namespace App\Imports;

use App\Events\User\UserCreated;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Spatie\Permission\Models\Role;

class UserImport implements OnEachRow
{
    /**
    * @param Collection $collection
    */
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();
        if ($rowIndex == 1)
        {
            return null;
        }
        $password = uniqid();
        $fields = [
            'kode_anggota' => $row[0],
            'name' => $row[1],
            'email' => trim($row[2]),
            'password' => Hash::make($password),
        ];
        
        // if user is excist, next
        $user = User::where('email',$fields['email'])->first();
        if ($user)
        {
            return null;
        }
        
        $user = User::create($fields);
        $role = Role::where('id',ROLE_ANGGOTA)->first();
        $user->assignRole($role->name);
        $user->activation_code = uniqid().$user->id;
        $user->save();
        event(new UserCreated($user, $password));
    }
}
