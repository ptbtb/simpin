<?php
namespace App\Managers;

use App\Models\Anggota;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use App\Events\User\UserCreated;

use Auth;


class UserManager 
{
    static function createUser(Anggota $anggota, $password) {
        try {
            $user = new User();
            $user->name = $anggota->nama_anggota;
            $user->kode_anggota = $anggota->kode_anggota;
            $user->email = $anggota->email;
            $user->password = $password;
			$user->created_by = Auth::user()->id;
            $user->save();
            
            $user->activation_code = uniqid().$user->id;
            $user->save();

            // assign role anggota to user as default
            $role = Role::where('id',ROLE_ANGGOTA)->first();
            $user->assignRole($role->name);
            $user->save();
            
            event(new UserCreated($user, $user->password));
			return redirect()->route('user-list')->withSuccess('Create user Success');
        }
        catch(\Exception $e) {
            $message = $e->getMessage();
			if (isset($e->errorInfo[2]))
			{
				$message = $e->errorInfo[2];
			}
			return redirect()->back()->withError($message);

        }

    }    
}