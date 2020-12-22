<?php

namespace App\Http\Controllers;

use App\Events\User\UserCreated;
use App\Exports\UserExport;
use App\Imports\UserImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use App\Models\User;
use App\Models\Anggota;
use App\Models\KelasCompany;

use Auth;
use Carbon\Carbon;
use Storage;
use Excel;
// use Yajra\DataTables\DataTables;
use Yajra\DataTables\Facades\DataTables;

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
		$users = $users->orderBy('created_at','asc');
		// $users = $users->get();
		// $users->map(function ($user, $key)
		// {
		// 	$user->number = $key+1;
		// 	return $user;
		// });
		// return $users;
		return DataTables::eloquent($users)->make(true);
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
			$user->created_by = Auth::user()->id;
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
			$user->activation_code = uniqid().$user->id;
			$user->save();

			event(new UserCreated($user, $request->password));
			
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

		if (is_null($user->anggota))
			$classList = "";
		else
			$classList = KelasCompany::where('company_id', $user->anggota->company_id)->get();
		
    	$data['title'] = 'Edit Profile';
    	$data['user'] = $user;
		$data['classList'] = $classList;
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
		$user->salary = $request->salary;
		$user->kelas_company_id = $request->kelas_company;
		$file = $request->photo;
		$file_ktp = $request->ktp_photo;
		$file_salary = $request->salary_slip;

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
		
		if ($file_ktp)
		{
			$config['disk'] = 'upload';
			$config['upload_path'] = '/user/'.$user->id.'/ktp'; 
			$config['public_path'] = env('APP_URL') . '/upload/user/'.$user->id.'/ktp';

			// create directory if doesn't exist
			if (!Storage::disk($config['disk'])->has($config['upload_path']))
			{
				Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
			}

			// upload file if valid
			if ($file_ktp->isValid())
			{
				$filename = uniqid() .'.'. $file_ktp->getClientOriginalExtension();

				Storage::disk($config['disk'])->putFileAs($config['upload_path'], $file_ktp, $filename);
				$user->photo_ktp_path = $config['disk'].$config['upload_path'].'/'.$filename;
			}
		}
		
		if ($file_salary)
		{
			$config['disk'] = 'upload';
			$config['upload_path'] = '/user/'.$user->id.'/salary'; 
			$config['public_path'] = env('APP_URL') . '/upload/user/'.$user->id.'/salary';

			// create directory if doesn't exist
			if (!Storage::disk($config['disk'])->has($config['upload_path']))
			{
				Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
			}

			// upload file if valid
			if ($file_salary->isValid())
			{
				$filename = uniqid() .'.'. $file_salary->getClientOriginalExtension();

				Storage::disk($config['disk'])->putFileAs($config['upload_path'], $file_salary, $filename);
				$user->salary_path = $config['disk'].$config['upload_path'].'/'.$filename;
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

	public function validation($validationId)
	{
		try
		{
			$user = User::where('activation_code',$validationId)->first();
			if (is_null($user))
			{
				abort(404);
			}
			if ($user->isVerified())
			{
				return redirect()->route('login');
			}
			$user->is_verified = 1;
			$user->email_verified_at = Carbon::now();
			$user->save();
			return redirect()->route('login')->withSuccess('Akun anda berhasil di verifikasi. silahkan login');
		}
		catch (\Throwable $e)
		{
			$message = $e->getMessage();
			if (isset($e->errorInfo[2]))
			{
				$message = $e->errorInfo[2];
			}
			return redirect()->back()->withError($message);
		}
	}

	public function importExcel()
	{
		$this->authorize('import user', Auth::user());
        $data['title'] = 'Import User';
        return view('user.import', $data);
	}

	public function storeImportExcel(Request $request)
    {
        $this->authorize('import user', Auth::user());
        try
        {
            Excel::import(new UserImport, $request->file);
            return redirect()->back()->withSuccess('Import data berhasil');
        }
        catch (\Throwable $e)
        {
            \Log::error($e);
            return redirect()->back()->withError('Gagal import data');
        }
        
	}
	
	public function createExcel(Request $request)
    {
        try
        {
			$user = Auth::user();
			$this->authorize('export user', $user);
			$filename = 'export_user_excel_'.Carbon::now()->format('d M Y').'.xlsx';
			return Excel::download(new UserExport($request), $filename, \Maatwebsite\Excel\Excel::XLSX);
        }
        catch (\Throwable $e)
        {
            \Log::error($e);
            return redirect()->back()->withError('Gagal Export data');
        }
    }
}
