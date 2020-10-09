<?php
namespace App\Managers;

use App\Mail\RegistrationCompleted;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class MailManager 
{
    static function sendEmailRegistrationCompleted(User $user, $password)
    {
        $body = '';
        $details = [
            'subject' => 'Registration Completed',
            'anggota' => $user->anggota,
            'user' => $user,
            'password' => $password
        ];

        Mail::to($user->email)->send(new RegistrationCompleted($details));
        return "Email Sent";
    }
}