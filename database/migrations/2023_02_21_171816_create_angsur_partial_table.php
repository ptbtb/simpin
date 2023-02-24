<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAngsurPartialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_angsur_partial', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('kode_angsur')->nullable();
            $table->double('besar_pembayaran')->nullable();
            $table->double('besar_angsuran')->default(0);
            $table->double('jasa')->default(0);
            $table->date('tgl_transaksi')->nullable();
            $table->integer('serial_number')->nullable();
            $table->integer('id_akun_kredit')->nullable();
            $table->integer('kode_anggota')->nullable();
            $table->string('keterangan', 190)->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('deleted_by');
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
        Schema::dropIfExists('t_angsur_partial');
    }
}
