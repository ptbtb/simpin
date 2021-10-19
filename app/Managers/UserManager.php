<?php
namespace App\Managers;

use App\Models\Anggota;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use App\Events\User\UserCreated;

use Auth;
use Illuminate\Support\Facades\Hash;

class UserManager 
{
    static function createUser(Anggota $anggota, $password) {
        try {
            // jenis anggota PENSIUNAN TIDAK AKTIF tidak perlu create user
            if($anggota->id_jenis_anggota != JENIS_ANGGOTA_PENSIUNAN_TIDAK_AKTIF)
            {
                $user = new User();
                $user->name = $anggota->nama_anggota;
                $user->kode_anggota = $anggota->kode_anggota;
                $user->email = $anggota->email;
                $user->password = Hash::make($password);
                $user->created_by = Auth::user()->id;
                $user->save();
                
                $user->activation_code = uniqid().$user->id;
                $user->save();

                // assign role anggota to user as default
                $role = Role::where('id',ROLE_ANGGOTA)->first();
                $user->assignRole($role->name);
                $user->save();
                
                event(new UserCreated($user, $password));
                return redirect()->route('user-list')->withSuccess('Create user Success');
            }
            else
            {
                return redirect()->route('user-list');
            }
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