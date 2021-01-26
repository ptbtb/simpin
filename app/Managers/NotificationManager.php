<?php
namespace App\Managers;

use App\Models\Notification;
use App\Models\Pinjaman;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\JenisPinjaman;
use Spatie\Permission\Models\Role;

use Carbon\Carbon;

class NotificationManager 
{
    static function sendNotificationApprovalPengajuanPinjaman(Pengajuan $pengajuan) {
       try {
            $notifikasi = new Notification();
            $receiver = User::operatorSimpin()->get();
            foreach ($receiver as $user)
            {
                $roleReceiver = $receiver->first()->roles;

                $notifikasi->role_id=$roleReceiver->first()->id;
                $notifikasi->receiver=$user->name;
                $notifikasi->peminjam = User::where('kode_anggota', $pengajuan->kode_anggota)->get()->first()->name;
                $notifikasi->informasi_notifikasi = $notifikasi->peminjam . ' telah melakukan pengajuan pinjaman sebesar Rp ' . number_format($pengajuan->besar_pinjam,0,",",".");
                $notifikasi->has_read = 0;
                $notifikasi->keterangan = JenisPinjaman::where('kode_jenis_pinjam', $pengajuan->kode_jenis_pinjam)->get()->first()->nama_pinjaman;
                $notifikasi->url='';
                $notifikasi->save();
            }

       } catch (\Exception $e) {
            $message = $e->getMessage();
			if (isset($e->errorInfo[2]))
			{
				$message = $e->errorInfo[2];
			}
			return redirect()->back()->withError($message);
       }

    }    
    
    static function sendNotificationUpdatePengajuanPinjaman(Pengajuan $pengajuan) {
       try {
           dd($pengajuan);
       } catch (\Throwable $e) {
           \Log::info($e);
       }

    }    
    
    static function sendNotificationPengajuanApproved(Pengajuan $pengajuan) {
       try {
           dd($pengajuan);
       } catch (\Throwable $e) {
           \Log::info($e);
       }

    }    
}