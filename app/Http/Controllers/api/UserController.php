<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
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

    public function disclaimer(Request $request)
    {
        $trans = DB::table('company_setting')
                    ->where('name','company_splash') 
                    ->first();
        
        return response()->json(['message' => $trans->value], 200);
    }

    public function menu(Request $request)
    {
        $trans = DB::table('menu_mobile')
                    ->where('display',1) 
                    ->get();
        $result['message']=null;
        $result['data']=$trans;

        return response()->json($result, 200);
    }
}
