<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTPengajuanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_pengajuan', function (Blueprint $table) {
            $table->string('kode_pengajuan');
            $table->date('tgl_pengajuan');
            $table->integer('kode_anggota')->nullable();
            $table->integer('id_akun_debet')->nullable();
            $table->string('kode_jenis_pinjam', 10);
            $table->double('biaya_jasa_topup')->default(0);
            $table->double('biaya_jasa')->default(0);
            $table->double('biaya_provisi')->default(0);
            $table->double('biaya_administrasi')->default(0);
            $table->double('biaya_asuransi')->default(0);
            $table->string('form_persetujuan', 250)->nullable();
            $table->integer('id_status_pengajuan')->nullable();
            $table->text('keperluan')->nullable();
            $table->integer('sumber_dana')->nullable();
            $table->date('tgl_acc')->nullable();
            $table->string('no_jkk', 190)->nullable();
            $table->tinyInteger('status_jkk')->nullable()->default(0);
            $table->string('bukti_pembayaran', 190)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('approved_by')->nullable();
            $table->integer('paid_by_cashier')->nullable();
            $table->text('keterangan')->nullable();
            $table->double('transfer_simpanan_pagu', 28, 2)->nullable();
            $table->id()->autoIncrement();
            $table->date('tgl_transaksi')->nullable();
            $table->integer('tenor')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_pengajuan');
    }
}
