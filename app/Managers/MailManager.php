<?php
namespace App\Managers;

use App\Events\Pinjaman\PengajuanApproved;
use App\Mail\PenarikanApproved;
use App\Mail\PenarikanCreated;
use App\Mail\PenarikanPayment;
use App\Mail\PenarikanUpdated;
use App\Mail\PengajuanPinjamanApproved;
use App\Mail\PengajuanPinjamanCreated;
use App\Mail\PengajuanPinjamanPayment;
use App\Mail\PengajuanPinjamanUpdated;
use App\Mail\RegistrationCompleted;
use App\Models\Penarikan;
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

    static function sendEmailApprovalPenarikan(Penarikan $penarikan)
    {
        $operatorSimpin = User::operatorSimpin()->get();
        foreach ($operatorSimpin as $user)
        {
            $email = $user->email;
            $details = [
                'subject' => 'Konfirmasi Pengajuan Penarikan',
                'penarikan' => $penarikan,
                'user' => $user
            ];

            Mail::to($email)->send(new PenarikanCreated($details));
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

    static function sendEmailUpdatePenarikan(Penarikan $penarikan)
    {
        
        if ($penarikan->menungguApprovalSpv())
        {
            $users = User::spv()->get();
            foreach ($users as $user)
            {
                $email = $user->email;
                $details = [
                    'subject' => 'Approval Penarikan',
                    'penarikan' => $penarikan,
                    'user' => $user
                ];

                Mail::to($email)->send(new PenarikanUpdated($details));
            }
            
        }
        elseif($penarikan->menungguApprovalAsman())
        {
            $users = User::asman()->get();
            foreach ($users as $user)
            {
                $email = $user->email;
                $details = [
                    'subject' => 'Approval Penarikan',
                    'penarikan' => $penarikan,
                    'user' => $user
                ];

                Mail::to($email)->send(new PenarikanUpdated($details));
            }
        }
        elseif($penarikan->menungguApprovalManager())
        {
            $users = User::manager()->get();
            foreach ($users as $user)
            {
                $email = $user->email;
                $details = [
                    'subject' => 'Approval Penarikan',
                    'penarikan' => $penarikan,
                    'user' => $user
                ];

                Mail::to($email)->send(new PenarikanUpdated($details));
            }
        }
        elseif($penarikan->menungguApprovalBendahara())
        {
            $users = User::bendahara()->get();
            foreach ($users as $user)
            {
                $email = $user->email;
                $details = [
                    'subject' => 'Approval Penarikan',
                    'penarikan' => $penarikan,
                    'user' => $user
                ];

                Mail::to($email)->send(new PenarikanUpdated($details));
            }
        }
        elseif($penarikan->menungguApprovalKetua())
        {
            $users = User::ketua()->get();
            foreach ($users as $user)
            {
                $email = $user->email;
                $details = [
                    'subject' => 'Approval Penarikan',
                    'penarikan' => $penarikan,
                    'user' => $user
                ];

                Mail::to($email)->send(new PenarikanUpdated($details));
            }
        }
        elseif($penarikan->menungguPembayaran())
        {
            $users = User::operatorSimpin()->get();
            foreach ($users as $user)
            {
                $email = $user->email;
                $details = [
                    'subject' => 'Penarikan Menunggu Pembayaran',
                    'penarikan' => $penarikan,
                    'user' => $user
                ];

                Mail::to($email)->send(new PenarikanPayment($details));
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

    static function sendEmailPenarikanApproved(Penarikan $penarikan)
    {
        $email = $penarikan->anggota->email;
        $anggota = $penarikan->anggota;
        $details = [
            'subject' => 'Pengajuan Penarikan Diterima',
            'penarikan' => $penarikan,
            'anggota' => $anggota
        ];

        Mail::to($email)->send(new PenarikanApproved($details));
    }
}