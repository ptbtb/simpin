<?php
namespace App\Managers;

use App\Events\Pinjaman\PengajuanApproved;
use App\Mail\PengajuanPinjamanApproved;
use App\Mail\PengajuanPinjamanCreated;
use App\Mail\PengajuanPinjamanPayment;
use App\Mail\PengajuanPinjamanUpdated;
use App\Mail\RegistrationCompleted;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

    static function sendEmailApprovalPengajuanPinjaman(Pengajuan $pengajuan)
    {
        $operatorSimpin = User::operatorSimpin()->get();
        foreach ($operatorSimpin as $user)
        {
            $email = $user->email;
            $details = [
                'subject' => 'Konfirmasi Pengajuan Pinjaman',
                'pengajuan' => $pengajuan,
                'user' => $user
            ];

            Mail::to($email)->send(new PengajuanPinjamanCreated($details));
        }
    }

    static function sendEmailUpdatePengajuanPinjaman(Pengajuan $pengajuan)
    {
        
        if ($pengajuan->menungguApprovalSpv())
        {
            $users = User::spv()->get();
            foreach ($users as $user)
            {
                $email = $user->email;
                $details = [
                    'subject' => 'Approval Pengajuan Pinjaman',
                    'pengajuan' => $pengajuan,
                    'user' => $user
                ];

                Mail::to($email)->send(new PengajuanPinjamanUpdated($details));
            }
            
        }
        elseif($pengajuan->menungguApprovalAsman())
        {
            $users = User::asman()->get();
            foreach ($users as $user)
            {
                $email = $user->email;
                $details = [
                    'subject' => 'Approval Pengajuan Pinjaman',
                    'pengajuan' => $pengajuan,
                    'user' => $user
                ];

                Mail::to($email)->send(new PengajuanPinjamanUpdated($details));
            }
        }
        elseif($pengajuan->menungguApprovalManager())
        {
            $users = User::manager()->get();
            foreach ($users as $user)
            {
                $email = $user->email;
                $details = [
                    'subject' => 'Approval Pengajuan Pinjaman',
                    'pengajuan' => $pengajuan,
                    'user' => $user
                ];

                Mail::to($email)->send(new PengajuanPinjamanUpdated($details));
            }
        }
        elseif($pengajuan->menungguApprovalBendahara())
        {
            $users = User::bendahara()->get();
            foreach ($users as $user)
            {
                $email = $user->email;
                $details = [
                    'subject' => 'Approval Pengajuan Pinjaman',
                    'pengajuan' => $pengajuan,
                    'user' => $user
                ];

                Mail::to($email)->send(new PengajuanPinjamanUpdated($details));
            }
        }
        elseif($pengajuan->menungguApprovalKetua())
        {
            $users = User::ketua()->get();
            foreach ($users as $user)
            {
                $email = $user->email;
                $details = [
                    'subject' => 'Approval Pengajuan Pinjaman',
                    'pengajuan' => $pengajuan,
                    'user' => $user
                ];

                Mail::to($email)->send(new PengajuanPinjamanUpdated($details));
            }
            event(new PengajuanApproved($pengajuan));
        }
        elseif($pengajuan->menungguPembayaran())
        {
            $users = User::operatorSimpin()->get();
            foreach ($users as $user)
            {
                $email = $user->email;
                $details = [
                    'subject' => 'Pengajuan Pinjaman Menunggu Pembayaran',
                    'pengajuan' => $pengajuan,
                    'user' => $user
                ];

                Mail::to($email)->send(new PengajuanPinjamanPayment($details));
            }
        }
    }

    static function sendEmailPengajuanPinjamanApproved(Pengajuan $pengajuan)
    {
        $email = $pengajuan->anggota->email;
        $anggota = $pengajuan->anggota;
        $details = [
            'subject' => 'Pengajuan Pinjaman Diterima',
            'pengajuan' => $pengajuan,
            'anggota' => $anggota
        ];

        Mail::to($email)->send(new PengajuanPinjamanApproved($details));
    }
}