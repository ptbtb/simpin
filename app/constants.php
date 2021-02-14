<?php

define('ROLE_ADMIN', 1);
define('ROLE_OPERATOR', 2);
define('ROLE_PENGURUS', 3);
define('ROLE_ANGGOTA', 4);
define('ROLE_OPERATOR_SIMPIN', 5);
define('ROLE_CUSTOMER_SERVICE', 6);
define('ROLE_SPV', 7);
define('ROLE_ASMAN', 8);
define('ROLE_MANAGER', 9);
define('ROLE_BENDAHARA', 10);
define('ROLE_KETUA', 11);

define('VERIFIKASI_PENGAJUAN_PINJAMAN', 1);
define('APPROVE_PENGAJUAN_PINJAMAN_SPV', 2);
define('APPROVE_PENGAJUAN_PINJAMAN_ASMAN', 3);
define('APPROVE_PENGAJUAN_PINJAMAN_MANAGER', 4);
define('APPROVE_PENGAJUAN_PINJAMAN_BENDAHARA', 5);
define('APPROVE_PENGAJUAN_PINJAMAN_KETUA', 6);
define('REJECT_PENGAJUAN_PINJAMAN', 9);
define('CANCEL_PENGAJUAN_PINJAMAN', 0);
define('KONFIRMASI_PEMBAYARAN_PENGAJUAN_PINJAMAN', 7);

define('COMPANY_SETTING_ADDRESS', 1);
define('COMPANY_SETTING_BANK_NAME', 2);
define('COMPANY_SETTING_BANK_ACCOUNT', 3);

define('DEFAULT_BESAR_TABUNGAN', 0);

define('JENIS_SIMPANAN_POKOK', '411.01.000');
define('JENIS_SIMPANAN_WAJIB', '411.12.000');
define('JENIS_SIMPANAN_SUKARELA', '502.01.000');
define('JENIS_SIMPANAN_KHUSUS', '409.01.000');

define('TIPE_JENIS_PINJAMAN_DANA_KOPEGMAR', 1);
define('TIPE_JENIS_PINJAMAN_DANA_LAIN', 2);

define('KATEGORI_JENIS_PINJAMAN_JANGKA_PENDEK', 1);
define('KATEGORI_JENIS_PINJAMAN_JANGKA_PANJANG', 2);

define('JENIS_ANGGOTA_BIASA', 1);
define('JENIS_ANGGOTA_LUAR_BIASA', 2);
define('JENIS_ANGGOTA_PENSIUNAN', 3);

define('COMPANY_GROUP_KOPEGMAR', 1);
define('COMPANY_GROUP_KOJA', 2);

define('STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_KONFIRMASI', 1);
define('STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_SPV', 2);
define('STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_ASMAN', 3);
define('STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_MANAGER', 4);
define('STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_BENDAHARA', 5);
define('STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_KETUA', 6);
define('STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_PEMBAYARAN', 7);
define('STATUS_PENGAJUAN_PINJAMAN_DITERIMA', 8);
define('STATUS_PENGAJUAN_PINJAMAN_DITOLAK', 9);
define('STATUS_PENGAJUAN_PINJAMAN_DIBATALKAN', 10);

define('STATUS_PINJAMAN_BELUM_LUNAS', 1);
define('STATUS_PINJAMAN_LUNAS', 2);

define('STATUS_ANGSURAN_BELUM_LUNAS', 1);
define('STATUS_ANGSURAN_LUNAS', 2);

define('JENIS_PENGHASILAN_GAJI_BULANAN', 4);

define('JENIS_PINJAM_JAPAN', '106.02');
define('JENIS_PINJAM_JAPEN', '105.01');
define('JENIS_PINJAM_KREDIT_BARANG', '106.09.002');
define('JENIS_PINJAM_KREDIT_MOTOR', '106.09.003');

define('JENIS_PENGAJUAN_PINJAMAN', 0);
define('JENIS_PENGAJUAN_TOPUP', 1);

define('STATUS_PENGAMBILAN_MENUNGGU_KONFIRMASI', 1);
define('STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_SPV', 2);
define('STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_ASMAN', 3);
define('STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_MANAGER', 4);
define('STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_BENDAHARA', 5);
define('STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_KETUA', 6);
define('STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN', 7);
define('STATUS_PENGAMBILAN_DITERIMA', 8);
define('STATUS_PENGAMBILAN_DITOLAK', 9);
define('STATUS_PENGAMBILAN_DIBATALKAN', 10);

define('INVOICE_STATUS_UNPAID', 1);
define('INVOICE_STATUS_PENDING_CONFIRMATION', 2);
define('INVOICE_STATUS_PAID', 3);
define('INVOICE_STATUS_CANCELLED', 4);

define('INVOICE_TYPE_PINJAMAN', 1);
define('INVOICE_TYPE_SIMPANAN', 1);
