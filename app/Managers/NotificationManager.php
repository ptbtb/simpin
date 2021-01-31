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
            } elseif($pengajuan->menungguPembayaran()){
                $receiver = User::operatorSimpin()->get();
            }

            $keterangan = '';
            $informasi_notifikasi = '';
            $roleReceiver = $receiver->first()->roles;
            $jenisPinjaman = JenisPinjaman::where('kode_jenis_pinjam', $pengajuan->kode_jenis_pinjam)->get()->first()->nama_pinjaman;
            $namaPeminjam =  User::where('kode_anggota', $pengajuan->kode_anggota)->get()->first()->name;

            if($pengajuan->menungguPembayaran()){
                // Notifikasi untuk Operator Simpin/Kasir
                $keterangan = "Pengajuan Pinjaman oleh " .$namaPeminjam. " menunggu pembayaran oleh " . $roleReceiver->first()->name;
                $informasi_notifikasi = "Pengajuan Pinjaman " .$jenisPinjaman. " oleh " .$namaPeminjam. ' menunggu pembayaran dari anda';
            } else {
                $keterangan = 'Persetujuan Pengajuan Pinjaman oleh ' . $roleReceiver->first()->name;
                $informasi_notifikasi = $namaPeminjam. ' telah melakukan pengajuan pinjaman ' .$jenisPinjaman. ' sebesar Rp ' . number_format($pengajuan->besar_pinjam,0,",",".");
            }


            foreach ($receiver as $user)
            {
                $notifikasi = new Notification();
                $notifikasi->role_id=$roleReceiver->first()->id;
                $notifikasi->receiver=$user->id;
                $notifikasi->peminjam = User::where('kode_anggota', $pengajuan->kode_anggota)->get()->first()->id;
                $notifikasi->informasi_notifikasi = $informasi_notifikasi;
                $notifikasi->has_read = 0;
                $notifikasi->keterangan = $keterangan;
                $notifikasi->url=route('pengajuan-pinjaman-list');
                $notifikasi->save();
            }


            // Notifikasi untuk user
            if($pengajuan->menungguPembayaran()){
                $notifikasi = new Notification();
                $notifikasi->role_id=ROLE_ANGGOTA;
                $notifikasi->receiver= $notifikasi->peminjam = User::where('kode_anggota', $pengajuan->kode_anggota)->get()->first()->id;
                $notifikasi->informasi_notifikasi = "Pengajuan pinjaman anda telah diterima. Silakan menunggu pembayaran oleh kasir ";
                $notifikasi->has_read = 0;
                $notifikasi->keterangan = "Pengajuan Pinjaman Menunggu Pembayaran";
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
           $receiver = User::where('kode_anggota', $pengajuan->kode_anggota)->get()->first();
           $roleReceiver = $receiver->roles;
           $jenisPinjaman = JenisPinjaman::where('kode_jenis_pinjam', $pengajuan->kode_jenis_pinjam)->get()->first()->nama_pinjaman;
           $namaPeminjam =  $receiver->name;
           
           $notifikasi = new Notification();
           $notifikasi->role_id=$roleReceiver->first()->id;
           $notifikasi->receiver= $notifikasi->peminjam = $receiver->id;
           $notifikasi->informasi_notifikasi = 'Selamat !!! Pengajuan pinjaman ' . $jenisPinjaman . ' sebesar Rp ' . number_format($pengajuan->besar_pinjam,0,",","."). "oleh anda telah diterima";
           $notifikasi->has_read = 0;
           $notifikasi->keterangan = 'Pengajuan Pinjaman Diterima';
           $notifikasi->url=route('pengajuan-pinjaman-list');
           $notifikasi->save();

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