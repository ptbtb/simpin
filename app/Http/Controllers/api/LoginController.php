<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required' => 'email'],
            'password' => ['required']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user)
        {
            throw ValidationException::withMessages([
                'Email Salah'
            ]);
        }
        elseif(!Hash::check($request->password, $user->password))
        {
            throw ValidationException::withMessages([
                'Password Salah'
            ]);
        }

        $trans = DB::table('company_setting')
                    ->where('name','version') 
                    ->first();

        $version=$trans->value;   

        if($request->version){
            if($request->version!==$version){
                throw ValidationException::withMessages([
                 'Versi terbaru sudah tersedia, Mohon Update Aplikasi Anda Terlebih Dahulu'
            ]);
            }
        }    
        $token = $user->createToken('Auth Token')->accessToken;
        $response = [
            "message"=>"",
            "errors"=>null,
            "token_type" => "Bearer",
            "access_token" => $token
        ];
        return response()->json($response, 200);
    }
}
