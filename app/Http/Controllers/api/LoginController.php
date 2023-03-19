<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
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

        $user = User::where('email', $request->email)
        ->where('is_allow',1)
        ->first();

        if (!$user)
        {
            $response = [
            "message"=>"The given data was invalid.",
            "errors"=>'Email Salah'
        ];
        return response()->json($response, 422);
        }
        elseif(!Hash::check($request->password, $user->password))
        {
            $response = [
            "message"=>"The given data was invalid.",
            "errors"=>'Password Salah'
        ];
        return response()->json($response, 422);
        }

        $trans = DB::table('company_setting')
                    ->where('name','version')
                    ->first();

        $version=$trans->value;

        if($request->version){
            if($request->version!==$version){
                $response = [
            "message"=>"The given data was invalid.",
            "errors"=>'Versi terbaru sudah tersedia, Mohon Update Aplikasi Anda Terlebih Dahulu'
        ];
        return response()->json($response, 422);
            }
        }
        if(!$request->version){
            $response = [
            "message"=>"The given data was invalid.",
            "errors"=>'Versi terbaru sudah tersedia, Mohon Update Aplikasi Anda Terlebih Dahulu'
        ];
        return response()->json($response, 422);
        }
        $maintenance= CompanySetting::findorfail(7)->value;
        if($maintenance==1){
            $response = [
                "message"=>"Application is Under Maintenance.",
                "errors"=>'Silahkan Coba Lagi beberapa waktu kembali'
            ];
            return response()->json($response, 422);
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
