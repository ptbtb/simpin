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
                'email' => 'Incorrect email'
            ]);
        }
        elseif(!Hash::check($request->password, $user->password))
        {
            throw ValidationException::withMessages([
                'password' => 'Incorrect password'
            ]);
        }

        $token = $user->createToken('Auth Token')->accessToken;
        $response = [
            "token_type" => "Bearer",
            "access_token" => $token
        ];
        return response()->json($response, 200);
    }
}
