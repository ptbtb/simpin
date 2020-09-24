<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Lcobucci\JWT\Parser;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        return $request->user();
    }

    public function logout(Request $request)
    {
        $userToken = $request->user()->token();
        $userToken->revoke();
        return response()->json(['message' => 'Logged out'], 200);
    }
}
