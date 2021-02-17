<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
class UserController extends Controller
{
    public function getUser(Request $request)
    {
        try
        {
            $user = $request->user('api');
            $anggota = $user->anggota;
            $user->anggota = $anggota;

            $response['message'] = null;
            $response['data'] = $user;
            return response()->json($response, 200);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            $response['message'] = API_DEFAULT_ERROR_MESSAGE;
            return response()->json($response, 500);
        }
    }

    public function logout(Request $request)
    {
        $userToken = $request->user()->token();
        $userToken->revoke();
        return response()->json(['message' => 'Logged out'], 200);
    }
}
