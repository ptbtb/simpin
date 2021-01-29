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
           $receiver = User::operatorSimpin()->get();
           $roleReceiver = $receiver->first()->roles;
           $jenisPinjaman = JenisPinjaman::where('kode_jenis_pinjam', $pengajuan->kode_jenis_pinjam)->get()->first()->nama_pinjaman;
           $namaPeminjam = User::where('kode_anggota', $pengajuan->kode_anggota)->get()->first()->name;
           
           foreach ($receiver as $user)
           {
                $notifikasi = new Notification();
                $notifikasi->role_id=$roleReceiver->first()->id;
                $notifikasi->receiver=$user->id;
                $notifikasi->peminjam = User::where('kode_anggota', $pengajuan->kode_anggota)->get()->first()->id;
                $notifikasi->informasi_notifikasi = $namaPeminjam. ' telah melakukan pengajuan pinjaman ' . $jenisPinjaman . ' sebesar Rp ' . number_format($pengajuan->besar_pinjam,0,",",".");
                $notifikasi->has_read = 0;
                $notifikasi->keterangan = 'Konfirmasi Persetujuan Pengajuan Pinjaman';
                $notifikasi->url=route('pengajuan-pinjaman-list');
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
            if($pengajuan->menungguApprovalSpv()) {
                $receiver = User::spv()->get();
            } elseif ($pengajuan->menungguApprovalAsman()) {
                $receiver = User::asman()->get();
            } elseif ($pengajuan->menungguApprovalManager()) {
                $receiver = User::manager()->get(); 
            } elseif ($pengajuan->menungguApprovalBendahara()) {
                $receiver = User::bendahara()->get(); 
            } elseif ($pengajuan->menungguApprovalKetua()) {
                $receiver = User::ketua()->get(); 
            }

            $roleReceiver = $receiver->first()->roles;
            $jenisPinjaman = JenisPinjaman::where('kode_jenis_pinjam', $pengajuan->kode_jenis_pinjam)->get()->first()->nama_pinjaman;
            $namaPeminjam =  User::where('kode_anggota', $pengajuan->kode_anggota)->get()->first()->name;

            foreach ($receiver as $user)
            {
                $notifikasi = new Notification();
                $notifikasi->role_id=$roleReceiver->first()->id;
                $notifikasi->receiver=$user->id;
                $notifikasi->peminjam = User::where('kode_anggota', $pengajuan->kode_anggota)->get()->first()->id;
                $notifikasi->informasi_notifikasi = $namaPeminjam. ' telah melakukan pengajuan pinjaman ' .$jenisPinjaman. ' sebesar Rp ' . number_format($pengajuan->besar_pinjam,0,",",".");
                $notifikasi->has_read = 0;
                $notifikasi->keterangan = 'Persetujuan Pengajuan Pinjaman oleh ' . $roleReceiver->first()->name;
                $notifikasi->url=route('pengajuan-pinjaman-list');
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
    
    static function sendNotificationPengajuanApproved(Pengajuan $pengajuan) {
       try {
           $receiver = User::asman()->get();
           $roleReceiver = $receiver->first()->roles;
           $jenisPinjaman = JenisPinjaman::where('kode_jenis_pinjam', $pengajuan->kode_jenis_pinjam)->get()->first()->nama_pinjaman;
           
           foreach ($receiver as $user)
           {
                $notifikasi = new Notification();
                $notifikasi->role_id=$roleReceiver->first()->id;
                $notifikasi->receiver=$user->name;
                $notifikasi->peminjam = User::where('kode_anggota', $pengajuan->kode_anggota)->get()->first()->name;
                $notifikasi->informasi_notifikasi = $notifikasi->peminjam . ' telah melakukan pengajuan pinjaman ' . $jenisPinjaman . ' sebesar Rp ' . number_format($pengajuan->besar_pinjam,0,",",".");
                $notifikasi->has_read = 0;
                $notifikasi->keterangan = 'Persetujuan Pengajuan Pinjaman oleh ASMAN';
                $notifikasi->url=route('pengajuan-pinjaman-list');
                // $notifikasi->save();

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
}