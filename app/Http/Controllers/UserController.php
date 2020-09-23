<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use App\Models\User;
use App\Models\Anggota;

use Auth;
use Datatables;
use Storage;

class UserController extends Controller
{
	public function index(Request $request)
	{
		$this->authorize('view user', Auth::user());
		$currentUser = Auth::user();
		if ($currentUser->hasRole('Admin'))
		{
			$roles = Role::get();
			$data['roles'] = $roles;
		}
		else
		{
			if ($currentUser->can('filter user'))
			{
				$roles = Role::where('id', ROLE_ANGGOTA)->get();
				$data['roles'] = $roles;
			}							
		}
		$data['title'] = 'List User';
		$data['request'] = $request;
		return view('user.index', $data);
	}

	public function indexAjax(Request $request)
	{
		$this->authorize('view user', Auth::user());
		$users = User::with('roles');
		$currentUser = Auth::user();
		if(isset($request->role_id) && $request->role_id !== '')
        {     
            $users = $users->whereHas('roles', function ($query) use ($request)
			{
				return $query->where('id', $request->role_id);
			});
		}
		if (!$currentUser->hasRole('Admin'))
		{
			$users = $users->whereHas('roles', function ($query)
			{
				return $query->where('id', ROLE_ANGGOTA);
			});
		}
		$users = $users->get();
		$users->map(function ($user, $key)
		{
			$user->number = $key+1;
			return $user;
		});
		return $users;
	}

	public function create()
	{
		$this->authorize('add user', Auth::user());
		$currentUser = Auth::user();
		if ($currentUser->hasRole('Admin'))
		{
			$roles = Role::get();
		}
		else
		{
			$roles = Role::where('name', 'Anggota')->get();
		}
		$data['roles'] = $roles;
		$data['title'] = 'Create User';
		return view('user.create', $data);
	}

	public function store(Request $request)
	{
		$this->authorize('add user', Auth::user());
		try
		{
			$anggota = null;
			if(isset($request->kode_anggota))
			{
				$anggota = Anggota::where('kode_anggota', $request->kode_anggota)->first();
			}
			$user = new User();
			$user->email = $request->email;
			if($anggota)
			{
				$user->name = $anggota->nama_anggota;
				$user->kode_anggota = $anggota->kode_anggota;
			}
			else
			{
				$user->name = $request->name;
			}
			$user->password = Hash::make($request->password);
			$user->save();

			$file = $request->photo;
			if ($file)
			{
				$config['disk'] = 'upload';
				$config['upload_path'] = '/user/'.$user->id.'/photo'; 
				$config['public_path'] = env('APP_URL') . '/upload/user/'.$user->id.'/photo';

				// create directory if doesn't exist
				if (!Storage::disk($config['disk'])->has($config['upload_path']))
				{
					Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
				}

				// upload file if valid
				if ($file->isValid())
				{
					$filename = uniqid() .'.'. $file->getClientOriginalExtension();

					Storage::disk($config['disk'])->putFileAs($config['upload_path'], $file, $filename);
					$user->photo_profile_path = $config['disk'].$config['upload_path'].'/'.$filename;
				}
			}

			// asign role user
			$role = Role::where('id',$request->role_id)->first();
			$user->assignRole($role->name);
			$user->save();
			
			return redirect()->route('user-list')->withSuccess('Create user Success');
		}
		catch (\Exception $e)
		{
			$message = $e->getMessage();
			if (isset($e->errorInfo[2]))
			{
				$message = $e->errorInfo[2];
			}
			return redirect()->back()->withError($message);
		}
	}

	public function edit($id)
	{
		$this->authorize('edit user', Auth::user());
		$user = User::findOrFail($id);
		$currentUser = Auth::user();
		if ($currentUser->hasRole('Admin'))
		{
			$roles = Role::get();
		}
		else
		{
			$roles = Role::where('name', 'Anggota')->get();
		}
		
		$data['user'] = $user;
		$data['roles'] = $roles;
		$data['title'] = 'Edit User';
		return view('user.edit', $data);
	}

	public function update($id, Request $request)
	{
		$this->authorize('edit user', Auth::user());
		$user = User::find($request->user_id);
    	if (is_null($user))
    	{
    		return redirect()->back()->withMessage('User not found');
		}
		
		if ($request->reset_password)
		{
			$user->password = Hash::make($request->new_password);
			$user->save();
			return redirect()->back()->withSuccess('Reset Password Success');
		}

		$user->name = $request->name;
		$file = $request->photo;
		if ($file)
		{
			$config['disk'] = 'upload';
			$config['upload_path'] = '/user/'.$user->id.'/photo'; 
			$config['public_path'] = env('APP_URL') . '/upload/user/'.$user->id.'/photo';

			// create directory if doesn't exist
			if (!Storage::disk($config['disk'])->has($config['upload_path']))
			{
				Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
			}

			// upload file if valid
			if ($file->isValid())
			{
				$filename = uniqid() .'.'. $file->getClientOriginalExtension();

				Storage::disk($config['disk'])->putFileAs($config['upload_path'], $file, $filename);
				$user->photo_profile_path = $config['disk'].$config['upload_path'].'/'.$filename;
			}
		}

		// asign role user
		$role = Role::where('id',$request->role_id)->first();
		$userRole = $user->roles->first();
		$user->removeRole($userRole->name);
		$user->assignRole($role->name);
    	$user->save();
    	return redirect()->route('user-list')->withSuccess('Update user success');
	}

	public function delete($id)
	{
		$this->authorize('delete user', Auth::user());
		$user = User::findOrFail($id);
		$user->delete();
		return redirect()->back()->withSuccess('Delete user success');
	}
	
    public function profile()
    {
    	$user = Auth::user();
    	$data['title'] = 'Edit Profile';
    	$data['user'] = $user;
    	return view('user.profile', $data);
    }

    public function updateProfile(Request $request)
    {
    	$user = User::find($request->user_id);
    	if (is_null($user))
    	{
    		return redirect()->back()->withMessage('User not found');
    	}

		$user->name = $request->name;
		$file = $request->photo;
		if ($file)
		{
			$config['disk'] = 'upload';
			$config['upload_path'] = '/user/'.$user->id.'/photo'; 
			$config['public_path'] = env('APP_URL') . '/upload/user/'.$user->id.'/photo';

			// create directory if doesn't exist
			if (!Storage::disk($config['disk'])->has($config['upload_path']))
			{
				Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
			}

			// upload file if valid
			if ($file->isValid())
			{
				$filename = uniqid() .'.'. $file->getClientOriginalExtension();

				Storage::disk($config['disk'])->putFileAs($config['upload_path'], $file, $filename);
				$user->photo_profile_path = $config['disk'].$config['upload_path'].'/'.$filename;
			}
		}
    	$user->save();
    	return redirect()->back()->withMessage('Update profile success');
	}
	
	public function changePassword()
	{
		$user = Auth::user();
    	$data['title'] = 'Change Password';
    	$data['user'] = $user;
    	return view('user.changePassword', $data);
	}

	public function updatePassword(Request $request)
	{
		$user = Auth::user();
		if (!Hash::check($request->old_password, $user->password))
		{
			return redirect()->back()->withError('Old password do not match');
		}

		if ($request->new_password != $request->confirm_new_password)
		{
			return redirect()->back()->withError('new password do not match');
		}

		$hashPassword = Hash::make($request->new_password);
		$user->password = $hashPassword;
		$user->save();
		return redirect()->back()->with(['success' => 'Update password success']);
	}
}
