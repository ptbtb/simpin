<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTPinjamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_pinjam', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pinjam', 250);
            $table->string('kode_pengajuan_pinjaman', 250)->nullable();
            $table->integer('kode_anggota')->nullable();
            $table->integer('id_akun_debet')->nullable();
            $table->integer('serial_number')->nullable();
            $table->string('kode_jenis_pinjam', 20);
            $table->double('besar_pinjam', 22, 2);
            $table->double('besar_angsuran', 22, 2);
            $table->double('besar_angsuran_pokok', 22, 2);
            $table->integer('lama_angsuran');
            $table->integer('sisa_angsuran');
            $table->double('sisa_pinjaman', 22, 2);
            $table->double('biaya_jasa', 22, 2)->default(0);
            $table->double('biaya_asuransi', 22, 2)->default(0);
            $table->double('biaya_provisi', 22, 2)->default(0);
            $table->double('biaya_administrasi', 22, 2)->default(0);
            $table->double('biaya_jasa_topup', 22, 2)->default(0);
            $table->double('diskon', 22, 2)->default(0);
            $table->double('total_diskon', 22, 2)->default(0);
            $table->string('confirmation_document', 500)->nullable();
            $table->string('u_entry', 50);
            $table->date('tgl_entri');
            $table->date('tgl_tempo')->nullable();
            $table->date('tgl_transaksi')->nullable();
            $table->string('status', 30)->nullable();
            $table->integer('id_status_pinjaman')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->text('keterangan')->nullable();
            $table->double('saldo_mutasi', 22, 0)->nullable()->default(0);
            $table->integer('id_akun_kredit')->nullable();
            $table->integer('serial_number_kredit')->nullable();
            $table->double('service_fee', 22, 0)->nullable()->default(0);
            $table->date('tgl_pelunasan')->nullable();
            $table->double('mutasi_juli')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_pinjam');
    }
}
