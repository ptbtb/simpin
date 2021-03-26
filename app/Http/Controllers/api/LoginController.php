<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
                'message' => 'Incorrect email'
            ]);
        }
        elseif(!Hash::check($request->password, $user->password))
        {
            throw ValidationException::withMessages([
                'message' => 'Incorrect password'
            ]);
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
