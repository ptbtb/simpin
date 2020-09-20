<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\Role;
use App\Models\User;

use Auth;
use Storage;

class UserController extends Controller
{
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
