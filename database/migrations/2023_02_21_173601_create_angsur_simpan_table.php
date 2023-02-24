<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAngsurSimpanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_angsur_simpan', function (Blueprint $table) {
            $table->integer('kode_angsur')->autoIncrement();
            $table->integer('kode_simpan');
            $table->integer('angsuran_ke');
            $table->string('kode_anggota', 5);
            $table->string('u_entry', 50);
            $table->date('tgl_entri');
            $table->dateTime('tgl_transaksi')->nullable();
            $table->string('keterangan', 190)->nullable();
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
        Schema::dropIfExists('t_angsur_simpan');
    }
}
