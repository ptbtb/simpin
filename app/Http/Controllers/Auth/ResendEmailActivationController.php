<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Managers\MailManager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ResendEmailActivationController extends Controller
{
    public function create()
    {
        return view('auth.passwords.resendEmail');
    }

    public function store(Request $request)
    {
        try
        {
            $user = User::where('email', $request->email)
                        ->first();

            if (is_null($user))
            {
                return redirect()->back()->withErrors('Email tidak terdaftar');
            }

            $password = uniqid();
            $user->password = Hash::make($password);
            $user->save();

            MailManager::sendEmailRegistrationCompleted($user, $password);
            return redirect()->back()->withSuccess('Email aktifasi terkirim. Silahkan periksa email anda');
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }
}
